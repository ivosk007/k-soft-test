<?php

namespace Database\Seeders;

use App\Models\ProductProperty;
use Illuminate\Database\Seeder;
use Throwable;

class ProductPropertySeeder extends Seeder
{
    const LIST = [
        ['name' => 'Color', 'slug' => 'color'],
        ['name' => 'Weight', 'slug' => 'weight'],
    ];

    /**
     * @throws Throwable
     */
    public function run(): void
    {
        foreach (self::LIST as $item) {
            ProductProperty::whereSlug($item['slug'])
                ->firstOrNew()
                ->fill($item)
                ->saveOrFail();
        }
    }
}
