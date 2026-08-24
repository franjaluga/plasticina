<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();
            $table->unsignedBigInteger('vc_document_id')->nullable();
            $table->foreignId('owner_id')->constrained('owners')->onDelete('restrict');
            $table->unsignedSmallInteger('year');
            $table->unsignedInteger('entry_number');

            $table->date('date');
            $table->decimal('total_debit', 15, 2);
            $table->decimal('total_credit', 15, 2);
            $table->boolean('is_balanced')->default(false);
            $table->timestamps();

            $table->foreign('vc_document_id')
                  ->references('id')
                  ->on('vc_documents')
                  ->onDelete('cascade');

            $table->unique(['owner_id', 'year', 'entry_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};