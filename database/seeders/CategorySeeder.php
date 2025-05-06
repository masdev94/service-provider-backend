<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Technology' => 'IT, Software, Hardware, and digital services',
            'Healthcare' => 'Medical services, pharmaceuticals, and healthcare providers',
            'Finance' => 'Banking, insurance, investments, and financial services',
            'Education' => 'Schools, universities, training platforms, and educational services',
            'Hospitality' => 'Hotels, restaurants, catering, and entertainment venues',
            'Logistics' => 'Transportation, shipping, warehousing, and supply chain services',
            'Manufacturing' => 'Product creation, assembly, industrial production, and fabrication',
            'Real Estate' => 'Property development, sales, rentals, and management',
            'Retail' => 'Consumer products, stores, e-commerce platforms, and retail services',
            'Marketing' => 'Advertising, PR, digital marketing, and branding services'
        ];

        foreach ($categories as $name => $description) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($name)
            ]);
        }

        $this->command->info('Categories seeded successfully!');
    }
}
