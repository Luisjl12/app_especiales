<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Corde extends Model
{
    protected $table='cordes';

    protected $fillable=['clave_corde', 'nombre_corde', 'fecha_Actualizacion_seccion']; 

    protected function casts(): array
    {
        return ['fecha_actualizacion_seccion' => 'datetime'];
    }

    public function planteles()
    {
        return $this->hasMany(Plantel::class);
    }
}
