<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('planteles', function (Blueprint $table) {
            $table->integer('total_alumnos')->default(0)->after('accesibilidad_otros'); 
            $table->integer('total_docentes')->default(0)->after('total_alumnos'); 
            $table->integer('total_administrativos')->default(0)->after('total_docentes'); 
            $table->decimal('latitud', 10,8)->nullable()->after('total_administrativos'); 
            $table->decimal('longitud', 11,8)->nullable()->after('latitud'); 
        });
    }

    public function down(): void
    {
        Schema::table('planteles', function (Blueprint $table) {
            $table->dropColumn(['total_alumnos', 'total_docentes', 'total_administrativos', 'latitud', 'longitud']); 
        });
    }
};
