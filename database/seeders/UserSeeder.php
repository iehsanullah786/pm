<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Check if roles exist before creating
        $roleNames = ['super_admin', 'staff'];
        foreach ($roleNames as $roleName) {
            if (!Role::where('name', $roleName)->exists()) {
                Role::create(['name' => $roleName, 'guard_name' => 'web']);
            }
        }

        // Create users and assign roles
        $superAdmin = User::create([
            'name' => 'ehsan',
            'email' => 'ehsan@ehsan.co',
            'password' => bcrypt('password'),
        ]);
        \Log::info('Assigning role to user');
        $superAdmin->assignRole('super_admin');


        

    }
}
