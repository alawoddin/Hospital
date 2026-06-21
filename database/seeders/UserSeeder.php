<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            ['name' => 'admin', 'password' => Hash::make('111'), 'role' => 'admin']
        );

        User::updateOrCreate(
            ['email' => 'doctor@gmail.com'],
            ['name' => 'Neurology Doctor', 'password' => Hash::make('111'), 'role' => 'doctor', 'consultation_fee' => 300]
        );

        User::updateOrCreate(
            ['email' => 'opd@gmail.com'],
            ['name' => 'OPD Doctor', 'password' => Hash::make('111'), 'role' => 'doctor', 'consultation_fee' => 200]
        );

        User::updateOrCreate(
            ['email' => 'recieption@gmail.com'],
            ['name' => 'recieption', 'password' => Hash::make('111'), 'role' => 'recieption']
        );
        User::updateOrCreate(
            ['email' => 'finance@gmail.com'],
            ['name' => 'finance', 'password' => Hash::make('111'), 'role' => 'finance']
        );
        User::updateOrCreate(
            ['email' => 'pharmacy@gmail.com'],
            ['name' => 'pharmacy', 'password' => Hash::make('111'), 'role' => 'pharmacy']
        );
        User::updateOrCreate(
            ['email' => 'user@gmail.com'],
            ['name' => 'user', 'password' => Hash::make('111'), 'role' => 'user']
        );
    }
}
