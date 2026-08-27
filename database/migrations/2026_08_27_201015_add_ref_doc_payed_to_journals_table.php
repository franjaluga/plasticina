<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->unsignedBigInteger('ref_doc_payed')->nullable()->after('vc_document_id');
            
            // Opcional pero recomendado: llave foránea para mantener integridad referencial
            $table->foreign('ref_doc_payed')
                  ->references('id')
                  ->on('vc_documents')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropForeign(['ref_doc_payed']);
            $table->dropColumn('ref_doc_payed');
        });
    }
};