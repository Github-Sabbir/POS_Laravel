<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('login_id', 120)->nullable()->after('name');
            $table->unique('login_id');
        });

        $users = DB::table('users')->select('id', 'email')->orderBy('id')->get();
        $used = [];
        foreach ($users as $user) {
            $base = preg_replace('/[^A-Za-z0-9_\\-]/', '', (string) Str::before($user->email, '@')) ?: 'user';
            $candidate = $base;
            $n = 1;
            while (isset($used[strtolower($candidate)])) {
                $n++;
                $candidate = $base . $n;
            }
            $used[strtolower($candidate)] = true;
            DB::table('users')->where('id', $user->id)->update(['login_id' => $candidate]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('login_id', 120)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['login_id']);
            $table->dropColumn('login_id');
        });
    }
};
