<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->string('phone', 50)->nullable()->after('email');
            $t->string('whatsapp', 50)->nullable()->after('phone');
            $t->string('image')->nullable()->after('whatsapp');
            $t->text('address')->nullable()->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->dropColumn(['phone', 'whatsapp', 'image', 'address']);
        });
    }
};
