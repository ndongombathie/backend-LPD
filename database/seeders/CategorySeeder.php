<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Fournitures Scolaires',
                'description' => 'Cahiers, stylos, crayons, règles, etc.',
                'color' => '#3498db',
                'is_active' => true,
            ],
            [
                'name' => 'Livres et Manuels',
                'description' => 'Livres scolaires, manuels, dictionnaires',
                'color' => '#e74c3c',
                'is_active' => true,
            ],
            [
                'name' => 'Matériel Informatique',
                'description' => 'Ordinateurs, tablettes, accessoires informatiques',
                'color' => '#2ecc71',
                'is_active' => true,
            ],
            [
                'name' => 'Vêtements Scolaires',
                'description' => 'Uniformes, chaussures, accessoires',
                'color' => '#f39c12',
                'is_active' => true,
            ],
            [
                'name' => 'Matériel de Bureau',
                'description' => 'Agendas, classeurs, dossiers, etc.',
                'color' => '#9b59b6',
                'is_active' => true,
            ],
            [
                'name' => 'Jeux et Jouets Éducatifs',
                'description' => 'Jeux éducatifs, puzzles, jouets',
                'color' => '#1abc9c',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            $category['id'] = \Illuminate\Support\Str::uuid();
            Category::create($category);
        }
    }
}
