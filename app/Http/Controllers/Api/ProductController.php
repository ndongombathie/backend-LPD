<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Product::with(['category', 'store']);

        if (!$user->isAdmin()) {
            $query->where('store_id', $user->store_id);
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
        }

        if ($request->has('low_stock') && $request->low_stock) {
            $query->whereRaw('stock_quantity <= min_stock_level');
        }

        $products = $query->paginate(20);

        return response()->json([
            'products' => $products
        ]);
    }

    public function show(Request $request, Product $product): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isAdmin() && $user->store_id !== $product->store_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $product->load(['category', 'store', 'orderItems.order']);

        return response()->json([
            'product' => $product
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isAdmin() && !$user->isStoreManager()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'required|string|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();
        $data['store_id'] = $user->store_id;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        return response()->json([
            'product' => $product->load('category')
        ], 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isAdmin() && $user->store_id !== $product->store_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$user->isAdmin() && !$user->isStoreManager()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'sometimes|required|string|unique:products,sku,' . $product->id,
            'price' => 'sometimes|required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'sometimes|required|integer|min:0',
            'min_stock_level' => 'sometimes|required|integer|min:0',
            'category_id' => 'sometimes|required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'sometimes|boolean'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return response()->json([
            'product' => $product->load('category')
        ]);
    }

    public function destroy(Request $request, Product $product): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isAdmin() && $user->store_id !== $product->store_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$user->isAdmin() && !$user->isStoreManager()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }

    public function updateStock(Request $request, Product $product): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isAdmin() && $user->store_id !== $product->store_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'quantity' => 'required|integer',
            'operation' => 'required|in:add,subtract,set'
        ]);

        $quantity = $request->quantity;
        $operation = $request->operation;

        switch ($operation) {
            case 'add':
                $product->stock_quantity += $quantity;
                break;
            case 'subtract':
                $product->stock_quantity = max(0, $product->stock_quantity - $quantity);
                break;
            case 'set':
                $product->stock_quantity = $quantity;
                break;
        }

        $product->save();

        return response()->json([
            'product' => $product->load('category')
        ]);
    }
}
