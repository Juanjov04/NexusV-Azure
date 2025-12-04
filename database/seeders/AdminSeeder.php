<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear Administrador Maestro (CRÍTICO)
        User::updateOrCreate(
            ['email' => 'admin@nexusv.com'],
            [
                'name' => 'Admin Maestro',
                'password' => Hash::make('password123'),
                'role' => 'admin-master',
                'email_verified_at' => now(),
            ]
        );

        // 2. Crear un Vendedor de Prueba
        User::updateOrCreate(
            ['email' => 'seller@test.com'],
            [
                'name' => 'Vendedor Demo',
                'password' => Hash::make('password123'),
                'role' => 'seller',
                'email_verified_at' => now(),
            ]
        );

        // 3. Crear un Comprador de Prueba
        User::updateOrCreate(
            ['email' => 'buyer@test.com'],
            [
                'name' => 'Comprador Demo',
                'password' => Hash::make('password123'),
                'role' => 'buyer',
                'email_verified_at' => now(),
            ]
        );
    }
}