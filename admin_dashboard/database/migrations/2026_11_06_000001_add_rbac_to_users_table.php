<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['super_admin', 'dinas_admin', 'staff'])
                  ->default('staff')
                  ->after('password');
            $table->enum('dinas', ['kominfo', 'pariwisata', 'dukcapil'])
                  ->nullable()
                  ->after('role');
            $table->index(['role', 'dinas']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role', 'dinas']);
            $table->dropColumn(['role', 'dinas']);
        });
    }
};

