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
            $table->string('codigo', 20)->nullable()->after('nombre');
        });

        DB::table('itineraries')->whereNull('codigo')->orderBy('id')->get(['id', 'nombre'])->each(function ($itinerary) {
            if (!preg_match('/^([A-Z]{2,6})\s*\|\s*(.+)$/u', trim($itinerary->nombre), $matches)) {
                return;
            }

            DB::table('itineraries')->where('id', $itinerary->id)->update([
                'codigo' => $matches[1],
                'nombre' => trim($matches[2]),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('itineraries', function (Blueprint $table) {
            $table->dropColumn('codigo');
        });
    }
};
