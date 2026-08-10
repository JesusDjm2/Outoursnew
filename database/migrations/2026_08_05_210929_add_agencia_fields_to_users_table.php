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
            $table->string('ruc')->nullable()->after('email');
            $table->string('telefono')->nullable()->after('ruc');
            $table->string('logo_path')->nullable()->after('telefono');
            $table->json('colores')->nullable()->after('logo_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ruc', 'telefono', 'logo_path', 'colores']);
        });
    }
};
