<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $permissionList = [
            ['Dashboard View', 'dashboard.view', 'Dashboard'],
            ['POS Access', 'pos.access', 'POS'],
            ['Product View', 'products.view', 'Products'],
            ['Product Manage', 'products.manage', 'Products'],
            ['Product Archive', 'products.archive', 'Products'],
            ['Product Restore', 'products.restore', 'Products'],
            ['Barcodes Manage', 'barcodes.manage', 'Barcodes'],
            ['Purchases Manage', 'purchases.manage', 'Purchases'],
            ['Sales View', 'sales.view', 'Sales'],
            ['Receipt Print', 'receipts.print', 'Sales'],
            ['Sales Return View', 'returns.view', 'Returns'],
            ['Sales Return Create', 'returns.create', 'Returns'],
            ['Customers View', 'customers.view', 'Customers'],
            ['Customer Add', 'customers.create', 'Customers'],
            ['Customer Edit', 'customers.edit', 'Customers'],
            ['Customer Delete', 'customers.delete', 'Customers'],
            ['Customer Payment', 'customers.payment', 'Customers'],
            ['Suppliers View', 'suppliers.view', 'Suppliers'],
            ['Supplier Add', 'suppliers.create', 'Suppliers'],
            ['Supplier Edit', 'suppliers.edit', 'Suppliers'],
            ['Supplier Delete', 'suppliers.delete', 'Suppliers'],
            ['Supplier Payment', 'suppliers.payment', 'Suppliers'],
            ['Expense View', 'expenses.view', 'Expenses'],
            ['Expense Add', 'expenses.create', 'Expenses'],
            ['Expense Edit', 'expenses.edit', 'Expenses'],
            ['Expense Delete', 'expenses.delete', 'Expenses'],
            ['Reports View', 'reports.view', 'Reports'],
            ['User View', 'users.view', 'Users'],
            ['User Add', 'users.create', 'Users'],
            ['User Edit', 'users.edit', 'Users'],
            ['User Delete', 'users.delete', 'Users'],
            ['Roles Manage', 'roles.manage', 'System'],
            ['Activity Logs View', 'activity.view', 'System'],
            ['Backup Manage', 'backup.manage', 'System'],
            ['Settings Manage', 'settings.manage', 'System'],
        ];

        $permissions = collect($permissionList)->mapWithKeys(function ($p) {
            $permission = Permission::updateOrCreate(
                ['slug' => $p[1]],
                ['name' => $p[0], 'group' => $p[2]]
            );
            return [$permission->slug => $permission];
        });

        $admin = Role::updateOrCreate(['slug' => 'admin'], [
            'name' => 'Admin', 'description' => 'Full system access', 'is_system' => true,
        ]);
        $manager = Role::updateOrCreate(['slug' => 'manager'], [
            'name' => 'Manager', 'description' => 'Operations and reporting access', 'is_system' => true,
        ]);
        $cashier = Role::updateOrCreate(['slug' => 'cashier'], [
            'name' => 'Cashier', 'description' => 'POS, customer and sales-return operations', 'is_system' => true,
        ]);
        $sales = Role::updateOrCreate(['slug' => 'sales-staff'], [
            'name' => 'Sales Staff', 'description' => 'Sales and POS access', 'is_system' => true,
        ]);

        $admin->permissions()->sync($permissions->pluck('id')->all());
        $manager->permissions()->sync($permissions->except([
            'users.view', 'users.create', 'users.edit', 'users.delete', 'roles.manage', 'settings.manage', 'backup.manage',
        ])->pluck('id')->all());
        $cashier->permissions()->sync($permissions->only([
            'dashboard.view', 'pos.access', 'products.view', 'sales.view', 'receipts.print',
            'returns.view', 'returns.create', 'customers.view', 'customers.create', 'customers.payment',
        ])->pluck('id')->all());
        $sales->permissions()->sync($permissions->only([
            'dashboard.view', 'pos.access', 'products.view', 'sales.view', 'receipts.print',
            'returns.view', 'returns.create', 'customers.view', 'customers.create',
        ])->pluck('id')->all());

        // Factory/default reset account. This is the ONLY login restored by a factory reset.
        // The Admin may edit this account later, but a factory reset always restores these credentials.
        User::updateOrCreate(['login_id' => 'MdAbdulAlimSabbir'], [
            'name' => 'Administrator',
            'email' => 'admin@retailpos.local',
            'password' => 'MdAbdulAlimSabbir',
            'role' => 'admin',
            'role_id' => $admin->id,
            'active' => true,
            'phone' => null, 'whatsapp' => null, 'image' => null, 'address' => null,
        ]);

        // Factory reset intentionally does not recreate any other user account.

        Setting::updateOrCreate(['key' => 'shop_name'], ['value' => 'Retail POS']);
        Setting::updateOrCreate(['key' => 'currency_symbol'], ['value' => '৳']);
        Setting::updateOrCreate(['key' => 'receipt_width'], ['value' => '80mm']);
    }
}
