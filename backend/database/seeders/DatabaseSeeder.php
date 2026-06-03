<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('12345678'),
            'acc_type' => 'admin',
            'phone' => '1234567890',
            'avatar' => null,
            'status' => 'active',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'last_seen_at' => now(),
        ]);
        $this->call([
            PermissionSeeder::class,
        ]);
        $this->call([
            ChatPermissionTestSeeder::class,
        ]);
    }
}
