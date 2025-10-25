<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Store;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier que les stores et categories existent
        if (Store::count() === 0) {
            $this->command->warn('Aucune boutique trouvée. Exécutez d\'abord StoreSeeder.');
            return;
        }

        if (Category::count() === 0) {
            $this->command->warn('Aucune catégorie trouvée. Exécutez d\'abord CategorySeeder.');
            return;
        }

        $this->command->info('🌱 Création des produits...');

        // Créer des produits pour chaque boutique
        $stores = Store::all();
        $categories = Category::all();

        foreach ($stores as $store) {
            $this->command->info("📦 Création des produits pour {$store->name}...");

            // Créer 20-30 produits par boutique
            $productCount = rand(20, 30);
            
            for ($i = 0; $i < $productCount; $i++) {
                // Créer un produit normal
                Product::factory()->create([
                    'store_id' => $store->id,
                ]);
            }

            // Ajouter quelques produits avec stock faible
            $lowStockCount = rand(3, 8);
            for ($i = 0; $i < $lowStockCount; $i++) {
                Product::factory()->lowStock()->create([
                    'store_id' => $store->id,
                ]);
            }

            // Ajouter quelques produits en rupture de stock
            $outOfStockCount = rand(2, 5);
            for ($i = 0; $i < $outOfStockCount; $i++) {
                Product::factory()->outOfStock()->create([
                    'store_id' => $store->id,
                ]);
            }

            // Ajouter quelques produits avec stock élevé
            $highStockCount = rand(5, 10);
            for ($i = 0; $i < $highStockCount; $i++) {
                Product::factory()->highStock()->create([
                    'store_id' => $store->id,
                ]);
            }

            // Ajouter quelques produits inactifs
            $inactiveCount = rand(2, 5);
            for ($i = 0; $i < $inactiveCount; $i++) {
                Product::factory()->inactive()->create([
                    'store_id' => $store->id,
                ]);
            }
        }

        $totalProducts = Product::count();
        $this->command->info("✅ {$totalProducts} produits créés avec succès!");

        // Afficher les statistiques
        $this->displayStatistics();
    }

    /**
     * Afficher les statistiques des produits créés
     */
    private function displayStatistics(): void
    {
        $this->command->info("\n📊 Statistiques des produits:");
        
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $inactiveProducts = Product::where('is_active', false)->count();
        $lowStockProducts = Product::whereRaw('stock_quantity <= min_stock_level')->count();
        $outOfStockProducts = Product::where('stock_quantity', 0)->count();

        $this->command->table(
            ['Statistique', 'Nombre'],
            [
                ['Total des produits', $totalProducts],
                ['Produits actifs', $activeProducts],
                ['Produits inactifs', $inactiveProducts],
                ['Produits en stock faible', $lowStockProducts],
                ['Produits en rupture', $outOfStockProducts],
            ]
        );

        // Statistiques par boutique
        $this->command->info("\n🏪 Produits par boutique:");
        $stores = Store::withCount('products')->get();
        
        $storeData = $stores->map(function ($store) {
            return [
                'Boutique' => $store->name,
                'Produits' => $store->products_count,
            ];
        });

        $this->command->table(
            ['Boutique', 'Nombre de produits'],
            $storeData->toArray()
        );

        // Statistiques par catégorie
        $this->command->info("\n📂 Produits par catégorie:");
        $categories = Category::withCount('products')->get();
        
        $categoryData = $categories->map(function ($category) {
            return [
                'Catégorie' => $category->name,
                'Produits' => $category->products_count,
            ];
        });

        $this->command->table(
            ['Catégorie', 'Nombre de produits'],
            $categoryData->toArray()
        );
    }
}
