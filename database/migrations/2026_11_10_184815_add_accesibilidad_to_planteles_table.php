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
        Schema::table('planteles', function (Blueprint $table) {
            $table->boolean('accesibilidad_rampas')->default(false)->after('correo_institucional'); 
            $table->boolean('accesibilidad_banos_adaptados')->default(false)->after('accesibilidad_rampas'); 
            $table->boolean('accesibilidad_senaletica_braille')->default(false)->after('accesibilidad_banos_adaptados'); 
            $table->text('accesibilidad_otros')->nullable()->after('accesibilidad_senaletica_braille'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planteles', function (Blueprint $table) {
            $table->dropColumn([
                'accesibilidad_rampas', 'accesibilidad_banos_adaptados', 
                'accesibilidad_senaletica_braile', 'accesibilidad_otros', 
            ]);
        });
    }
};
