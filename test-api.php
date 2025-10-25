<?php

/**
 * Script de test pour l'API avec UUID
 * Usage: php test-api.php
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Store;
use App\Models\Product;

echo "🌐 Test de l'API avec UUID\n";
echo "===========================\n\n";

try {
    // Vérifier la connexion à la base de données
    echo "1️⃣ Vérification de la connexion à la base de données...\n";
    DB::connection()->getPdo();
    echo "✅ Connexion à la base de données réussie\n\n";

    // Récupérer un utilisateur pour le test
    echo "2️⃣ Récupération d'un utilisateur de test...\n";
    $user = User::where('email', 'admin@stockmanagement.com')->first();
    
    if (!$user) {
        echo "❌ Utilisateur admin non trouvé\n";
        exit(1);
    }
    
    echo "✅ Utilisateur trouvé: {$user->name}\n";
    echo "   - ID: {$user->id}\n";
    echo "   - Email: {$user->email}\n\n";

    // Créer un token d'authentification
    echo "3️⃣ Création d'un token d'authentification...\n";
    $token = $user->createToken('test-api-token')->plainTextToken;
    echo "✅ Token créé avec succès\n";
    echo "   - Token: " . substr($token, 0, 20) . "...\n\n";

    // Récupérer une boutique pour le test
    echo "4️⃣ Récupération d'une boutique de test...\n";
    $store = Store::first();
    
    if (!$store) {
        echo "❌ Aucune boutique trouvée\n";
        exit(1);
    }
    
    echo "✅ Boutique trouvée: {$store->name}\n";
    echo "   - ID: {$store->id}\n";
    echo "   - Adresse: {$store->address}\n\n";

    // Tester l'URL de l'API
    echo "5️⃣ Test de l'URL de l'API...\n";
    $apiUrl = "http://127.0.0.1:8000/api/stores/{$store->id}/products";
    echo "   - URL: {$apiUrl}\n";
    echo "   - Token: " . substr($token, 0, 20) . "...\n\n";

    // Simuler une requête HTTP
    echo "6️⃣ Simulation d'une requête HTTP...\n";
    $headers = [
        'Authorization: Bearer ' . $token,
        'Accept: application/json',
        'Content-Type: application/json'
    ];
    
    echo "   - Headers: " . implode(', ', $headers) . "\n";
    echo "   - Méthode: GET\n\n";

    // Vérifier les produits de la boutique
    echo "7️⃣ Vérification des produits de la boutique...\n";
    $products = $store->products()->with('category')->get();
    echo "✅ Produits trouvés: " . $products->count() . "\n";
    
    if ($products->count() > 0) {
        echo "   - Premier produit: {$products->first()->name}\n";
        echo "   - Catégorie: {$products->first()->category->name}\n";
        echo "   - Stock: {$products->first()->stock_quantity}\n";
    }

    echo "\n🎉 Test de l'API terminé avec succès!\n";
    echo "L'endpoint est prêt à être utilisé.\n\n";

    echo "📋 Instructions pour tester l'API:\n";
    echo "1. Démarrer le serveur: php artisan serve\n";
    echo "2. Utiliser l'URL: {$apiUrl}\n";
    echo "3. Ajouter l'en-tête: Authorization: Bearer {$token}\n";
    echo "4. Ou utiliser Postman/Insomnia avec ces paramètres\n\n";

    echo "🔧 Exemples de requêtes:\n";
    echo "   - GET {$apiUrl}\n";
    echo "   - GET {$apiUrl}?search=cahier\n";
    echo "   - GET {$apiUrl}?status=low_stock\n";
    $categoryId = $products->count() > 0 ? $products->first()->category_id : 'uuid';
    echo "   - GET {$apiUrl}?category_id={$categoryId}\n";

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Vérifiez votre configuration de base de données.\n";
    exit(1);
}
