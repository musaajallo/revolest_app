<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Administrator',
                'email' => 'admin@revolest.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_SUPER_ADMIN,
            ],
            [
                'name' => 'Mamadou Alieu Jallow',
                'email' => 'majallow@revolest.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_SUPER_ADMIN,
            ],
            [
                'name' => 'Lamin Samba',
                'email' => 'lsamba@revolest.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_SUPER_ADMIN,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        // Add 10 random users for testing
        $excludeEmails = array_column($users, 'email');
        User::factory()->count(10)->create([
            'email' => function () use ($excludeEmails) {
                do {
                    $email = fake()->unique()->safeEmail();
                } while (in_array($email, $excludeEmails));
                return $email;
            },
        ]);
    }
}
