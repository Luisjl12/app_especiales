<?php

namespace Database\Seeders;

use App\Models\Municipio;
use Illuminate\Database\Seeder;

class CatalogoSeeder extends Seeder
{
    public function run(): void
    {
        $tehuacan = Municipio::firstOrCreate(['nombre' => 'Tehuacán']);

        foreach (['Tehuacán', 'Santa María Coapan', 'San Diego Chalma', 'San Cristóbal Tepeteopan'] as $nombre) {
            $tehuacan->localidades()->firstOrCreate(['nombre' => $nombre]);
        }
    }
}
