<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('itineraries')->whereNotNull('tour_id')->orderBy('id')->get(['id', 'tour_id'])
            ->each(function ($itinerary) {
                DB::table('itinerary_tour')->insertOrIgnore([
                    'tour_id' => $itinerary->tour_id,
                    'itinerary_id' => $itinerary->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

        Schema::table('itineraries', function (Blueprint $table) {
            $table->dropForeign(['tour_id']);
            $table->dropColumn('tour_id');
        });
    }

    public function down(): void
    {
        Schema::table('itineraries', function (Blueprint $table) {
            $table->foreignId('tour_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });

        DB::table('itinerary_tour')->orderBy('id')->get(['tour_id', 'itinerary_id'])
            ->each(function ($pivot) {
                DB::table('itineraries')->where('id', $pivot->itinerary_id)->whereNull('tour_id')
                    ->update(['tour_id' => $pivot->tour_id]);
            });
    }
};
