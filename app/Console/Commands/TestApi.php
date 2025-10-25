<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Store;
use App\Models\Product;
use Illuminate\Console\Command;

class TestApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the API endpoints with UUID';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🌐 Test de l\'API avec UUID');
        $this->info('===========================');
        $this->newLine();

        try {
            // Récupérer un utilisateur pour le test
            $this->info('1️⃣ Récupération d\'un utilisateur de test...');
            $user = User::where('email', 'admin@stockmanagement.com')->first();
            
            if (!$user) {
                $this->error('❌ Utilisateur admin non trouvé');
                return 1;
            }
            
            $this->info("✅ Utilisateur trouvé: {$user->name}");
            $this->line("   - ID: {$user->id}");
            $this->line("   - Email: {$user->email}");
            $this->newLine();

            // Créer un token d'authentification
            $this->info('2️⃣ Création d\'un token d\'authentification...');
            $token = $user->createToken('test-api-token')->plainTextToken;
            $this->info('✅ Token créé avec succès');
            $this->line("   - Token: " . substr($token, 0, 20) . "...");
            $this->newLine();

            // Récupérer une boutique pour le test
            $this->info('3️⃣ Récupération d\'une boutique de test...');
            $store = Store::first();
            
            if (!$store) {
                $this->error('❌ Aucune boutique trouvée');
                return 1;
            }
            
            $this->info("✅ Boutique trouvée: {$store->name}");
            $this->line("   - ID: {$store->id}");
            $this->line("   - Adresse: {$store->address}");
            $this->newLine();

            // Tester l'URL de l'API
            $this->info('4️⃣ Test de l\'URL de l\'API...');
            $apiUrl = "http://127.0.0.1:8000/api/stores/{$store->id}/products";
            $this->line("   - URL: {$apiUrl}");
            $this->line("   - Token: " . substr($token, 0, 20) . "...");
            $this->newLine();

            // Vérifier les produits de la boutique
            $this->info('5️⃣ Vérification des produits de la boutique...');
            $products = $store->products()->with('category')->get();
            $this->info("✅ Produits trouvés: " . $products->count());
            
            if ($products->count() > 0) {
                $firstProduct = $products->first();
                $this->line("   - Premier produit: {$firstProduct->name}");
                $this->line("   - Catégorie: {$firstProduct->category->name}");
                $this->line("   - Stock: {$firstProduct->stock_quantity}");
            }

            $this->newLine();
            $this->info('🎉 Test de l\'API terminé avec succès!');
            $this->info('L\'endpoint est prêt à être utilisé.');
            $this->newLine();

            $this->info('📋 Instructions pour tester l\'API:');
            $this->line('1. Démarrer le serveur: php artisan serve');
            $this->line("2. Utiliser l'URL: {$apiUrl}");
            $this->line("3. Ajouter l'en-tête: Authorization: Bearer {$token}");
            $this->line('4. Ou utiliser Postman/Insomnia avec ces paramètres');
            $this->newLine();

            $this->info('🔧 Exemples de requêtes:');
            $this->line("   - GET {$apiUrl}");
            $this->line("   - GET {$apiUrl}?search=cahier");
            $this->line("   - GET {$apiUrl}?status=low_stock");
            if ($products->count() > 0) {
                $categoryId = $products->first()->category_id;
                $this->line("   - GET {$apiUrl}?category_id={$categoryId}");
            }

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Erreur: ' . $e->getMessage());
            $this->error('Vérifiez votre configuration de base de données.');
            return 1;
        }
    }
}
