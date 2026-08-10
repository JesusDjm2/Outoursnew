<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('itineraries', function (Blueprint $table) {
            $table->foreignId('destino_id')->nullable()->after('subcategoria_id')->constrained('destinos');
            $table->foreignId('categoria_id')->nullable()->after('subcategoria_id')->constrained('categorias');
        });

        DB::table('itineraries')->whereNotNull('subcategoria_id')->orderBy('id')->get()->each(function ($itinerary) {
            $sub = DB::table('subcategorias')->find($itinerary->subcategoria_id);
            if (!$sub) {
                return;
            }
            $categoria = DB::table('categorias')->find($sub->categoria_id);
            DB::table('itineraries')->where('id', $itinerary->id)->update([
                'categoria_id' => $sub->categoria_id,
                'destino_id' => $categoria?->destino_id,
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('itineraries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('categoria_id');
            $table->dropConstrainedForeignId('destino_id');
        });
    }
};
