# 🔔 Notifications Temps Réel - Guide Complet

## 📋 Vue d'ensemble

Le système de notifications temps réel permet aux caissiers de recevoir instantanément les commandes validées par les vendeurs, sans avoir besoin de rafraîchir la page.

## 🚀 Fonctionnalités

### ✅ Notifications Implémentées

1. **Nouvelle Commande Créée** (`order_created`)
   - Déclenché quand un vendeur crée une commande
   - Reçu par tous les caissiers de la boutique
   - Contient les détails complets de la commande

2. **Commande Mise à Jour** (`order_updated`)
   - Déclenché quand le statut d'une commande change
   - Reçu par tous les utilisateurs de la boutique

3. **Commande Annulée** (`order_cancelled`)
   - Déclenché quand une commande est annulée
   - Restaure automatiquement le stock

4. **Nouveau Paiement Créé** (`payment_created`)
   - Déclenché quand un caissier commence un paiement
   - Reçu par tous les utilisateurs de la boutique

5. **Paiement Terminé** (`payment_completed`)
   - Déclenché quand un paiement est finalisé
   - Marque la commande comme terminée

## 🛠️ Configuration Technique

### Backend (Laravel)

#### 1. Événements Configurés
```php
// app/Events/OrderCreated.php
// app/Events/OrderUpdated.php
// app/Events/OrderCancelled.php
// app/Events/PaymentCreated.php
// app/Events/PaymentCompleted.php
```

#### 2. Canaux de Diffusion
```php
// routes/channels.php
Broadcast::channel('store.{storeId}', function ($user, $storeId) {
    if ($user->isAdmin()) return true;
    return (int) $user->store_id === (int) $storeId;
});
```

#### 3. Broadcasting dans les Contrôleurs
```php
// Dans OrderController
broadcast(new OrderCreated($order))->toOthers();

// Dans PaymentController
broadcast(new PaymentCreated($payment))->toOthers();
```

### Frontend (JavaScript)

#### 1. Configuration Pusher
```javascript
const pusher = new Pusher('your-pusher-key', {
    cluster: 'mt1',
    authEndpoint: '/api/broadcasting/auth',
    auth: {
        headers: {
            'Authorization': 'Bearer ' + token
        }
    }
});
```

#### 2. Écoute des Canaux
```javascript
const channel = pusher.subscribe('private-store.' + storeId);

channel.bind('order_created', function(data) {
    console.log('Nouvelle commande:', data);
    // Mettre à jour l'interface utilisateur
});
```

## 📱 Interface de Test

### Fichier de Test HTML
- **Fichier** : `public/realtime-test.html`
- **URL** : `http://localhost:8000/realtime-test.html`
- **Fonctionnalités** :
  - Connexion aux canaux de boutique
  - Affichage des notifications en temps réel
  - Test avec différents rôles utilisateur

### Script de Test Node.js
- **Fichier** : `test-commands.js`
- **Usage** : `node test-commands.js`
- **Fonctionnalités** :
  - Simulation de création de commandes
  - Test des notifications automatiques
  - Validation du flux complet

## 🔧 Configuration Requise

### 1. Variables d'Environnement
```env
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1
```

### 2. Dépendances
```bash
# Backend
composer require pusher/pusher-php-server

# Frontend
npm install pusher-js
```

### 3. Configuration Laravel
```php
// config/broadcasting.php
'pusher' => [
    'driver' => 'pusher',
    'key' => env('PUSHER_APP_KEY'),
    'secret' => env('PUSHER_APP_SECRET'),
    'app_id' => env('PUSHER_APP_ID'),
    'options' => [
        'cluster' => env('PUSHER_APP_CLUSTER'),
        'useTLS' => true
    ],
],
```

## 🎯 Flux de Notifications

### Scénario 1 : Nouvelle Commande
```
1. Vendeur crée une commande
   ↓
2. Événement OrderCreated déclenché
   ↓
3. Notification envoyée au canal store.{storeId}
   ↓
4. Caissiers reçoivent la notification instantanément
   ↓
5. Interface mise à jour automatiquement
```

### Scénario 2 : Paiement
```
1. Caissier crée un paiement
   ↓
2. Événement PaymentCreated déclenché
   ↓
3. Notification envoyée à tous les utilisateurs
   ↓
4. Caissier finalise le paiement
   ↓
5. Événement PaymentCompleted déclenché
   ↓
6. Commande marquée comme terminée
```

## 🧪 Tests et Validation

### 1. Test Manuel
1. Ouvrir `realtime-test.html` dans le navigateur
2. Se connecter avec un token d'authentification
3. Sélectionner la boutique à écouter
4. Créer une commande via l'API
5. Vérifier la réception de la notification

### 2. Test Automatisé
```bash
# Installer les dépendances
npm install

# Exécuter le script de test
npm test
```

### 3. Test avec Postman
1. Créer une collection Postman
2. Configurer l'authentification Bearer Token
3. Tester les endpoints de commandes
4. Vérifier les notifications dans le navigateur

## 🔒 Sécurité

### 1. Authentification des Canaux
- Chaque canal est protégé par authentification
- Seuls les utilisateurs autorisés peuvent écouter
- Isolation des données par boutique

### 2. Validation des Permissions
```php
// Seuls les caissiers peuvent créer des paiements
if (!$user->isCashier()) {
    return response()->json(['message' => 'Unauthorized'], 403);
}
```

### 3. Chiffrement des Données
- Tokens JWT sécurisés
- Canaux privés chiffrés
- Validation côté serveur

## 📊 Monitoring et Logs

### 1. Logs des Événements
```php
// Dans les contrôleurs
Log::info('Order created', ['order_id' => $order->id]);
Log::info('Payment completed', ['payment_id' => $payment->id]);
```

### 2. Métriques Pusher
- Nombre de connexions actives
- Messages envoyés par seconde
- Erreurs de connexion

### 3. Dashboard de Monitoring
- Statut des canaux
- Utilisateurs connectés
- Performance des notifications

## 🚀 Déploiement

### 1. Configuration Production
```env
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=prod_app_id
PUSHER_APP_KEY=prod_app_key
PUSHER_APP_SECRET=prod_app_secret
PUSHER_APP_CLUSTER=mt1
```

### 2. Optimisations
- Mise en cache des canaux
- Compression des messages
- Gestion des reconnexions

### 3. Monitoring
- Alertes en cas de déconnexion
- Métriques de performance
- Logs d'erreurs

## 🎉 Résultat Final

Avec cette configuration, **les caissiers reçoivent instantanément les commandes validées par les vendeurs** sans aucun délai, créant une expérience utilisateur fluide et efficace pour la gestion des ventes en temps réel.

---

**💡 Conseil** : Testez toujours les notifications avec plusieurs utilisateurs connectés simultanément pour valider le bon fonctionnement du système temps réel.
