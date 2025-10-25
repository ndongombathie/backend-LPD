# 🏭 Factories et Seeders - Guide Complet

## 📋 Vue d'ensemble

Ce document explique comment utiliser les factories et seeders pour peupler la base de données avec des données de test réalistes pour le système de gestion de stock multi-boutiques.

## 🏭 Factories Disponibles

### 1. ProductFactory

#### **Données Générées**
- **20+ produits réalistes** de matériel scolaire
- **6 catégories** : Fournitures, Livres, Informatique, Vêtements, Bureau, Jeux
- **Prix réalistes** en FCFA
- **SKU uniques** avec préfixes logiques
- **Stocks variés** (faible, normal, élevé, rupture)

#### **États Disponibles**
```php
// Produit en rupture de stock
Product::factory()->outOfStock()->create();

// Produit avec stock faible
Product::factory()->lowStock()->create();

// Produit avec stock élevé
Product::factory()->highStock()->create();

// Produit inactif
Product::factory()->inactive()->create();
```

#### **Exemples de Produits Générés**
- **Fournitures** : Cahiers, Stylos, Crayons, Règles, Gommes
- **Livres** : Dictionnaires, Manuels scolaires, Cahiers d'exercices
- **Informatique** : Calculatrices, Clés USB, Souris
- **Vêtements** : Chemises, Pantalons, Chaussures scolaires
- **Bureau** : Agendas, Classeurs, Dossiers
- **Jeux** : Puzzles, Jeux de cartes, Cubes de construction

### 2. UserFactory (Laravel par défaut)

#### **Utilisateurs Créés**
- **1 Admin global** : `admin@stockmanagement.com`
- **Par boutique** :
  - 1 Gestionnaire : `manager@[boutique].com`
  - 1 Vendeur : `seller@[boutique].com`
  - 1 Caissier : `cashier@[boutique].com`

## 🌱 Seeders Disponibles

### 1. StoreSeeder
```php
// Crée 5 boutiques
- Boutique Centre-Ville
- Boutique Quartier Nord
- Boutique Quartier Sud
- Boutique Quartier Est
- Boutique Quartier Ouest
```

### 2. CategorySeeder
```php
// Crée 6 catégories
- Fournitures Scolaires
- Livres et Manuels
- Matériel Informatique
- Vêtements Scolaires
- Matériel de Bureau
- Jeux et Jouets Éducatifs
```

### 3. UserSeeder
```php
// Crée les utilisateurs pour chaque boutique
// + 1 administrateur global
```

### 4. ProductSeeder
```php
// Crée 20-30 produits par boutique
// + produits avec différents états de stock
// + statistiques détaillées
```

### 5. OrderSeeder
```php
// Crée 15-25 commandes par boutique
// + articles de commande
// + paiements (80% des commandes)
// + mise à jour des stocks
```

## 🚀 Utilisation

### 1. Exécution Complète
```bash
# Exécuter tous les seeders
php artisan db:seed

# Ou avec migration
php artisan migrate:fresh --seed
```

### 2. Exécution Individuelle
```bash
# Seulement les produits
php artisan db:seed --class=ProductSeeder

# Seulement les commandes
php artisan db:seed --class=OrderSeeder
```

### 3. Test des Seeders
```bash
# Exécuter le script de test
php test-seeders.php
```

## 📊 Données Générées

### **Statistiques Typiques**
- **5 boutiques** avec informations complètes
- **6 catégories** de produits
- **16 utilisateurs** (1 admin + 15 par boutique)
- **100-150 produits** répartis entre les boutiques
- **75-125 commandes** avec historique complet
- **60-100 paiements** avec différentes méthodes

### **Répartition des Produits**
```
Par boutique (20-30 produits):
├── 15-20 produits normaux
├── 3-8 produits en stock faible
├── 2-5 produits en rupture
├── 5-10 produits en stock élevé
└── 2-5 produits inactifs
```

### **Répartition des Commandes**
```
Par boutique (15-25 commandes):
├── 30% en attente (pending)
├── 20% en traitement (processing)
├── 50% terminées (completed)
└── 80% avec paiement associé
```

## 🎯 Cas d'Usage

### 1. Développement Local
```bash
# Configuration complète pour le développement
php artisan migrate:fresh --seed
```

### 2. Tests de Performance
```bash
# Générer plus de données
Product::factory()->count(1000)->create();
```

### 3. Tests Spécifiques
```php
// Produits en rupture pour tester les alertes
Product::factory()->outOfStock()->count(10)->create();

// Commandes en attente pour tester les notifications
Order::factory()->pending()->count(5)->create();
```

## 🔧 Personnalisation

### 1. Modifier les Données
```php
// Dans ProductFactory.php
$products = [
    [
        'name' => 'Votre Produit',
        'description' => 'Description personnalisée',
        'price' => 5000,
        'category_id' => 1,
    ],
    // ... autres produits
];
```

### 2. Ajouter de Nouveaux États
```php
// Dans ProductFactory.php
public function customState(): static
{
    return $this->state(fn (array $attributes) => [
        'stock_quantity' => 100,
        'min_stock_level' => 50,
    ]);
}
```

### 3. Créer de Nouveaux Seeders
```bash
php artisan make:seeder CustomSeeder
```

## 📈 Statistiques Générées

### **Produits**
- Total par boutique
- Répartition par catégorie
- États de stock (normal, faible, rupture)
- Produits actifs/inactifs

### **Commandes**
- Total par boutique
- Répartition par statut
- Chiffre d'affaires par boutique
- Historique des ventes

### **Paiements**
- Total par méthode
- Paiements terminés/en attente
- Références de transaction

## 🎉 Résultat Final

Après l'exécution des seeders, vous aurez :

✅ **Base de données complète** avec des données réalistes
✅ **5 boutiques** opérationnelles
✅ **16 utilisateurs** avec différents rôles
✅ **100+ produits** de matériel scolaire
✅ **75+ commandes** avec historique
✅ **60+ paiements** avec différentes méthodes
✅ **Notifications temps réel** fonctionnelles
✅ **Système prêt** pour les tests et le développement

## 🚀 Prochaines Étapes

1. **Tester l'API** avec les données générées
2. **Vérifier les notifications** temps réel
3. **Tester les différents rôles** utilisateur
4. **Valider les fonctionnalités** de gestion de stock
5. **Déployer** en production

---

**💡 Conseil** : Utilisez `php test-seeders.php` pour vérifier que tous les seeders fonctionnent correctement avant de commencer le développement.
