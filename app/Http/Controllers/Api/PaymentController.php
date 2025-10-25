<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Payment::with(['order', 'cashier', 'store']);

        if (!$user->isAdmin()) {
            $query->where('store_id', $user->store_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'payments' => $payments
        ]);
    }

    public function show(Request $request, Payment $payment): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isAdmin() && $user->store_id !== $payment->store_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $payment->load(['order.orderItems.product', 'cashier', 'store']);

        return response()->json([
            'payment' => $payment
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isCashier()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,mobile_money,bank_transfer',
            'transaction_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        $order = Order::findOrFail($request->order_id);
        
        if ($order->store_id !== $user->store_id) {
            return response()->json(['message' => 'Order does not belong to your store'], 403);
        }

        if ($order->status !== 'pending') {
            return response()->json(['message' => 'Order is not pending'], 400);
        }

        DB::beginTransaction();

        try {
            $payment = Payment::create([
                'order_id' => $order->id,
                'store_id' => $user->store_id,
                'cashier_id' => $user->id,
                'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'transaction_reference' => $request->transaction_reference,
                'status' => 'pending',
                'notes' => $request->notes
            ]);

            // Mettre à jour le statut de la commande
            $order->status = 'processing';
            $order->cashier_id = $user->id;
            $order->save();

            DB::commit();

            // Broadcast payment created event
            broadcast(new \App\Events\PaymentCreated($payment))->toOthers();

            return response()->json([
                'payment' => $payment->load(['order', 'cashier'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function complete(Request $request, Payment $payment): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isCashier()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($user->store_id !== $payment->store_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($payment->status !== 'pending') {
            return response()->json(['message' => 'Payment is not pending'], 400);
        }

        DB::beginTransaction();

        try {
            $payment->status = 'completed';
            $payment->paid_at = now();
            $payment->save();

            // Mettre à jour le statut de la commande
            $order = $payment->order;
            $order->status = 'completed';
            $order->payment_status = 'paid';
            $order->completed_at = now();
            $order->save();

            DB::commit();

            // Broadcast payment completed event
            broadcast(new \App\Events\PaymentCompleted($payment))->toOthers();

            return response()->json([
                'payment' => $payment->load(['order', 'cashier']),
                'message' => 'Payment completed successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function update(Request $request, Payment $payment): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isAdmin() && $user->store_id !== $payment->store_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'amount' => 'sometimes|required|numeric|min:0',
            'payment_method' => 'sometimes|required|in:cash,card,mobile_money,bank_transfer',
            'transaction_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'sometimes|in:pending,completed,failed,refunded'
        ]);

        $payment->update($request->all());

        return response()->json([
            'payment' => $payment->load(['order', 'cashier'])
        ]);
    }

    public function destroy(Request $request, Payment $payment): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isAdmin() && $user->store_id !== $payment->store_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($payment->status === 'completed') {
            return response()->json(['message' => 'Cannot delete completed payment'], 400);
        }

        $payment->delete();

        return response()->json([
            'message' => 'Payment deleted successfully'
        ]);
    }
}
