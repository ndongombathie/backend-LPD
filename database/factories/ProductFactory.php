<?php

namespace Database\Factories;

use App\Models\Store;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Données réalistes pour le matériel scolaire
        $products = [
            // Fournitures Scolaires
            [
                'name' => 'Cahier 200 pages',
                'description' => 'Cahier à spirale 200 pages, format A4, ligné',
                'sku' => 'CAH-200-' . $this->faker->unique()->numberBetween(1000, 9999),
                'price' => $this->faker->numberBetween(800, 1500),
                'cost_price' => $this->faker->numberBetween(400, 800),
                'stock_quantity' => $this->faker->numberBetween(50, 200),
                'min_stock_level' => $this->faker->numberBetween(5, 20),
                'category_id' => 1, // Fournitures Scolaires
            ],
            [
                'name' => 'Stylo Bic Bleu',
                'description' => 'Stylo à bille Bic, couleur bleue, pointe fine',
                'sku' => 'STY-BIC-BLEU-' . $this->faker->unique()->numberBetween(1000, 9999),
                'price' => $this->faker->numberBetween(100, 300),
                'cost_price' => $this->faker->numberBetween(50, 150),
                'stock_quantity' => $this->faker->numberBetween(100, 500),
                'min_stock_level' => $this->faker->numberBetween(20, 50),
                'category_id' => 1,
            ],
            [
                'name' => 'Crayon HB',
                'description' => 'Crayon à papier HB, boîte de 12',
                'sku' => 'CRY-HB-12-' . $this->faker->unique()->numberBetween(1000, 9999),
                'price' => $this->faker->numberBetween(200, 500),
                'cost_price' => $this->faker->numberBetween(100, 250),
                'stock_quantity' => $this->faker->numberBetween(50, 150),
                'min_stock_level' => $this->faker->numberBetween(10, 30),
                'category_id' => 1,
            ],
            [
                'name' => 'Règle 30cm',
                'description' => 'Règle transparente 30cm, graduée',
                'sku' => 'REG-30CM-' . $this->faker->unique()->numberBetween(1000, 9999),
                'price' => $this->faker->numberBetween(150, 400),
                'cost_price' => $this->faker->numberBetween(75, 200),
                'stock_quantity' => $this->faker->numberBetween(30, 100),
                'min_stock_level' => $this->faker->numberBetween(5, 15),
                'category_id' => 1,
            ],
            [
                'name' => 'Gomme Blanche',
                'description' => 'Gomme blanche rectangulaire, efface proprement',
                'sku' => 'GOM-BLAN-' . $this->faker->unique()->numberBetween(1000, 9999),
                'price' => $this->faker->numberBetween(50, 150),
                'cost_price' => $this->faker->numberBetween(25, 75),
                'stock_quantity' => $this->faker->numberBetween(80, 200),
                'min_stock_level' => $this->faker->numberBetween(15, 40),
                'category_id' => 1,
            ],
            
            // Livres et Manuels
            [
                'name' => 'Dictionnaire Larousse',
                'description' => 'Dictionnaire français Larousse, édition 2024',
                'sku' => 'DIC-LAR-' . $this->faker->unique()->numberBetween(1000, 9999),
                'price' => $this->faker->numberBetween(5000, 8000),
                'cost_price' => $this->faker->numberBetween(2500, 4000),
                'stock_quantity' => $this->faker->numberBetween(10, 50),
                'min_stock_level' => $this->faker->numberBetween(2, 10),
                'category_id' => 2, // Livres et Manuels
            ],
            [
                'name' => 'Manuel de Mathématiques 6ème',
                'description' => 'Manuel scolaire de mathématiques pour la 6ème',
                'sku' => 'MAN-MATH-6E-' . $this->faker->unique()->numberBetween(1000, 9999),
                'price' => $this->faker->numberBetween(3000, 5000),
                'cost_price' => $this->faker->numberBetween(1500, 2500),
                'stock_quantity' => $this->faker->numberBetween(20, 80),
                'min_stock_level' => $this->faker->numberBetween(5, 15),
                'category_id' => 2,
            ],
            [
                'name' => 'Cahier d\'exercices Français',
                'description' => 'Cahier d\'exercices de français, niveau primaire',
                'sku' => 'CAH-FR-' . $this->faker->unique()->numberBetween(1000, 9999),
                'price' => $this->faker->numberBetween(1500, 2500),
                'cost_price' => $this->faker->numberBetween(750, 1250),
                'stock_quantity' => $this->faker->numberBetween(30, 100),
                'min_stock_level' => $this->faker->numberBetween(5, 20),
                'category_id' => 2,
            ],
            
            // Matériel Informatique
            [
                'name' => 'Calculatrice Casio FX-82',
                'description' => 'Calculatrice scientifique Casio FX-82, autorisée aux examens',
                'sku' => 'CAL-CAS-FX82-' . $this->faker->unique()->numberBetween(1000, 9999),
                'price' => $this->faker->numberBetween(8000, 12000),
                'cost_price' => $this->faker->numberBetween(4000, 6000),
                'stock_quantity' => $this->faker->numberBetween(15, 60),
                'min_stock_level' => $this->faker->numberBetween(3, 10),
                'category_id' => 3, // Matériel Informatique
            ],
            [
                'name' => 'Clé USB 32GB',
                'description' => 'Clé USB 32GB, USB 3.0, pour stockage de documents',
                'sku' => 'USB-32GB-' . $this->faker->unique()->numberBetween(1000, 9999),
                'price' => $this->faker->numberBetween(3000, 5000),
                'cost_price' => $this->faker->numberBetween(1500, 2500),
                'stock_quantity' => $this->faker->numberBetween(20, 80),
                'min_stock_level' => $this->faker->numberBetween(5, 15),
                'category_id' => 3,
            ],
            [
                'name' => 'Souris USB',
                'description' => 'Souris optique USB, filaire, pour ordinateur',
                'sku' => 'SOU-USB-' . $this->faker->unique()->numberBetween(1000, 9999),
                'price' => $this->faker->numberBetween(2000, 4000),
                'cost_price' => $this->faker->numberBetween(1000, 2000),
                'stock_quantity' => $this->faker->numberBetween(10, 40),
                'min_stock_level' => $this->faker->numberBetween(2, 8),
                'category_id' => 3,
            ],
            
            // Vêtements Scolaires
            [
                'name' => 'Chemise Blanche',
                'description' => 'Chemise blanche pour uniforme scolaire, taille M',
                'sku' => 'CHE-BLAN-M-' . $this->faker->unique()->numberBetween(1000, 9999),
                'price' => $this->faker->numberBetween(3000, 5000),
                'cost_price' => $this->faker->numberBetween(1500, 2500),
                'stock_quantity' => $this->faker->numberBetween(20, 80),
                'min_stock_level' => $this->faker->numberBetween(5, 15),
                'category_id' => 4, // Vêtements Scolaires
            ],
            [
                'name' => 'Pantalon Noir',
                'description' => 'Pantalon noir pour uniforme scolaire, taille L',
                'sku' => 'PAN-NOIR-L-' . $this->faker->unique()->numberBetween(1000, 9999),
                'price' => $this->faker->numberBetween(4000, 6000),
                'cost_price' => $this->faker->numberBetween(2000, 3000),
                'stock_quantity' => $this->faker->numberBetween(15, 60),
                'min_stock_level' => $this->faker->numberBetween(3, 12),
                'category_id' => 4,
            ],
            [
                'name' => 'Chaussures Noires',
                'description' => 'Chaussures noires pour uniforme scolaire, pointure 40',
                'sku' => 'CHAU-NOIR-40-' . $this->faker->unique()->numberBetween(1000, 9999),
                'price' => $this->faker->numberBetween(8000, 12000),
                'cost_price' => $this->faker->numberBetween(4000, 6000),
                'stock_quantity' => $this->faker->numberBetween(10, 40),
                'min_stock_level' => $this->faker->numberBetween(2, 8),
                'category_id' => 4,
            ],
            
            // Matériel de Bureau
            [
                'name' => 'Agenda 2024',
                'description' => 'Agenda scolaire 2024, format A5, couverture rigide',
                'sku' => 'AGA-2024-' . $this->faker->unique()->numberBetween(1000, 9999),
                'price' => $this->faker->numberBetween(2000, 3500),
                'cost_price' => $this->faker->numberBetween(1000, 1750),
                'stock_quantity' => $this->faker->numberBetween(30, 100),
                'min_stock_level' => $this->faker->numberBetween(5, 20),
                'category_id' => 5, // Matériel de Bureau
            ],
            [
                'name' => 'Classeur 4 Anneaux',
                'description' => 'Classeur 4 anneaux, format A4, couleur bleue',
                'sku' => 'CLA-4ANN-BLEU-' . $this->faker->unique()->numberBetween(1000, 9999),
                'price' => $this->faker->numberBetween(1500, 2500),
                'cost_price' => $this->faker->numberBetween(750, 1250),
                'stock_quantity' => $this->faker->numberBetween(25, 80),
                'min_stock_level' => $this->faker->numberBetween(5, 15),
                'category_id' => 5,
            ],
            [
                'name' => 'Dossier Carton',
                'description' => 'Dossier carton, format A4, pour classement',
                'sku' => 'DOS-CART-' . $this->faker->unique()->numberBetween(1000, 9999),
                'price' => $this->faker->numberBetween(200, 500),
                'cost_price' => $this->faker->numberBetween(100, 250),
                'stock_quantity' => $this->faker->numberBetween(50, 150),
                'min_stock_level' => $this->faker->numberBetween(10, 30),
                'category_id' => 5,
            ],
            
            // Jeux et Jouets Éducatifs
            [
                'name' => 'Puzzle 100 pièces',
                'description' => 'Puzzle éducatif 100 pièces, thème géographie',
                'sku' => 'PUZ-100-GEO-' . $this->faker->unique()->numberBetween(1000, 9999),
                'price' => $this->faker->numberBetween(2500, 4000),
                'cost_price' => $this->faker->numberBetween(1250, 2000),
                'stock_quantity' => $this->faker->numberBetween(15, 50),
                'min_stock_level' => $this->faker->numberBetween(3, 10),
                'category_id' => 6, // Jeux et Jouets Éducatifs
            ],
            [
                'name' => 'Jeu de Cartes Éducatif',
                'description' => 'Jeu de cartes pour apprendre les mathématiques',
                'sku' => 'JEU-CART-MATH-' . $this->faker->unique()->numberBetween(1000, 9999),
                'price' => $this->faker->numberBetween(1500, 3000),
                'cost_price' => $this->faker->numberBetween(750, 1500),
                'stock_quantity' => $this->faker->numberBetween(20, 60),
                'min_stock_level' => $this->faker->numberBetween(4, 12),
                'category_id' => 6,
            ],
            [
                'name' => 'Cubes de Construction',
                'description' => 'Cubes de construction en plastique, 50 pièces',
                'sku' => 'CUB-CONST-50-' . $this->faker->unique()->numberBetween(1000, 9999),
                'price' => $this->faker->numberBetween(3000, 5000),
                'cost_price' => $this->faker->numberBetween(1500, 2500),
                'stock_quantity' => $this->faker->numberBetween(10, 40),
                'min_stock_level' => $this->faker->numberBetween(2, 8),
                'category_id' => 6,
            ],
        ];

        // Sélectionner un produit aléatoire
        $product = $this->faker->randomElement($products);
        
        return [
            'id' => \Illuminate\Support\Str::uuid(),
            'store_id' => Store::inRandomOrder()->first()->id,
            'category_id' => Category::inRandomOrder()->first()->id,
            'name' => $product['name'],
            'description' => $product['description'],
            'sku' => $product['sku'],
            'price' => $product['price'],
            'cost_price' => $product['cost_price'],
            'stock_quantity' => $product['stock_quantity'],
            'min_stock_level' => $product['min_stock_level'],
            'image' => null, // Pas d'image par défaut
            'is_active' => $this->faker->boolean(90), // 90% de chance d'être actif
        ];
    }

    /**
     * Indicate that the product is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => 0,
            'min_stock_level' => $this->faker->numberBetween(1, 5),
        ]);
    }

    /**
     * Indicate that the product has low stock.
     */
    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => $this->faker->numberBetween(1, 3),
            'min_stock_level' => $this->faker->numberBetween(5, 10),
        ]);
    }

    /**
     * Indicate that the product has high stock.
     */
    public function highStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => $this->faker->numberBetween(200, 500),
            'min_stock_level' => $this->faker->numberBetween(10, 20),
        ]);
    }

    /**
     * Indicate that the product is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
