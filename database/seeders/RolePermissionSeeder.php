<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permisos = [
            'ver empleados',
            'crear empleados',
            'editar empleados',
            'borrar empleados',
            'ver permisos',
            'crear permisos',
            'editar permisos',
            'borrar permisos',
        ];

        foreach ($permisos as $permiso){
            Permission::firstOrCreate(['name'=>$permiso]);
        }

        $admin = Role::firsOrCreate(['name' => 'Administrador']);
        $admin->givePermissionTo(Permission::all());

        $usuario = Role::firstOrCreate(['name'=>'Usuario']);
        $usuario->givePermissionTo(['ver empleados','ver permisos']);

    }
}
