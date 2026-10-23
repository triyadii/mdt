<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
//
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    //

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Membuat Master Role
        $adminRole = Role::firstOrCreate([
            'namaRole' => 'Admin',
        ]);

        $userRole = Role::firstOrCreate([
            'namaRole' => 'User',
        ]);

        // Membuat User Admin
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'password' => Hash::make('password'),
                'nama' => 'Administrator',
                'role' => $adminRole->uuid,
                'status' => 'active',
            ]
        );

        // Membuat User Biasa
        User::firstOrCreate(
            ['username' => 'user'],
            [
                'password' => Hash::make('password'),
                'nama' => 'User Default',
                'role' => $userRole->uuid,
                'status' => 'active',
            ]
        );

        $this->call(ManagementSeeder::class);
    }
}
