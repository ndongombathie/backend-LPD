<?php

/**
 * Script de test pour vérifier la connexion avec UUID
 * Usage: php test-login.php
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "🔐 Test de Connexion avec UUID\n";
echo "==============================\n\n";

try {
    // Vérifier la connexion à la base de données
    echo "1️⃣ Vérification de la connexion à la base de données...\n";
    DB::connection()->getPdo();
    echo "✅ Connexion à la base de données réussie\n\n";

    // Vérifier les utilisateurs créés
    echo "2️⃣ Vérification des utilisateurs...\n";
    $users = User::all();
    echo "👥 Utilisateurs trouvés: " . $users->count() . "\n";
    
    foreach ($users as $user) {
        $isUuid = preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $user->id);
        $status = $isUuid ? "✅ UUID valide" : "❌ UUID invalide";
        echo "   - {$user->name} ({$user->email}): {$status}\n";
    }

    // Test de connexion avec l'admin
    echo "\n3️⃣ Test de connexion avec l'administrateur...\n";
    $admin = User::where('email', 'admin@stockmanagement.com')->first();
    
    if ($admin) {
        echo "✅ Administrateur trouvé: {$admin->name}\n";
        echo "   - ID: {$admin->id}\n";
        echo "   - Email: {$admin->email}\n";
        echo "   - Rôle: {$admin->role}\n";
        
        // Vérifier le mot de passe
        if (Hash::check('password', $admin->password)) {
            echo "✅ Mot de passe valide\n";
        } else {
            echo "❌ Mot de passe invalide\n";
        }
    } else {
        echo "❌ Administrateur non trouvé\n";
    }

    // Test de connexion avec un vendeur
    echo "\n4️⃣ Test de connexion avec un vendeur...\n";
    $seller = User::where('role', 'seller')->first();
    
    if ($seller) {
        echo "✅ Vendeur trouvé: {$seller->name}\n";
        echo "   - ID: {$seller->id}\n";
        echo "   - Email: {$seller->email}\n";
        echo "   - Boutique: " . ($seller->store ? $seller->store->name : 'Aucune') . "\n";
        
        // Vérifier le mot de passe
        if (Hash::check('password', $seller->password)) {
            echo "✅ Mot de passe valide\n";
        } else {
            echo "❌ Mot de passe invalide\n";
        }
    } else {
        echo "❌ Aucun vendeur trouvé\n";
    }

    // Test de création de token
    echo "\n5️⃣ Test de création de token...\n";
    if ($admin) {
        try {
            $token = $admin->createToken('test-token')->plainTextToken;
            echo "✅ Token créé avec succès\n";
            echo "   - Token: " . substr($token, 0, 20) . "...\n";
            echo "   - Tokenable ID: {$admin->id}\n";
            echo "   - Tokenable Type: " . get_class($admin) . "\n";
        } catch (Exception $e) {
            echo "❌ Erreur lors de la création du token: " . $e->getMessage() . "\n";
        }
    }

    // Test des relations
    echo "\n6️⃣ Test des relations...\n";
    $userWithStore = User::with('store')->whereNotNull('store_id')->first();
    if ($userWithStore) {
        echo "✅ Relation User->Store fonctionne\n";
        echo "   - Utilisateur: {$userWithStore->name}\n";
        echo "   - Boutique: {$userWithStore->store->name}\n";
    }

    echo "\n🎉 Test de connexion terminé avec succès!\n";
    echo "Le système est prêt pour les connexions avec UUID.\n\n";

    echo "📋 Comptes de test disponibles:\n";
    echo "   - Admin: admin@stockmanagement.com / password\n";
    echo "   - Vendeurs: seller@[boutique].com / password\n";
    echo "   - Caissiers: cashier@[boutique].com / password\n";
    echo "   - Gestionnaires: manager@[boutique].com / password\n";

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Vérifiez votre configuration de base de données.\n";
    exit(1);
}
