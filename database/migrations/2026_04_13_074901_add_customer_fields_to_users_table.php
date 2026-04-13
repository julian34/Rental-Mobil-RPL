<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Make email optional (customers log in with username)
            $table->string('email')->nullable()->change();

            // Customer-specific fields
            $table->string('username')->unique()->nullable()->after('email');
            $table->string('alamat')->nullable()->after('username');
            $table->string('no_telepon', 20)->nullable()->after('alamat');
            $table->string('no_ktp', 16)->unique()->nullable()->after('no_telepon');
            $table->string('no_sim')->unique()->nullable()->after('no_ktp');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
            $table->dropUnique(['username']);
            $table->dropUnique(['no_ktp']);
            $table->dropUnique(['no_sim']);
            $table->dropColumn(['username', 'alamat', 'no_telepon', 'no_ktp', 'no_sim']);
        });
    }
};
