<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DataSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        $definitions = [
            [
                'name' => 'Abdullateef Olalekan Dada',
                'email' => 'mirrorageconcepts@gmail.com',
                'phone' => '735627734',
                'address' => 'No 2 Raji Olaosebikan Doherty Street, Atari Area, Offa, Kwara State.',
                'role' => 'Super Admin',
            ],
            [
                'name' => 'Barakat Olamide Oyebode',
                'email' => 'oyeola2000@gmail.com',
                'phone' => '07016903366',
                'address' => 'Admin Office, Kwara State',
                'role' => 'Admin',
            ],
        ];

        $keptEmails = [];

        foreach ($definitions as $definition) {
            $user = User::updateOrCreate(
                ['email' => $definition['email']],
                [
                    'name' => $definition['name'],
                    'phone' => $definition['phone'],
                    'address' => $definition['address'],
                    'password' => $password,
                    'email_verified_at' => now(),
                ]
            );

            $user->syncRoles([$definition['role']]);
            $keptEmails[] = $definition['email'];
        }

        User::query()
            ->whereNotIn('email', $keptEmails)
            ->delete();
    }
}
