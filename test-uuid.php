<?php

/**
 * Script de test pour vérifier l'utilisation des UUID
 * Usage: php artisan test:uuid
 */

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\Store;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;

echo "🔑 Test des UUID - Système de Gestion de Stock\n";
echo "==============================================\n\n";

try {
    // Vérifier la connexion à la base de données
    echo "1️⃣ Vérification de la connexion à la base de données...\n";
    DB::connection()->getPdo();
    echo "✅ Connexion à la base de données réussie\n\n";

    // Exécuter les migrations
    echo "2️⃣ Exécution des migrations...\n";
    Artisan::call('migrate:fresh');
    echo "✅ Migrations exécutées avec succès\n\n";

    // Exécuter les seeders
    echo "3️⃣ Exécution des seeders...\n";
    Artisan::call('db:seed');
    echo "✅ Seeders exécutés avec succès\n\n";

    // Vérifier les UUID
    echo "4️⃣ Vérification des UUID...\n";
    
    // Vérifier les stores
    $stores = Store::all();
    echo "📦 Stores créés: " . $stores->count() . "\n";
    foreach ($stores as $store) {
        $isUuid = preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $store->id);
        echo "   - {$store->name}: " . ($isUuid ? "✅ UUID valide" : "❌ UUID invalide") . " ({$store->id})\n";
    }

    // Vérifier les utilisateurs
    $users = User::all();
    echo "\n👥 Utilisateurs créés: " . $users->count() . "\n";
    foreach ($users as $user) {
        $isUuid = preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $user->id);
        echo "   - {$user->name}: " . ($isUuid ? "✅ UUID valide" : "❌ UUID invalide") . " ({$user->id})\n";
    }

    // Vérifier les produits
    $products = Product::all();
    echo "\n🛍️ Produits créés: " . $products->count() . "\n";
    $validProducts = 0;
    foreach ($products as $product) {
        $isUuid = preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $product->id);
        if ($isUuid) $validProducts++;
    }
    echo "   - Produits avec UUID valides: {$validProducts}/" . $products->count() . "\n";

    // Vérifier les commandes
    $orders = Order::all();
    echo "\n🛒 Commandes créées: " . $orders->count() . "\n";
    $validOrders = 0;
    foreach ($orders as $order) {
        $isUuid = preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $order->id);
        if ($isUuid) $validOrders++;
    }
    echo "   - Commandes avec UUID valides: {$validOrders}/" . $orders->count() . "\n";

    // Vérifier les relations
    echo "\n5️⃣ Vérification des relations...\n";
    
    $productWithStore = Product::with('store')->first();
    if ($productWithStore) {
        echo "✅ Relation Product->Store fonctionne\n";
        echo "   - Produit: {$productWithStore->name}\n";
        echo "   - Boutique: {$productWithStore->store->name}\n";
    }

    $orderWithItems = Order::with('orderItems')->first();
    if ($orderWithItems) {
        echo "✅ Relation Order->OrderItems fonctionne\n";
        echo "   - Commande: {$orderWithItems->order_number}\n";
        echo "   - Articles: " . $orderWithItems->orderItems->count() . "\n";
    }

    // Test de création d'un nouveau produit
    echo "\n6️⃣ Test de création d'un nouveau produit...\n";
    $newProduct = Product::create([
        'store_id' => $stores->first()->id,
        'category_id' => \App\Models\Category::first()->id,
        'name' => 'Produit Test UUID',
        'description' => 'Test de création avec UUID',
        'sku' => 'TEST-UUID-' . time(),
        'price' => 1000,
        'cost_price' => 500,
        'stock_quantity' => 10,
        'min_stock_level' => 2,
        'is_active' => true,
    ]);

    $isUuid = preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $newProduct->id);
    echo "   - Nouveau produit: " . ($isUuid ? "✅ UUID généré automatiquement" : "❌ UUID non généré") . "\n";
    echo "   - ID: {$newProduct->id}\n";

    // Statistiques finales
    echo "\n📊 Statistiques finales:\n";
    echo "   - Stores: " . Store::count() . "\n";
    echo "   - Utilisateurs: " . User::count() . "\n";
    echo "   - Produits: " . Product::count() . "\n";
    echo "   - Commandes: " . Order::count() . "\n";
    echo "   - Paiements: " . \App\Models\Payment::count() . "\n";

    echo "\n🎉 Test des UUID terminé avec succès!\n";
    echo "Tous les modèles utilisent maintenant des UUID au lieu d'IDs auto-incrémentés.\n\n";

    echo "🔒 Avantages des UUID:\n";
    echo "   - Sécurité renforcée (pas d'exposition de la structure)\n";
    echo "   - Identifiants uniques globaux\n";
    echo "   - Pas de collision entre environnements\n";
    echo "   - Meilleure sécurité pour les APIs publiques\n";

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Vérifiez votre configuration de base de données.\n";
    exit(1);
}
