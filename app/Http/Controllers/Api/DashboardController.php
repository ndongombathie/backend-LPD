<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if ($user->isAdmin()) {
            // Statistiques globales pour l'admin
            $stats = [
                'total_stores' => Store::count(),
                'total_products' => Product::count(),
                'total_orders' => Order::count(),
                'total_users' => User::count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'completed_orders' => Order::where('status', 'completed')->count(),
                'total_sales' => Order::where('status', 'completed')->sum('total_amount'),
                'low_stock_products' => Product::whereRaw('stock_quantity <= min_stock_level')->count(),
            ];
        } else {
            // Statistiques pour la boutique de l'utilisateur
            $storeId = $user->store_id;
            
            $stats = [
                'total_products' => Product::where('store_id', $storeId)->count(),
                'total_orders' => Order::where('store_id', $storeId)->count(),
                'pending_orders' => Order::where('store_id', $storeId)->where('status', 'pending')->count(),
                'completed_orders' => Order::where('store_id', $storeId)->where('status', 'completed')->count(),
                'total_sales' => Order::where('store_id', $storeId)->where('status', 'completed')->sum('total_amount'),
                'low_stock_products' => Product::where('store_id', $storeId)->whereRaw('stock_quantity <= min_stock_level')->count(),
            ];
        }

        return response()->json([
            'stats' => $stats
        ]);
    }

    public function sales(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Order::with(['seller', 'cashier']);

        if (!$user->isAdmin()) {
            $query->where('store_id', $user->store_id);
        }

        $dateFrom = $request->get('date_from', now()->subDays(30));
        $dateTo = $request->get('date_to', now());

        $sales = $query->whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('status', 'completed')
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total, COUNT(*) as orders_count')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'sales' => $sales
        ]);
    }

    public function products(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Product::with(['category', 'store']);

        if (!$user->isAdmin()) {
            $query->where('store_id', $user->store_id);
        }

        $products = $query->selectRaw('
            products.*,
            (SELECT SUM(quantity) FROM order_items 
             JOIN orders ON order_items.order_id = orders.id 
             WHERE order_items.product_id = products.id 
             AND orders.status = "completed") as total_sold
        ')->orderBy('total_sold', 'desc')->limit(10)->get();

        return response()->json([
            'products' => $products
        ]);
    }

    public function orders(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Order::with(['seller', 'cashier', 'orderItems.product']);

        if (!$user->isAdmin()) {
            $query->where('store_id', $user->store_id);
        }

        $orders = $query->orderBy('created_at', 'desc')->limit(10)->get();

        return response()->json([
            'orders' => $orders
        ]);
    }
}
