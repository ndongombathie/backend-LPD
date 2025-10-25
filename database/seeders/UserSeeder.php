<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Store;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin global
        User::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'name' => 'Administrateur Global',
            'email' => 'admin@stockmanagement.com',
            'password' => Hash::make('password'),
            'store_id' => null,
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Utilisateurs pour chaque boutique
        $stores = Store::all();
        
        foreach ($stores as $store) {
            // Gestionnaire de boutique
            User::create([
                'id' => \Illuminate\Support\Str::uuid(),
                'name' => 'Gestionnaire ' . $store->name,
                'email' => 'manager@' . strtolower(str_replace(' ', '', $store->name)) . '.com',
                'password' => Hash::make('password'),
                'store_id' => $store->id,
                'role' => 'store_manager',
                'is_active' => true,
            ]);

            // Vendeur
            User::create([
                'id' => \Illuminate\Support\Str::uuid(),
                'name' => 'Vendeur ' . $store->name,
                'email' => 'seller@' . strtolower(str_replace(' ', '', $store->name)) . '.com',
                'password' => Hash::make('password'),
                'store_id' => $store->id,
                'role' => 'seller',
                'is_active' => true,
            ]);

            // Caissier
            User::create([
                'id' => \Illuminate\Support\Str::uuid(),
                'name' => 'Caissier ' . $store->name,
                'email' => 'cashier@' . strtolower(str_replace(' ', '', $store->name)) . '.com',
                'password' => Hash::make('password'),
                'store_id' => $store->id,
                'role' => 'cashier',
                'is_active' => true,
            ]);
        }
    }
}
