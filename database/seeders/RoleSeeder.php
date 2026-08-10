<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $superAdmin = Role::create(['name' => 'Super Administrador']);
        $admin = Role::create(['name' => 'Administrador']);
        $agencia = Role::create(['name' => 'Agencia']);
        $reservas = Role::create(['name' => 'Reservas']);
        $contabilidad = Role::create(['name' => 'Contabilidad']);

        // Create default superadmin user
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole($superAdmin);

        // Create default admin user
        $user2 = User::create([
            'name' => 'Administrador',
            'email' => 'admin2@admin.com',
            'password' => bcrypt('password'),
        ]);
        $user2->assignRole($admin);

        // Create demo agencia user
        $agenciaUser = User::create([
            'name' => 'Agencia Demo',
            'email' => 'agencia@admin.com',
            'password' => bcrypt('password'),
            'ruc' => '20123456789',
            'telefono' => '999888777',
            'colores' => ['#0ea5e9', '#f59e0b', '#10b981'],
        ]);
        $agenciaUser->assignRole($agencia);
    }
}
