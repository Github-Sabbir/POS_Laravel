<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    public function up(): void
    {
        $slugs = ['returns.manage', 'customers.manage', 'suppliers.manage', 'expenses.manage'];
        $ids = DB::table('permissions')->whereIn('slug', $slugs)->pluck('id');
        DB::table('permission_role')->whereIn('permission_id', $ids)->delete();
        DB::table('permissions')->whereIn('slug', $slugs)->delete();
    }
    public function down(): void
    {
        $items = [['Returns', 'returns.manage', 'Sales'], ['Customers', 'customers.manage', 'Customers'], ['Suppliers', 'suppliers.manage', 'Suppliers'], ['Expenses', 'expenses.manage', 'Expenses']];
        foreach ($items as [$name, $slug, $group]) {
            DB::table('permissions')->updateOrInsert(['slug' => $slug], ['name' => $name, 'group' => $group, 'updated_at' => now(), 'created_at' => now()]);
        }
    }
};
