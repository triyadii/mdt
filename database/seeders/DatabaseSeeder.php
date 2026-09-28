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
        $adminRole = Role::create([
            'namaRole' => 'Admin'
        ]);

        $userRole = Role::create([
            'namaRole' => 'User'
        ]);

        // Membuat User Admin
        User::create([
            'username' => 'admin',
            'password' => Hash::make('password'),
            'nama' => 'Administrator',
            'role' => $adminRole->uuid,
            'status' => 'active'
        ]);

        // Membuat User Biasa
        User::create([
            'username' => 'user',
            'password' => Hash::make('password'),
            'nama' => 'User Default',
            'role' => $userRole->uuid,
            'status' => 'active'
        ]);

        $this->call(ManagementSeeder::class);
    }
}
