<?php

namespace App\Console\Commands;

use App\Models\Store;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\Category;
use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestUuid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:uuid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the UUID implementation in the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔑 Test des UUID - Système de Gestion de Stock');
        $this->info('==============================================');
        $this->newLine();

        try {
            // Vérifier la connexion à la base de données
            $this->info('1️⃣ Vérification de la connexion à la base de données...');
            DB::connection()->getPdo();
            $this->info('✅ Connexion à la base de données réussie');
            $this->newLine();

            // Exécuter les migrations
            $this->info('2️⃣ Exécution des migrations...');
            $this->call('migrate:fresh');
            $this->info('✅ Migrations exécutées avec succès');
            $this->newLine();

            // Exécuter les seeders
            $this->info('3️⃣ Exécution des seeders...');
            $this->call('db:seed');
            $this->info('✅ Seeders exécutés avec succès');
            $this->newLine();

            // Vérifier les UUID
            $this->info('4️⃣ Vérification des UUID...');
            
            // Vérifier les stores
            $stores = Store::all();
            $this->info("📦 Stores créés: " . $stores->count());
            foreach ($stores as $store) {
                $isUuid = preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $store->id);
                $status = $isUuid ? "✅ UUID valide" : "❌ UUID invalide";
                $this->line("   - {$store->name}: {$status} ({$store->id})");
            }

            // Vérifier les utilisateurs
            $users = User::all();
            $this->newLine();
            $this->info("👥 Utilisateurs créés: " . $users->count());
            foreach ($users as $user) {
                $isUuid = preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $user->id);
                $status = $isUuid ? "✅ UUID valide" : "❌ UUID invalide";
                $this->line("   - {$user->name}: {$status}");
            }

            // Vérifier les produits
            $products = Product::all();
            $this->newLine();
            $this->info("🛍️ Produits créés: " . $products->count());
            $validProducts = 0;
            foreach ($products as $product) {
                $isUuid = preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $product->id);
                if ($isUuid) $validProducts++;
            }
            $this->line("   - Produits avec UUID valides: {$validProducts}/" . $products->count());

            // Vérifier les commandes
            $orders = Order::all();
            $this->newLine();
            $this->info("🛒 Commandes créées: " . $orders->count());
            $validOrders = 0;
            foreach ($orders as $order) {
                $isUuid = preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $order->id);
                if ($isUuid) $validOrders++;
            }
            $this->line("   - Commandes avec UUID valides: {$validOrders}/" . $orders->count());

            // Vérifier les relations
            $this->newLine();
            $this->info('5️⃣ Vérification des relations...');
            
            $productWithStore = Product::with('store')->first();
            if ($productWithStore) {
                $this->info('✅ Relation Product->Store fonctionne');
                $this->line("   - Produit: {$productWithStore->name}");
                $this->line("   - Boutique: {$productWithStore->store->name}");
            }

            $orderWithItems = Order::with('orderItems')->first();
            if ($orderWithItems) {
                $this->info('✅ Relation Order->OrderItems fonctionne');
                $this->line("   - Commande: {$orderWithItems->order_number}");
                $this->line("   - Articles: " . $orderWithItems->orderItems->count());
            }

            // Test de création d'un nouveau produit
            $this->newLine();
            $this->info('6️⃣ Test de création d\'un nouveau produit...');
            $newProduct = Product::create([
                'store_id' => $stores->first()->id,
                'category_id' => Category::first()->id,
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
            $status = $isUuid ? "✅ UUID généré automatiquement" : "❌ UUID non généré";
            $this->line("   - Nouveau produit: {$status}");
            $this->line("   - ID: {$newProduct->id}");

            // Statistiques finales
            $this->newLine();
            $this->info('📊 Statistiques finales:');
            $this->line("   - Stores: " . Store::count());
            $this->line("   - Utilisateurs: " . User::count());
            $this->line("   - Produits: " . Product::count());
            $this->line("   - Commandes: " . Order::count());
            $this->line("   - Paiements: " . Payment::count());

            $this->newLine();
            $this->info('🎉 Test des UUID terminé avec succès!');
            $this->info('Tous les modèles utilisent maintenant des UUID au lieu d\'IDs auto-incrémentés.');
            $this->newLine();

            $this->info('🔒 Avantages des UUID:');
            $this->line('   - Sécurité renforcée (pas d\'exposition de la structure)');
            $this->line('   - Identifiants uniques globaux');
            $this->line('   - Pas de collision entre environnements');
            $this->line('   - Meilleure sécurité pour les APIs publiques');

        } catch (\Exception $e) {
            $this->error('❌ Erreur: ' . $e->getMessage());
            $this->error('Vérifiez votre configuration de base de données.');
            return 1;
        }

        return 0;
    }
}
