<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    public function up(): void
    {
        $items = [['Sales Return View', 'returns.view', 'Returns'], ['Sales Return Create', 'returns.create', 'Returns'], ['Reports View', 'reports.view', 'Reports'], ['Receipt Print', 'receipts.print', 'Sales']];
        foreach ($items as [$name, $slug, $group]) {
            DB::table('permissions')->updateOrInsert(['slug' => $slug], ['name' => $name, 'group' => $group, 'updated_at' => now(), 'created_at' => now()]);
        }
        $roleIds = DB::table('roles')->pluck('id', 'slug');
        $permissionIds = DB::table('permissions')->whereIn('slug', array_column($items, 1))->pluck('id', 'slug');
        $grant = function (string $role, array $slugs) use ($roleIds, $permissionIds) {
            if (!isset($roleIds[$role])) {
                return;
            }
            foreach ($slugs as $slug) {
                if (isset($permissionIds[$slug])) {
                    DB::table('permission_role')->updateOrInsert(['permission_id' => $permissionIds[$slug], 'role_id' => $roleIds[$role]], []);
                }
            }
        };
        $grant('admin', array_column($items, 1));
        $grant('manager', ['returns.view', 'returns.create', 'reports.view', 'receipts.print']);
        $grant('cashier', ['returns.view', 'returns.create', 'receipts.print']);
        $grant('sales-staff', ['returns.view', 'returns.create', 'receipts.print']);
    }
    public function down(): void
    {
        $slugs = ['returns.view', 'returns.create', 'reports.view', 'receipts.print'];
        $ids = DB::table('permissions')->whereIn('slug', $slugs)->pluck('id');
        DB::table('permission_role')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->whereIn('slug', $slugs)->delete();
    }
};
