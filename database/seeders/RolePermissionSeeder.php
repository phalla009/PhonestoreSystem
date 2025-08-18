<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['permission_name' => 'Dashboard', 'slug' => 'dashboard'],
            ['permission_name' => 'Orders', 'slug' => 'orders'],
            ['permission_name' => 'Customers', 'slug' => 'customers'],
            ['permission_name' => 'Payments', 'slug' => 'payments'],
            ['permission_name' => 'Products', 'slug' => 'products'],
            ['permission_name' => 'Categories', 'slug' => 'categories'],
            ['permission_name' => 'Inventory', 'slug' => 'inventory'],
            ['permission_name' => 'Reports', 'slug' => 'reports'],
            ['permission_name' => 'Settings', 'slug' => 'settings'],
            ['permission_name' => 'UserRoles', 'slug' => 'userroles'],
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(['slug' => $perm['slug']], $perm);
        }

        $roles = [
            ['role_name' => 'Admin', 'description' => 'Full access'],
            ['role_name' => 'Staff', 'description' => 'Dashboard, Orders, Customers, Payments'],
            ['role_name' => 'Stock', 'description' => 'Dashboard, Products, Categories, Inventory, Reports'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['role_name' => $role['role_name']], $role);
        }

        Role::where('role_name', 'Admin')->first()->permissions()->sync(Permission::pluck('id'));
        Role::where('role_name', 'Staff')->first()->permissions()->sync(
            Permission::whereIn('slug', ['dashboard','orders','customers','payments'])->pluck('id')
        );
        Role::where('role_name', 'Stock')->first()->permissions()->sync(
            Permission::whereIn('slug', ['products','categories','inventory','reports'])->pluck('id')
        );
    }
}
