<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StoreController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if ($user->isAdmin()) {
            $stores = Store::with(['users', 'products'])->get();
        } else {
            $stores = Store::where('id', $user->store_id)
                ->with(['users', 'products'])
                ->get();
        }

        return response()->json([
            'stores' => $stores
        ]);
    }

    public function show(Request $request, Store $store): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->isAdmin() && $user->store_id !== $store->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $store->load(['users', 'products.category']);

        return response()->json([
            'store' => $store
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string'
        ]);

        $store = Store::create($request->all());

        return response()->json([
            'store' => $store
        ], 201);
    }

    public function update(Request $request, Store $store): JsonResponse
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean'
        ]);

        $store->update($request->all());

        return response()->json([
            'store' => $store
        ]);
    }

    public function destroy(Request $request, Store $store): JsonResponse
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $store->delete();

        return response()->json([
            'message' => 'Store deleted successfully'
        ]);
    }

    /**
     * Récupérer les produits d'une boutique
     */
    public function products(Request $request, Store $store): JsonResponse
    {
        $user = $request->user();
        
        // Vérifier les permissions
        if (!$user->isAdmin() && $user->store_id !== $store->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Récupérer les paramètres de filtrage
        $categoryId = $request->query('category_id');
        $search = $request->query('search');
        $status = $request->query('status'); // active, inactive, low_stock, out_of_stock
        $perPage = $request->query('per_page', 15);

        $query = $store->products()->with('category');

        // Filtrer par catégorie
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Recherche par nom ou SKU
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtrer par statut
        if ($status) {
            switch ($status) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'low_stock':
                    $query->whereRaw('stock_quantity <= min_stock_level');
                    break;
                case 'out_of_stock':
                    $query->where('stock_quantity', 0);
                    break;
            }
        }

        // Trier par nom par défaut
        $query->orderBy('name');

        $products = $query->paginate($perPage);

        return response()->json([
            'products' => $products,
            'store' => [
                'id' => $store->id,
                'name' => $store->name,
                'address' => $store->address
            ],
            'filters' => [
                'category_id' => $categoryId,
                'search' => $search,
                'status' => $status
            ]
        ]);
    }
}
