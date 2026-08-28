<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();
            $table->foreignId('owner_id')->constrained('owners')->onDelete('cascade'); // Relación directa con el owner
            $table->string('code', 20);
            $table->string('name');
            $table->enum('category', ['activo', 'pasivo', 'patrimonio', 'perdida', 'ganancia']);
            $table->timestamps();

            // Índice único compuesto para que cada owner pueda tener sus propios códigos sin colisionar
            $table->unique(['owner_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};