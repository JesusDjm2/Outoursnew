<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->decimal('precio_regular', 10, 2)->nullable()->after('numero_habitacion');
            $table->decimal('precio_promo', 10, 2)->nullable()->after('precio_regular');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['precio_regular', 'precio_promo']);
        });
    }
};
