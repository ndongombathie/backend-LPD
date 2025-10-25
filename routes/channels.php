<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Canal privé pour les notifications de boutique
Broadcast::channel('store.{storeId}', function ($user, $storeId) {
    // L'admin peut écouter tous les canaux
    if ($user->isAdmin()) {
        return true;
    }
    
    // Les autres utilisateurs ne peuvent écouter que leur propre boutique
    return (int) $user->store_id === (int) $storeId;
});

// Canal pour les notifications globales (admin seulement)
Broadcast::channel('admin', function ($user) {
    return $user->isAdmin();
});
