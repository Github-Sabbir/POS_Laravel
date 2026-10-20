<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    public function up(): void
    {
        $roles = [['Admin', 'admin', 'Full system access'], ['Manager', 'manager', 'Operations and reporting access'], ['Cashier', 'cashier', 'POS, customer and sales-return operations'], ['Sales Staff', 'sales-staff', 'Sales and POS access']];
        foreach ($roles as [$name, $slug, $description]) {
            DB::table('roles')->updateOrInsert(['slug' => $slug], ['name' => $name, 'description' => $description, 'is_system' => true, 'updated_at' => now(), 'created_at' => now()]);
        }
        $permissionIds = DB::table('permissions')->pluck('id', 'slug');
        $roleIds = DB::table('roles')->pluck('id', 'slug');
        $sets = ['admin' => array_keys($permissionIds->all()), 'manager' => $permissionIds->keys()->reject(fn($slug) => in_array($slug, ['users.view', 'users.create', 'users.edit', 'users.delete', 'roles.manage', 'settings.manage', 'backup.manage'], true))->all(), 'cashier' => ['dashboard.view', 'pos.access', 'products.view', 'sales.view', 'receipts.print', 'returns.view', 'returns.create', 'customers.view', 'customers.create', 'customers.payment'], 'sales-staff' => ['dashboard.view', 'pos.access', 'products.view', 'sales.view', 'receipts.print', 'returns.view', 'returns.create', 'customers.view', 'customers.create']];
        foreach ($sets as $roleSlug => $slugs) {
            if (!isset($roleIds[$roleSlug])) {
                continue;
            }
            DB::table('permission_role')->where('role_id', $roleIds[$roleSlug])->delete();
            foreach ($slugs as $slug) {
                if (isset($permissionIds[$slug])) {
                    DB::table('permission_role')->insertOrIgnore(['role_id' => $roleIds[$roleSlug], 'permission_id' => $permissionIds[$slug]]);
                }
            }
        }
        foreach ($roleIds as $slug => $roleId) {
            DB::table('users')->where('role', $slug)->update(['role_id' => $roleId]);
        }
    }
    public function down(): void
    {
        // Keep system roles intact on rollback; permissions can be managed from the Roles UI.
    }
};
