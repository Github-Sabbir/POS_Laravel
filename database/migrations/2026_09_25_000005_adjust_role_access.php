<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    public function up(): void
    {
        $cashier = DB::table('roles')->where('slug', 'cashier')->value('id');
        $barcode = DB::table('permissions')->where('slug', 'barcodes.manage')->value('id');
        if ($cashier && $barcode) {
            DB::table('permission_role')->where('role_id', $cashier)->where('permission_id', $barcode)->delete();
        }
    }
    public function down(): void
    {
        $cashier = DB::table('roles')->where('slug', 'cashier')->value('id');
        $barcode = DB::table('permissions')->where('slug', 'barcodes.manage')->value('id');
        if ($cashier && $barcode) {
            DB::table('permission_role')->updateOrInsert(['role_id' => $cashier, 'permission_id' => $barcode], []);
        }
    }
};
