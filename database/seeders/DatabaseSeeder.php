<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin NutriScreen',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
        );

        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'User NutriScreen',
                'password' => Hash::make('password'),
                'role' => 'user',
            ],
        );

        $this->call([
            RuleSeeder::class,
        ]);
    }
}
