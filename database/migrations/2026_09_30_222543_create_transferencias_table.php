<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transferencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_cuenta_id')->constrained('cuentas')->cascadeOnDelete();
            $table->foreignId('to_cuenta_id')->constrained('cuentas')->cascadeOnDelete();
            $table->decimal('monto_origen', 15, 2);
            $table->decimal('monto_destino', 15, 2);
            $table->string('moneda_origen', 3);
            $table->string('moneda_destino', 3);
            $table->decimal('tipo_cambio', 18, 8);
            $table->unsignedSmallInteger('anio');
            $table->unsignedTinyInteger('mes');
            $table->date('fecha');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transferencias');
    }
};
