<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductProperty;
use App\Models\ProductPropertyValue;
use Arr;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class ProductSeeder extends Seeder
{
    private Collection $properties;

    public function run(): void
    {
        if (app()->environment() !== 'testing') {
            Product::query()->delete();
        }

        $this->properties = ProductProperty::get();

        Product::factory(50)
            ->afterCreating(fn(Product $product) => $this->createProductProperties($product))
            ->create();
    }

    private function createProductProperties(Product $product): void
    {
        $values = $this->properties
            ->map(function(ProductProperty $property) use ($product) {
                $value = $this->generateValue($property);
                if ($value === null) {
                    return null;
                }
                return new ProductPropertyValue([
                    'property_id' => $property->id,
                    'value' => $value,
                ]);
            })
            ->filter();

        $product->propertyValues()->saveMany($values);
    }

    private function generateValue(ProductProperty $property): mixed
    {
        return match ($property->slug) {
            ProductProperty::SLUG_COLOR => Arr::random(ProductProperty::COLORS + [null]),
            ProductProperty::SLUG_WEIGHT => rand(0, 100000) / 100 ?: null,
        };
    }
}
