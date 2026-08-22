<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vc_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('month_register');
            $table->unsignedSmallInteger('year_register');
            $table->char('type_vc');

            $table->foreignId('entity_id')->constrained('entities')->onDelete('restrict');
            $table->unsignedSmallInteger('document_type_id');
            $table->foreign('document_type_id')
                ->references('doctype')
                ->on('document_types')
                ->onDelete('restrict');
            
            $table->integer('folio');
            $table->date('date');

            // Hechos nullable para que no exijan datos si no aplican
            $table->string('rut_ref', 10)->nullable();
            $table->integer('folio_ref')->nullable();
            $table->unsignedSmallInteger('td_ref')->nullable();
            $table->date('date_centralize')->nullable();

            $table->bigInteger('net')->default(0);
            $table->bigInteger('exempt')->default(0);
            $table->bigInteger('vat_rec')->default(0);
            $table->bigInteger('vat_no_rec')->default(0);
            $table->bigInteger('plus_oth_tax')->default(0);
            $table->bigInteger('minus_oth_tax')->default(0);
            $table->bigInteger('total')->default(0);

            // Única línea para el owner
            $table->foreignId('owner_id')->constrained('owners')->onDelete('restrict');
            
            $table->index(['year_register', 'month_register', 'type_vc']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vc_documents');
    }
};