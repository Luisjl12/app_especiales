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
            $table->foreignId('corde_id')
                ->nullable()
                ->after('localidad_id')
                ->constrained('cordes'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planteles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('corde_id');
        });
    }
};
