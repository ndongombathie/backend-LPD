# Système de Gestion de Stock Multi-Boutiques

## 📋 Description

Système de gestion de stock et de vente en temps réel pour plusieurs boutiques de matériel scolaire. L'application permet la gestion centralisée des stocks, des commandes, des paiements et des transferts entre boutiques.

## 🚀 Fonctionnalités

### 🔐 Authentification et Rôles
- **Administrateur Global** : Gestion complète du système, supervision de toutes les boutiques
- **Gestionnaire de Boutique** : Gestion des produits, stocks et rapports de sa boutique
- **Vendeur** : Création de commandes clients
- **Caissier** : Traitement des paiements et impression des factures

### 📦 Gestion des Produits
- CRUD complet des produits
- Gestion des catégories
- Suivi du stock en temps réel
- Alertes de rupture de stock
- Gestion des images de produits

### 🛒 Gestion des Commandes
- Création de commandes par les vendeurs
- Notifications temps réel pour les caissiers
- Suivi du statut des commandes
- Gestion des remises et taxes

### 💰 Gestion des Paiements
- Traitement des paiements par les caissiers
- Génération automatique de factures
- Support de plusieurs méthodes de paiement
- Historique des transactions

### 🔄 Transferts Inter-Boutiques
- Demande de transfert de stock
- Approbation par les gestionnaires
- Suivi des transferts

### 📊 Rapports et Tableaux de Bord
- Statistiques de ventes par boutique
- Rapports de stock
- Performance des vendeurs
- Export des données

## 🛠️ Technologies Utilisées

- **Backend** : Laravel 12
- **Authentification** : Laravel Sanctum
- **Base de données** : MySQL
- **Temps réel** : Laravel Broadcasting + Pusher
- **API** : RESTful API

## 📁 Structure du Projet

```
stock-management-api/
├── app/
│   ├── Http/Controllers/Api/     # Contrôleurs API
│   ├── Models/                   # Modèles Eloquent
│   └── Events/                   # Événements temps réel
├── database/
│   ├── migrations/               # Migrations de base de données
│   └── seeders/                  # Seeders pour les données de test
├── routes/
│   └── api.php                   # Routes API
└── config/
    └── sanctum.php              # Configuration Sanctum
```

## 🚀 Installation

### Prérequis
- PHP 8.3+
- Composer
- MySQL
- Node.js (pour le frontend)

### Étapes d'installation

1. **Cloner le projet**
```bash
git clone <repository-url>
cd stock-management-api
```

2. **Installer les dépendances**
```bash
composer install
```

3. **Configuration de l'environnement**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configuration de la base de données**
Modifiez le fichier `.env` avec vos paramètres de base de données :
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stock_management
DB_USERNAME=root
DB_PASSWORD=your_password
```

5. **Exécuter les migrations et seeders**
```bash
php artisan migrate:fresh --seed
```

6. **Vérifier les données créées**
```bash
php test-seeders.php
```

7. **Démarrer le serveur**
```bash
php artisan serve
```

## 📚 API Documentation

### Authentification

#### Connexion
```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "admin@stockmanagement.com",
    "password": "password"
}
```

#### Inscription
```http
POST /api/auth/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password",
    "password_confirmation": "password",
    "store_id": 1,
    "role": "seller"
}
```

### Produits

#### Lister les produits
```http
GET /api/products
Authorization: Bearer {token}
```

#### Créer un produit
```http
POST /api/products
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Cahier 200 pages",
    "description": "Cahier à spirale 200 pages",
    "sku": "CAH-200-001",
    "price": 1500,
    "cost_price": 1000,
    "stock_quantity": 100,
    "min_stock_level": 10,
    "category_id": 1
}
```

### Commandes

#### Créer une commande
```http
POST /api/orders
Authorization: Bearer {token}
Content-Type: application/json

{
    "customer_name": "Jean Dupont",
    "customer_phone": "+237 6XX XX XX XX",
    "items": [
        {
            "product_id": 1,
            "quantity": 2
        }
    ],
    "tax_amount": 0,
    "discount_amount": 0
}
```

#### Commandes en attente (pour caissiers)
```http
GET /api/orders/pending
Authorization: Bearer {token}
```

## 🔧 Configuration

### Variables d'environnement importantes

```env
# Base de données
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stock_management
DB_USERNAME=root
DB_PASSWORD=

