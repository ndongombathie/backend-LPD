<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier que les données nécessaires existent
        if (Store::count() === 0 || Product::count() === 0 || User::count() === 0) {
            $this->command->warn('Données manquantes. Exécutez d\'abord les autres seeders.');
            return;
        }

        $this->command->info('🛒 Création des commandes et paiements...');

        $stores = Store::all();
        $totalOrders = 0;

        foreach ($stores as $store) {
            $this->command->info("📦 Création des commandes pour {$store->name}...");

            // Obtenir les vendeurs et caissiers de cette boutique
            $sellers = User::where('store_id', $store->id)->where('role', 'seller')->get();
            $cashiers = User::where('store_id', $store->id)->where('role', 'cashier')->get();
            $products = Product::where('store_id', $store->id)->where('is_active', true)->get();

            if ($sellers->isEmpty() || $cashiers->isEmpty() || $products->isEmpty()) {
                $this->command->warn("Données insuffisantes pour {$store->name}. Passer...");
                continue;
            }

            // Créer 15-25 commandes par boutique
            $orderCount = rand(15, 25);
            
            for ($i = 0; $i < $orderCount; $i++) {
                DB::beginTransaction();
                
                try {
                    // Créer une commande
                    $order = $this->createOrder($store, $sellers, $products);
                    
                    // Créer des articles de commande
                    $this->createOrderItems($order, $products);
                    
                    // Mettre à jour le total de la commande
                    $this->updateOrderTotal($order);
                    
                    // Créer un paiement pour certaines commandes
                    if (rand(1, 10) <= 8) { // 80% de chance d'avoir un paiement
                        $this->createPayment($order, $cashiers);
                    }
                    
                    DB::commit();
                    $totalOrders++;
                    
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->command->error("Erreur lors de la création de la commande: " . $e->getMessage());
                }
            }
        }

        $this->command->info("✅ {$totalOrders} commandes créées avec succès!");
        $this->displayStatistics();
    }

    /**
     * Créer une commande
     */
    private function createOrder($store, $sellers, $products): Order
    {
        $seller = $sellers->random();
        $customerNames = [
            'Jean Dupont', 'Marie Martin', 'Pierre Durand', 'Sophie Bernard',
            'Michel Leroy', 'Catherine Moreau', 'Philippe Petit', 'Isabelle Roux',
            'Alain Simon', 'Françoise Laurent', 'Robert Michel', 'Nathalie Garcia'
        ];

        $order = Order::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'store_id' => $store->id,
            'seller_id' => $seller->id,
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'customer_name' => fake()->randomElement($customerNames),
            'customer_phone' => '+237 6' . fake()->numberBetween(10, 99) . ' ' . fake()->numberBetween(10, 99) . ' ' . fake()->numberBetween(10, 99) . ' ' . fake()->numberBetween(10, 99),
            'subtotal' => 0, // Sera calculé plus tard
            'tax_amount' => fake()->numberBetween(0, 500),
            'discount_amount' => fake()->numberBetween(0, 1000),
            'total_amount' => 0, // Sera calculé plus tard
            'status' => fake()->randomElement(['pending', 'processing', 'completed']),
            'payment_status' => fake()->randomElement(['pending', 'paid']),
            'notes' => fake()->optional(0.3)->sentence(),
        ]);

        return $order;
    }

    /**
     * Créer les articles de commande
     */
    private function createOrderItems($order, $products): void
    {
        $itemCount = rand(1, 5); // 1 à 5 articles par commande
        $selectedProducts = $products->random($itemCount);

        foreach ($selectedProducts as $product) {
            $quantity = rand(1, 3);
            $unitPrice = $product->price;
            $totalPrice = $unitPrice * $quantity;

            OrderItem::create([
                'id' => \Illuminate\Support\Str::uuid(),
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
            ]);

            // Mettre à jour le stock du produit
            $product->stock_quantity = max(0, $product->stock_quantity - $quantity);
            $product->save();
        }
    }

    /**
     * Mettre à jour le total de la commande
     */
    private function updateOrderTotal($order): void
    {
        $subtotal = $order->orderItems->sum('total_price');
        $total = $subtotal + $order->tax_amount - $order->discount_amount;

        $order->update([
            'subtotal' => $subtotal,
            'total_amount' => max(0, $total),
        ]);
    }

    /**
     * Créer un paiement
     */
    private function createPayment($order, $cashiers): void
    {
        $cashier = $cashiers->random();
        $paymentMethods = ['cash', 'card', 'mobile_money', 'bank_transfer'];

        $payment = Payment::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'order_id' => $order->id,
            'store_id' => $order->store_id,
            'cashier_id' => $cashier->id,
            'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
            'amount' => $order->total_amount,
            'payment_method' => fake()->randomElement($paymentMethods),
            'transaction_reference' => fake()->optional(0.7)->bothify('TXN-########'),
            'status' => fake()->randomElement(['pending', 'completed']),
            'notes' => fake()->optional(0.2)->sentence(),
            'paid_at' => fake()->optional(0.8)->dateTimeBetween('-30 days', 'now'),
        ]);

        // Mettre à jour le statut de la commande si le paiement est terminé
        if ($payment->status === 'completed') {
            $order->update([
                'status' => 'completed',
                'payment_status' => 'paid',
                'cashier_id' => $cashier->id,
                'completed_at' => $payment->paid_at,
            ]);
        }
    }

    /**
     * Afficher les statistiques
     */
    private function displayStatistics(): void
    {
        $this->command->info("\n📊 Statistiques des commandes:");
        
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'completed')->count();
        $totalSales = Order::where('status', 'completed')->sum('total_amount');
        $totalPayments = Payment::count();
        $completedPayments = Payment::where('status', 'completed')->count();

        $this->command->table(
            ['Statistique', 'Nombre'],
            [
                ['Total des commandes', $totalOrders],
                ['Commandes en attente', $pendingOrders],
                ['Commandes terminées', $completedOrders],
                ['Chiffre d\'affaires', number_format($totalSales, 0, ',', ' ') . ' FCFA'],
                ['Total des paiements', $totalPayments],
                ['Paiements terminés', $completedPayments],
            ]
        );

        // Statistiques par boutique
        $this->command->info("\n🏪 Commandes par boutique:");
        $stores = Store::withCount('orders')->get();
        
        $storeData = $stores->map(function ($store) {
            $sales = Order::where('store_id', $store->id)->where('status', 'completed')->sum('total_amount');
            return [
                'Boutique' => $store->name,
                'Commandes' => $store->orders_count,
                'Ventes' => number_format($sales, 0, ',', ' ') . ' FCFA',
            ];
        });

        $this->command->table(
            ['Boutique', 'Commandes', 'Chiffre d\'affaires'],
            $storeData->toArray()
        );
    }
}
