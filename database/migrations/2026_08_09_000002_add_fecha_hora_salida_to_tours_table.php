<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->date('fecha_salida_viaje')->nullable()->after('hora_llegada');
            $table->time('hora_salida_viaje')->nullable()->after('fecha_salida_viaje');
        });
    }

    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn(['fecha_salida_viaje', 'hora_salida_viaje']);
        });
    }
};
