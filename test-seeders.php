<?php

/**
 * Script de test pour vérifier les seeders
 * Usage: php test-seeders.php
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

echo "🧪 Test des Seeders - Système de Gestion de Stock\n";
echo "================================================\n\n";

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

    // Vérifier les données créées
    echo "4️⃣ Vérification des données créées...\n";
    
    $stores = DB::table('stores')->count();
    $categories = DB::table('categories')->count();
    $users = DB::table('users')->count();
    $products = DB::table('products')->count();
    $orders = DB::table('orders')->count();
    $payments = DB::table('payments')->count();

    echo "📊 Résultats:\n";
    echo "   - Boutiques: {$stores}\n";
    echo "   - Catégories: {$categories}\n";
    echo "   - Utilisateurs: {$users}\n";
    echo "   - Produits: {$products}\n";
    echo "   - Commandes: {$orders}\n";
    echo "   - Paiements: {$payments}\n\n";

    // Vérifier les relations
    echo "5️⃣ Vérification des relations...\n";
    
    $productsWithStore = DB::table('products')
        ->join('stores', 'products.store_id', '=', 'stores.id')
        ->count();
    
    $productsWithCategory = DB::table('products')
        ->join('categories', 'products.category_id', '=', 'categories.id')
        ->count();
    
    $ordersWithStore = DB::table('orders')
        ->join('stores', 'orders.store_id', '=', 'stores.id')
        ->count();

    echo "✅ Relations vérifiées:\n";
    echo "   - Produits avec boutique: {$productsWithStore}\n";
    echo "   - Produits avec catégorie: {$productsWithCategory}\n";
    echo "   - Commandes avec boutique: {$ordersWithStore}\n\n";

    // Statistiques détaillées
    echo "6️⃣ Statistiques détaillées...\n";
    
    // Produits par boutique
    $productsByStore = DB::table('products')
        ->join('stores', 'products.store_id', '=', 'stores.id')
        ->select('stores.name', DB::raw('COUNT(products.id) as product_count'))
        ->groupBy('stores.id', 'stores.name')
        ->get();

    echo "📦 Produits par boutique:\n";
    foreach ($productsByStore as $store) {
        echo "   - {$store->name}: {$store->product_count} produits\n";
    }

    // Produits par catégorie
    $productsByCategory = DB::table('products')
        ->join('categories', 'products.category_id', '=', 'categories.id')
        ->select('categories.name', DB::raw('COUNT(products.id) as product_count'))
        ->groupBy('categories.id', 'categories.name')
        ->get();

    echo "\n📂 Produits par catégorie:\n";
    foreach ($productsByCategory as $category) {
        echo "   - {$category->name}: {$category->product_count} produits\n";
    }

    // Commandes par statut
    $ordersByStatus = DB::table('orders')
        ->select('status', DB::raw('COUNT(*) as count'))
        ->groupBy('status')
        ->get();

    echo "\n🛒 Commandes par statut:\n";
    foreach ($ordersByStatus as $status) {
        echo "   - {$status->status}: {$status->count} commandes\n";
    }

    // Paiements par méthode
    $paymentsByMethod = DB::table('payments')
        ->select('payment_method', DB::raw('COUNT(*) as count'))
        ->groupBy('payment_method')
        ->get();

    echo "\n💳 Paiements par méthode:\n";
    foreach ($paymentsByMethod as $method) {
        echo "   - {$method->payment_method}: {$method->count} paiements\n";
    }

    echo "\n🎉 Test terminé avec succès!\n";
    echo "Le système est prêt à être utilisé.\n\n";

    echo "📋 Prochaines étapes:\n";
    echo "1. Démarrer le serveur: php artisan serve\n";
    echo "2. Tester l'API avec Postman ou curl\n";
    echo "3. Ouvrir realtime-test.html pour tester les notifications\n";
    echo "4. Utiliser les comptes de test créés\n\n";

    echo "👥 Comptes de test créés:\n";
    echo "   - Admin: admin@stockmanagement.com / password\n";
    echo "   - Vendeurs: seller@[boutique].com / password\n";
    echo "   - Caissiers: cashier@[boutique].com / password\n";
    echo "   - Gestionnaires: manager@[boutique].com / password\n";

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Vérifiez votre configuration de base de données.\n";
    exit(1);
}
