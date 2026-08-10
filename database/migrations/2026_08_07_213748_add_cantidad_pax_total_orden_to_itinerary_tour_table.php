<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('itinerary_tour', function (Blueprint $table) {
            $table->unsignedInteger('cantidad_pax')->nullable()->after('itinerary_id');
            $table->decimal('total', 10, 2)->nullable()->after('cantidad_pax');
            $table->unsignedInteger('orden')->default(0)->after('total');
        });
    }

    public function down(): void
    {
        Schema::table('itinerary_tour', function (Blueprint $table) {
            $table->dropColumn(['cantidad_pax', 'total', 'orden']);
        });
    }
};
