<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'login_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('login_id', 190)->nullable()->unique()->after('name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'login_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['login_id']);
                $table->dropColumn('login_id');
            });
        }
    }
};
