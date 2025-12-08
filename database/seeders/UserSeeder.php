<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = 'admin@saproperty.gm';
        if (!User::where('email', $adminEmail)->exists()) {
                User::factory()->create([
                    'name' => 'Mamadou A Jallow',
                    'email' => $adminEmail,
                    'password' => bcrypt('password'),
                ]);
        }
        User::factory()->count(10)->create([
            'email' => function () use ($adminEmail) {
                do {
                    $email = fake()->unique()->safeEmail();
                } while ($email === $adminEmail);
                return $email;
            },
        ]);
    }
}
