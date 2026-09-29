<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sobre_meses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sobre_id')->constrained('sobres')->cascadeOnDelete();
            $table->unsignedSmallInteger('anio');
            $table->unsignedTinyInteger('mes');
            $table->decimal('assigned', 15, 2);
            $table->decimal('activity', 15, 2);
            $table->decimal('available', 15, 2);
            $table->timestamps();

            $table->unique(['sobre_id', 'anio', 'mes']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sobre_meses');
    }
};
