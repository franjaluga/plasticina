<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('owners', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();
            $table->string('rut', 10)->unique();
            $table->string('name', 100);
            $table->boolean('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('owners');
    }
};