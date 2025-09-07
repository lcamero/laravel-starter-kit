<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        $permissions = [
            //
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // create roles and assign existing permissions
        $roles = [
            Role::Administrator->value => [
                //
            ],
        ];

        foreach ($roles as $roleName => $permissionNames) {
            $role = \Spatie\Permission\Models\Role::create(['name' => $roleName]);
            $role->givePermissionTo($permissionNames);
        }

        // Seed your first administrator
        if (! app()->environment('production')) {
            $admininistrator = User::create([
                'name' => 'Admini Strator',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);

            $admininistrator->assignRole(Role::Administrator->value);
        }
    }
};
