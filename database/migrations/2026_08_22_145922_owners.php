<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('owners', function (Blueprint $table) {
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
