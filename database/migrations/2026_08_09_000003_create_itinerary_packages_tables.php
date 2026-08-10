<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itinerary_packages', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->unsignedInteger('dias')->default(1);
            $table->timestamps();
        });

        Schema::create('itinerary_package_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itinerary_package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('itinerary_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('dia')->default(1);
            $table->unsignedInteger('cantidad_pax_defecto')->nullable();
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itinerary_package_items');
        Schema::dropIfExists('itinerary_packages');
    }
};
