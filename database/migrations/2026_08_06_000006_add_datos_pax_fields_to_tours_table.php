<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->string('agente')->nullable()->after('agencia_id');
            $table->string('nombre_pax')->nullable()->after('agente');
            $table->unsignedSmallInteger('edad_pax')->nullable()->after('nombre_pax');
            $table->string('contacto_pax')->nullable()->after('edad_pax');
            $table->string('canal')->nullable()->after('contacto_pax');
            $table->date('fecha_cotizacion')->nullable()->after('canal');
            $table->unsignedSmallInteger('pax_adultos')->nullable()->after('fecha_cotizacion');
            $table->unsignedSmallInteger('pax_ninos')->nullable()->after('pax_adultos');
            $table->string('pais')->nullable()->after('pax_ninos');
            $table->string('codigo_pais')->nullable()->after('pais');
            $table->string('departamento_estado')->nullable()->after('codigo_pais');
            $table->date('fecha_llegada')->nullable()->after('departamento_estado');
            $table->time('hora_llegada')->nullable()->after('fecha_llegada');
        });
    }

    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn([
                'agente', 'nombre_pax', 'edad_pax', 'contacto_pax', 'canal',
                'fecha_cotizacion', 'pax_adultos', 'pax_ninos', 'pais',
                'codigo_pais', 'departamento_estado', 'fecha_llegada', 'hora_llegada',
            ]);
        });
    }
};
