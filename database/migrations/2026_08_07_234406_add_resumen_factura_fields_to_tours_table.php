<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->decimal('precio_adicional', 10, 2)->default(0)->after('precio');
            $table->decimal('descuento_especial', 10, 2)->default(0)->after('precio_adicional');
            $table->text('notas_adicionales')->nullable()->after('descuento_especial');
            $table->unsignedTinyInteger('reserva_pct')->default(30)->after('notas_adicionales');
        });
    }

    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn(['precio_adicional', 'descuento_especial', 'notas_adicionales', 'reserva_pct']);
        });
    }
};
