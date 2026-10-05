<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
return new class extends Migration {
    public function up(): void
    {
        $permission = DB::table('permissions')->where('slug','backup.manage')->first();
        if (!$permission) {
            DB::table('permissions')->insert(['name'=>'Backup Manage','slug'=>'backup.manage','group'=>'System','created_at'=>now(),'updated_at'=>now()]);
        }
        $admin = DB::table('roles')->where('slug','admin')->value('id');
        $pid = DB::table('permissions')->where('slug','backup.manage')->value('id');
        if ($admin && $pid) DB::table('permission_role')->insertOrIgnore(['role_id'=>$admin,'permission_id'=>$pid]);
    }
    public function down(): void {}
};
