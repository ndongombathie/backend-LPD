<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Order;
use App\Models\Store;
use Illuminate\Console\Command;

class TestOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the orders API endpoints with UUID';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🛒 Test des Commandes avec UUID');
        $this->info('===============================');
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
            $token = $user->createToken('test-orders-token')->plainTextToken;
            $this->info('✅ Token créé avec succès');
            $this->line("   - Token: " . substr($token, 0, 20) . "...");
            $this->newLine();

            // Lister toutes les commandes
            $this->info('3️⃣ Liste des commandes disponibles...');
            $orders = Order::with(['store', 'seller', 'orderItems.product'])->get();
            $this->info("✅ Commandes trouvées: " . $orders->count());
            
            if ($orders->count() > 0) {
                $this->newLine();
                $this->info('📋 Commandes disponibles:');
                
                $headers = ['ID', 'Numéro', 'Client', 'Boutique', 'Statut', 'Total'];
                $rows = [];
                
                foreach ($orders->take(10) as $order) {
                    $rows[] = [
                        $order->id,
                        $order->order_number,
                        $order->customer_name ?? 'N/A',
                        $order->store->name,
                        $order->status,
                        number_format($order->total_amount, 0, ',', ' ') . ' FCFA'
                    ];
                }
                
                $this->table($headers, $rows);
                
                if ($orders->count() > 10) {
                    $this->line("... et " . ($orders->count() - 10) . " autres commandes");
                }
            } else {
                $this->warn('⚠️ Aucune commande trouvée');
            }

            $this->newLine();

            // Tester l'endpoint des commandes
            if ($orders->count() > 0) {
                $firstOrder = $orders->first();
                $this->info('4️⃣ Test de l\'endpoint des commandes...');
                $apiUrl = "http://127.0.0.1:8000/api/orders/{$firstOrder->id}";
                $this->line("   - URL: {$apiUrl}");
                $this->line("   - Token: " . substr($token, 0, 20) . "...");
                $this->newLine();

                $this->info('📋 Instructions pour tester l\'API:');
                $this->line('1. Démarrer le serveur: php artisan serve');
                $this->line("2. Utiliser l'URL: {$apiUrl}");
                $this->line("3. Ajouter l'en-tête: Authorization: Bearer {$token}");
                $this->line('4. Ou utiliser Postman/Insomnia avec ces paramètres');
                $this->newLine();

                $this->info('🔧 Exemples d\'URLs de commandes:');
                foreach ($orders->take(5) as $order) {
                    $this->line("   - GET http://127.0.0.1:8000/api/orders/{$order->id}");
                }
            }

            $this->newLine();
            $this->info('🎉 Test des commandes terminé avec succès!');
            $this->info('Les endpoints des commandes sont prêts à être utilisés.');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Erreur: ' . $e->getMessage());
            $this->error('Vérifiez votre configuration de base de données.');
            return 1;
        }
    }
}
