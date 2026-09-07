<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->foreignId('destino_id')->nullable()->after('id')->constrained('destinos');
            $table->foreignId('categoria_id')->nullable()->after('destino_id')->constrained('categorias');
        });
    }

    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropConstrainedForeignId('categoria_id');
            $table->dropConstrainedForeignId('destino_id');
        });
    }
};
