<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Agregar el campo de plan de cuentas a los owners
        Schema::table('owners', function (Blueprint $table) {
            if (!Schema::hasColumn('owners', 'account_plan_type')) {
                $table->string('account_plan_type')->default('standard_pyme')->after('name');
            }
        });

        // 2. Tabla para almacenar los perfiles o planes maestros
        Schema::create('account_templates', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); 
            $table->string('name'); 
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 3. Tabla para almacenar las cuentas que componen cada plantilla maestra
        Schema::create('account_template_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_template_id')->constrained('account_templates')->onDelete('cascade');
            $table->string('code', 20);
            $table->string('name', 150);
            $table->string('category', 50); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_template_items');
        Schema::dropIfExists('account_templates');

        if (Schema::hasTable('owners') && Schema::hasColumn('owners', 'account_plan_type')) {
            Schema::table('owners', function (Blueprint $table) {
                $table->dropColumn('account_plan_type');
            });
        }
    }
};