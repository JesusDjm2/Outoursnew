<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hospedajes', function (Blueprint $table) {
            $table->string('estado_reserva')->default('pendiente')->after('fecha_salida');
        });

        Schema::table('proveedor_tour', function (Blueprint $table) {
            $table->string('estado_reserva')->default('pendiente')->after('proveedor_id');
        });
    }

    public function down(): void
    {
        Schema::table('hospedajes', function (Blueprint $table) {
            $table->dropColumn('estado_reserva');
        });

        Schema::table('proveedor_tour', function (Blueprint $table) {
            $table->dropColumn('estado_reserva');
        });
    }
};
