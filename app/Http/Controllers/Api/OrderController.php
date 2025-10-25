<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Order::with(['orderItems.product', 'seller', 'cashier', 'store']);

        if (!$user->isAdmin()) {
            $query->where('store_id', $user->store_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'orders' => $orders
        ]);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isAdmin() && $user->store_id !== $order->store_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $order->load(['orderItems.product', 'seller', 'cashier', 'store', 'payments']);

        return response()->json([
            'order' => $order
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isSeller()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            $order = new Order();
            $order->store_id = $user->store_id;
            $order->seller_id = $user->id;
            $order->order_number = 'ORD-' . strtoupper(Str::random(8));
            $order->customer_name = $request->customer_name;
            $order->customer_phone = $request->customer_phone;
            $order->tax_amount = $request->tax_amount ?? 0;
            $order->discount_amount = $request->discount_amount ?? 0;
            $order->notes = $request->notes;
            $order->status = 'pending';
            $order->payment_status = 'pending';

            $subtotal = 0;
            $orderItems = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                if ($product->store_id !== $user->store_id) {
                    throw new \Exception('Product does not belong to your store');
                }

                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                $unitPrice = $product->price;
                $totalPrice = $unitPrice * $item['quantity'];
                $subtotal += $totalPrice;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice
                ];
            }

            $order->subtotal = $subtotal;
            $order->total_amount = $subtotal + $order->tax_amount - $order->discount_amount;
            $order->save();

            foreach ($orderItems as $item) {
                $orderItem = new OrderItem($item);
                $orderItem->order_id = $order->id;
                $orderItem->save();

                // Update product stock
                $product = Product::find($item['product_id']);
                $product->stock_quantity -= $item['quantity'];
                $product->save();
            }

            DB::commit();

            // Broadcast order created event for real-time updates
            broadcast(new \App\Events\OrderCreated($order))->toOthers();

            return response()->json([
                'order' => $order->load(['orderItems.product', 'seller'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function update(Request $request, Order $order): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isAdmin() && $user->store_id !== $order->store_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($order->status === 'completed') {
            return response()->json(['message' => 'Cannot update completed order'], 400);
        }

        $request->validate([
            'status' => 'sometimes|in:pending,processing,completed,cancelled',
            'customer_name' => 'sometimes|nullable|string|max:255',
            'customer_phone' => 'sometimes|nullable|string|max:20',
            'notes' => 'sometimes|nullable|string'
        ]);

        $order->update($request->all());

        if ($request->has('status') && $request->status === 'completed') {
            $order->completed_at = now();
            $order->save();
        }

        // Broadcast order updated event
        broadcast(new \App\Events\OrderUpdated($order))->toOthers();

        return response()->json([
            'order' => $order->load(['orderItems.product', 'seller', 'cashier'])
        ]);
    }

    public function cancel(Request $request, Order $order): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isAdmin() && $user->store_id !== $order->store_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($order->status === 'completed') {
            return response()->json(['message' => 'Cannot cancel completed order'], 400);
        }

        DB::beginTransaction();

        try {
            // Restore stock
            foreach ($order->orderItems as $item) {
                $product = $item->product;
                $product->stock_quantity += $item->quantity;
                $product->save();
            }

            $order->status = 'cancelled';
            $order->save();

            DB::commit();

            // Broadcast order cancelled event
            broadcast(new \App\Events\OrderCancelled($order))->toOthers();

            return response()->json([
                'order' => $order->load(['orderItems.product', 'seller'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function pendingOrders(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isCashier()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $orders = Order::where('store_id', $user->store_id)
            ->where('status', 'pending')
            ->with(['orderItems.product', 'seller'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'orders' => $orders
        ]);
    }
}
