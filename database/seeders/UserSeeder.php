<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment() !== 'testing') {
            User::query()->delete();
        }

        User::factory(10)->create();
    }
}
