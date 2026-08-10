<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('celulares')->nullable()->after('direccion');
            $table->string('whatsapp')->nullable()->after('celulares');
            $table->text('terminos_condiciones_es')->nullable()->after('colores');
            $table->text('terminos_condiciones_en')->nullable()->after('terminos_condiciones_es');
            $table->text('terminos_condiciones_pt')->nullable()->after('terminos_condiciones_en');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'celulares',
                'whatsapp',
                'terminos_condiciones_es',
                'terminos_condiciones_en',
                'terminos_condiciones_pt',
            ]);
        });
    }
};
