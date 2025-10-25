<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Store;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stores = [
            [
                'name' => 'Boutique Centre-Ville',
                'address' => '123 Rue du Commerce, Centre-Ville',
                'phone' => '+237 6XX XX XX XX',
                'email' => 'centre@boutique.com',
                'description' => 'Boutique principale située au centre-ville',
                'is_active' => true,
            ],
            [
                'name' => 'Boutique Quartier Nord',
                'address' => '456 Avenue du Nord, Quartier Nord',
                'phone' => '+237 6XX XX XX XX',
                'email' => 'nord@boutique.com',
                'description' => 'Boutique du quartier nord',
                'is_active' => true,
            ],
            [
                'name' => 'Boutique Quartier Sud',
                'address' => '789 Boulevard du Sud, Quartier Sud',
                'phone' => '+237 6XX XX XX XX',
                'email' => 'sud@boutique.com',
                'description' => 'Boutique du quartier sud',
                'is_active' => true,
            ],
            [
                'name' => 'Boutique Quartier Est',
                'address' => '321 Rue de l\'Est, Quartier Est',
                'phone' => '+237 6XX XX XX XX',
                'email' => 'est@boutique.com',
                'description' => 'Boutique du quartier est',
                'is_active' => true,
            ],
            [
                'name' => 'Boutique Quartier Ouest',
                'address' => '654 Avenue de l\'Ouest, Quartier Ouest',
                'phone' => '+237 6XX XX XX XX',
                'email' => 'ouest@boutique.com',
                'description' => 'Boutique du quartier ouest',
                'is_active' => true,
            ],
        ];

        foreach ($stores as $store) {
            $store['id'] = \Illuminate\Support\Str::uuid();
            Store::create($store);
        }
    }
}
