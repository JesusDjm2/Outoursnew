<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->string('idioma')->nullable()->after('fecha_fin');
            $table->string('moneda')->default('USD')->after('idioma');
            $table->decimal('precio', 10, 2)->nullable()->after('moneda');
        });
    }

    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn(['idioma', 'moneda', 'precio']);
        });
    }
};
