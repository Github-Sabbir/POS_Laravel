<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductBarcode;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Setting;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $permissionList = [['Dashboard View', 'dashboard.view', 'Dashboard'], ['POS Access', 'pos.access', 'POS'], ['Product View', 'products.view', 'Products'], ['Product Manage', 'products.manage', 'Products'], ['Product Archive', 'products.archive', 'Products'], ['Product Restore', 'products.restore', 'Products'], ['Barcodes Manage', 'barcodes.manage', 'Barcodes'], ['Purchases Manage', 'purchases.manage', 'Purchases'], ['Sales View', 'sales.view', 'Sales'], ['Receipt Print', 'receipts.print', 'Sales'], ['Sales Return View', 'returns.view', 'Returns'], ['Sales Return Create', 'returns.create', 'Returns'], ['Customers View', 'customers.view', 'Customers'], ['Customer Add', 'customers.create', 'Customers'], ['Customer Edit', 'customers.edit', 'Customers'], ['Customer Delete', 'customers.delete', 'Customers'], ['Customer Payment', 'customers.payment', 'Customers'], ['Suppliers View', 'suppliers.view', 'Suppliers'], ['Supplier Add', 'suppliers.create', 'Suppliers'], ['Supplier Edit', 'suppliers.edit', 'Suppliers'], ['Supplier Delete', 'suppliers.delete', 'Suppliers'], ['Supplier Payment', 'suppliers.payment', 'Suppliers'], ['Expense View', 'expenses.view', 'Expenses'], ['Expense Add', 'expenses.create', 'Expenses'], ['Expense Edit', 'expenses.edit', 'Expenses'], ['Expense Delete', 'expenses.delete', 'Expenses'], ['Reports View', 'reports.view', 'Reports'], ['User View', 'users.view', 'Users'], ['User Add', 'users.create', 'Users'], ['User Edit', 'users.edit', 'Users'], ['User Delete', 'users.delete', 'Users'], ['Roles Manage', 'roles.manage', 'System'], ['Activity Logs View', 'activity.view', 'System'], ['Backup Manage', 'backup.manage', 'System'], ['Settings Manage', 'settings.manage', 'System']];
        $permissions = collect($permissionList)->mapWithKeys(function ($p) {
            $permission = Permission::updateOrCreate(['slug' => $p[1]], ['name' => $p[0], 'group' => $p[2]]);
            return [$permission->slug => $permission];
        });
        $admin = Role::updateOrCreate(['slug' => 'admin'], ['name' => 'Admin', 'description' => 'Full system access', 'is_system' => true]);
        $manager = Role::updateOrCreate(['slug' => 'manager'], ['name' => 'Manager', 'description' => 'Operations and reporting access', 'is_system' => true]);
        $cashier = Role::updateOrCreate(['slug' => 'cashier'], ['name' => 'Cashier', 'description' => 'POS, customer and sales-return operations', 'is_system' => true]);
        $sales = Role::updateOrCreate(['slug' => 'sales-staff'], ['name' => 'Sales Staff', 'description' => 'Sales and POS access', 'is_system' => true]);
        $admin->permissions()->sync($permissions->pluck('id')->all());
        $manager->permissions()->sync($permissions->except(['users.view', 'users.create', 'users.edit', 'users.delete', 'roles.manage', 'settings.manage', 'backup.manage'])->pluck('id')->all());
        $cashier->permissions()->sync($permissions->only(['dashboard.view', 'pos.access', 'products.view', 'sales.view', 'receipts.print', 'returns.view', 'returns.create', 'customers.view', 'customers.create', 'customers.payment'])->pluck('id')->all());
        $sales->permissions()->sync($permissions->only(['dashboard.view', 'pos.access', 'products.view', 'sales.view', 'receipts.print', 'returns.view', 'returns.create', 'customers.view', 'customers.create'])->pluck('id')->all());
        User::updateOrCreate(['email' => 'admin@example.com'], ['name' => 'Administrator', 'password' => 'password', 'role' => 'admin', 'role_id' => $admin->id, 'active' => true]);
        User::updateOrCreate(['email' => 'manager@example.com'], ['name' => 'Manager', 'password' => 'password', 'role' => 'manager', 'role_id' => $manager->id, 'active' => true]);
        User::updateOrCreate(['email' => 'cashier@example.com'], ['name' => 'Cashier', 'password' => 'password', 'role' => 'cashier', 'role_id' => $cashier->id, 'active' => true]);
        User::updateOrCreate(['email' => 'sales@example.com'], ['name' => 'Sales Staff', 'password' => 'password', 'role' => 'sales-staff', 'role_id' => $sales->id, 'active' => true]);
        $cat = Category::firstOrCreate(['name' => 'Grocery']);
        $brand = Brand::firstOrCreate(['name' => 'Demo Brand']);
        $products = [['A Company Oil 1L', 180, 150, 100], ['Rice 1kg', 75, 60, 100], ['Sugar 1kg', 120, 95, 80], ['Milk 1L', 95, 75, 50]];
        foreach ($products as $i => $item) {
            $product = Product::firstOrCreate(['name' => $item[0]], ['category_id' => $cat->id, 'brand_id' => $brand->id, 'sku' => 'DEM-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT), 'unit' => 'pcs', 'purchase_price' => $item[2], 'selling_price' => $item[1], 'wholesale_price' => $item[1], 'current_stock' => $item[3], 'minimum_stock' => 10, 'status' => 'active']);
            ProductBarcode::firstOrCreate(['barcode' => '89000000000' . ($i + 1)], ['product_id' => $product->id, 'is_primary' => true]);
        }
        Customer::firstOrCreate(['name' => 'Walk-in Customer']);
        Supplier::firstOrCreate(['name' => 'Demo Supplier', 'company' => 'Demo Supply']);
        Setting::updateOrCreate(['key' => 'shop_name'], ['value' => 'Retail POS']);
        Setting::updateOrCreate(['key' => 'currency_symbol'], ['value' => '৳']);
        Setting::updateOrCreate(['key' => 'tax'], ['value' => '0']);
        Setting::updateOrCreate(['key' => 'receipt_width'], ['value' => '80mm']);
    }
}
