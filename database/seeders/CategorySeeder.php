<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Pemasukan
            ['name' => 'Gaji', 'type' => 'income', 'icon' => 'briefcase', 'color' => '#22C55E'],
            ['name' => 'Freelance', 'type' => 'income', 'icon' => 'laptop', 'color' => '#3B82F6'],
            ['name' => 'Bonus', 'type' => 'income', 'icon' => 'gift', 'color' => '#A855F7'],
            ['name' => 'Bisnis', 'type' => 'income', 'icon' => 'store', 'color' => '#F59E0B'],
            ['name' => 'Investasi', 'type' => 'income', 'icon' => 'trending-up', 'color' => '#14B8A6'],
            ['name' => 'Lainnya', 'type' => 'income', 'icon' => 'wallet', 'color' => '#64748B'],

            // Pengeluaran
            ['name' => 'Makanan', 'type' => 'expense', 'icon' => 'utensils', 'color' => '#F97316'],
            ['name' => 'Transportasi', 'type' => 'expense', 'icon' => 'car', 'color' => '#3B82F6'],
            ['name' => 'Belanja', 'type' => 'expense', 'icon' => 'shopping-cart', 'color' => '#A855F7'],
            ['name' => 'Tagihan', 'type' => 'expense', 'icon' => 'receipt', 'color' => '#EF4444'],
            ['name' => 'Kesehatan', 'type' => 'expense', 'icon' => 'heart-pulse', 'color' => '#EC4899'],
            ['name' => 'Pendidikan', 'type' => 'expense', 'icon' => 'graduation-cap', 'color' => '#0EA5E9'],
            ['name' => 'Hiburan', 'type' => 'expense', 'icon' => 'gamepad-2', 'color' => '#8B5CF6'],
            ['name' => 'Tempat Tinggal', 'type' => 'expense', 'icon' => 'house', 'color' => '#D97706'],
            ['name' => 'Lainnya', 'type' => 'expense', 'icon' => 'circle-dollar-sign', 'color' => '#64748B'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                [
                    'user_id' => null,
                    'name' => $category['name'],
                    'type' => $category['type'],
                ],
                [
                    'icon' => $category['icon'],
                    'color' => $category['color'],
                    'is_default' => true,
                    'is_active' => true,
                ]
            );
        }
    }
}
