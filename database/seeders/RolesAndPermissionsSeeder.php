<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Cliente;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // --- CREACIÓN DE ROLES Y PERMISOS ---
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $clienteRole = Role::firstOrCreate(['name' => 'cliente']);
        
        // Permisos del sistema
        $adminPermissions = [
            'user-list', 'user-create', 'user-edit', 'user-delete', 'user-activate',
            'rol-list', 'rol-create', 'rol-edit', 'rol-delete',
            'categoria-list', 'categoria-create', 'categoria-edit', 'categoria-delete', 'categoria-activate',
            'producto-list', 'producto-create', 'producto-edit', 'producto-delete',
            'pedido-list', 'pedido-anulate',
            'permission-list', 'permission-create', 'permission-edit', 'permission-delete', 'permission-activate',
        ];
        $clientePermissions = [
            'pedido-view', 'pedido-cancel', 'perfil',
        ];

        
        foreach (array_merge($adminPermissions, $clientePermissions) as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }
        
        $adminRole->syncPermissions($adminPermissions);
        $clienteRole->syncPermissions($clientePermissions);
        $superAdminRole->syncPermissions(Permission::all());

        // --- CREACIÓN DE USUARIOS Y ENTIDADES ---

        // Empresas demo
        $empresaDemo = Empresa::firstOrCreate(['slug' => 'empresa-demo'], ['nombre' => 'Empresa Demo']);
        $otraEmpresa = Empresa::firstOrCreate(['slug' => 'otra-empresa'], ['nombre' => 'Otra Empresa']);

        // Usuario Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'super@admin.com'],
            ['name' => 'SuperAdmin', 'password' => Hash::make('super123456'), 'activo' => true]
        );
        $superAdmin->assignRole($superAdminRole);

        // Usuario Admin de Empresa
        $admin = User::firstOrCreate(
            ['email' => 'admin@empresa.com'],
            ['name' => 'Admin Empresa', 'password' => Hash::make('admin123456'), 'activo' => true, 'empresa_id' => $empresaDemo->id]
        );
        $admin->assignRole($adminRole);

        // ==========================================================
        //         NUEVA LÓGICA PARA CREAR EL CLIENTE
        // ==========================================================
        
        // 1. Crear PRIMERO el 'User' que se usará para iniciar sesión.
        $clienteUser = User::firstOrCreate(
            ['email' => 'cliente@prueba.com'], // El email sigue siendo el identificador único del User
            [
                'name' => 'Cliente Prueba',
                'password' => Hash::make('cliente123456'),
                'activo' => true,
                // Un usuario-cliente no pertenece a una empresa, sino que interactúa con ellas.
                'empresa_id' => null, 
            ]
        );
        $clienteUser->assignRole($clienteRole);

        // 2. Crear el perfil 'Cliente' y VINCULARLO al User que acabamos de crear.
        // Ahora usamos 'user_id' como identificador único para el perfil del cliente.
        $clienteRecord = Cliente::firstOrCreate(
            ['user_id' => $clienteUser->id], 
            [
                'nombre' => $clienteUser->name, // Reutilizamos el nombre del User
                'telefono' => '987654321',
            ]
        );

        // 3. (Opcional) Asociar este cliente a una o más empresas.
        // Esto simula que el "Cliente Prueba" ya ha comprado en ambas tiendas.
        $clienteRecord->empresas()->syncWithoutDetaching([$empresaDemo->id, $otraEmpresa->id]);
        
        // ==========================================================
    }
}