# Broadcasting (pour les notifications temps réel)
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1
```

## 👥 Utilisateurs par défaut

Après l'exécution des seeders, les utilisateurs suivants sont créés :

### Administrateur Global
- **Email** : admin@stockmanagement.com
- **Mot de passe** : password
- **Rôle** : admin

### Par boutique (5 boutiques créées)
- **Gestionnaire** : manager@[boutique].com
- **Vendeur** : seller@[boutique].com  
- **Caissier** : cashier@[boutique].com
- **Mot de passe** : password

## 🔄 Notifications Temps Réel

Le système utilise Laravel Broadcasting pour les notifications temps réel :

- **Nouvelle commande** : Notifie les caissiers instantanément
- **Mise à jour de commande** : Notifie tous les utilisateurs de la boutique
- **Annulation de commande** : Restaure le stock et notifie les utilisateurs

## 📊 Rapports et Statistiques

### Endpoints disponibles

- `GET /api/dashboard/stats` - Statistiques générales
- `GET /api/dashboard/sales` - Données de ventes
- `GET /api/dashboard/products` - Statistiques des produits
- `GET /api/dashboard/orders` - Statistiques des commandes

## 🧪 Tests

### Tests Unitaires
```bash
# Exécuter les tests
php artisan test

# Tests avec couverture
php artisan test --coverage
```

### Tests des Notifications Temps Réel

#### 1. Interface de Test Web
```bash
# Ouvrir dans le navigateur
http://localhost:8000/realtime-test.html
```

#### 2. Script de Test Automatisé
```bash
# Installer les dépendances
npm install

# Exécuter le test des notifications
npm test
```

#### 3. Test Manuel avec Postman
1. Créer une collection Postman
2. Configurer l'authentification Bearer Token
3. Tester les endpoints de commandes
4. Vérifier les notifications temps réel

### 🔔 Notifications Temps Réel

Le système envoie des notifications instantanées pour :
- **Nouvelle commande** : Les caissiers reçoivent immédiatement les commandes des vendeurs
- **Mise à jour de commande** : Changements de statut en temps réel
- **Paiements** : Notifications de création et finalisation des paiements
- **Transferts** : Suivi des transferts entre boutiques

**Documentation complète** : Voir `REALTIME_NOTIFICATIONS.md`

## 🏭 Factories et Seeders

### Données de Test Générées
- **5 boutiques** avec informations complètes
- **6 catégories** de produits scolaires
- **16 utilisateurs** (admin + gestionnaires, vendeurs, caissiers)
- **100+ produits** réalistes de matériel scolaire
- **75+ commandes** avec historique complet
- **60+ paiements** avec différentes méthodes

### Utilisation
```bash
# Générer toutes les données de test
php artisan migrate:fresh --seed

# Vérifier les données créées
php test-seeders.php

# Seeders individuels
php artisan db:seed --class=ProductSeeder
php artisan db:seed --class=OrderSeeder
```

### Produits Générés
- **Fournitures** : Cahiers, Stylos, Crayons, Règles
- **Livres** : Dictionnaires, Manuels scolaires
- **Informatique** : Calculatrices, Clés USB
- **Vêtements** : Uniformes scolaires
- **Bureau** : Agendas, Classeurs
- **Jeux** : Puzzles, Jeux éducatifs

**Documentation complète** : Voir `FACTORIES_SEEDERS.md`

## 🔑 UUID Implementation

### Sécurité Renforcée
- **UUID au lieu d'IDs numériques** pour tous les modèles
- **Sécurité des APIs** : IDs non devinables
- **Identifiants uniques globaux** : Pas de collision entre environnements
- **Audit et traçabilité** améliorés

### Utilisation
```bash
# Tester l'implémentation des UUID
php test-uuid.php

# Vérifier les UUID générés
php artisan tinker
>>> App\Models\Product::first()->id
```

### Avantages
- **Sécurité** : Impossible de deviner les autres IDs
- **Universalité** : Identifiants uniques entre systèmes
- **APIs sécurisées** : Pas d'exposition de la structure
- **Synchronisation** : Pas de collision entre environnements

**Documentation complète** : Voir `UUID_IMPLEMENTATION.md`

## 📝 Licence

Ce projet est sous licence MIT.

## 🤝 Contribution

1. Fork le projet
2. Créer une branche feature (`git checkout -b feature/AmazingFeature`)
3. Commit les changements (`git commit -m 'Add some AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

## 📞 Support

Pour toute question ou problème, contactez l'équipe de développement.

---

**Développé avec ❤️ pour la gestion efficace des boutiques de matériel scolaire**