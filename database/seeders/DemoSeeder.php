<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command->alert('Demo not support for production.');
            return;
        }

        $this->call([
            UserSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
