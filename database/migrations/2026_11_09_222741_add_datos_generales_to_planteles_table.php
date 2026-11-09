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
            $table->string('turno', 50)->nullable()->after('nombre_escuela'); 
            $table->string('nivel_educativo', 100)->nullable()->after('turno'); 
            $table->string('sostenimiento', 100)->nullable()->after('nivel_educativo'); 
            $table->string('domicilio_calle_numero')->nullable()->after('sostenimiento'); 
            $table->string('domicilio_colonia')->nullable()->after('domicilio_calle_numero'); 
            $table->string('domicilio_cp', 10)->nullable()->after('domicilio_colonia'); 
            $table->string('telefono_plantel', 50)->nullable()->after('domicilio_cp'); 
            $table->string('correo_institucional')->nullable()->after('telefono_plantel'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planteles', function (Blueprint $table) {
            $table->dropColumn([
                'turno', 'nivel_educativo', 'sostenimiento', 
                'domicilio_calle_numero', 'domicilio_colonia', 'domilio_cp', 
                'telefono_plantel', 'correo_institucional'
            ]); 
        });
    }
};
