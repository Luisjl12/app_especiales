<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role; 
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; 

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insertOrIgnore([
            ['id' => Role::ADMIN,   'name' => 'admin',   'description' => 'Super usuario',  'created_at' => now(), 'updated_at' => now()],
            ['id' => Role::USUARIO, 'name' => 'usuario', 'description' => 'Usuario normal', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $admin = User::updateOrCreate(
            ['email' => 'admin@demo.com'],
            ['name' => 'Administrador', 'password' => Hash::make('12345678')]
        );
        $admin->forceFill(['role_id' => Role::ADMIN])->save();
    }
}
