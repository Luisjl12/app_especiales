<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plantel extends Model
{
    protected $table = 'planteles';

    protected $fillable = 
    [
        'cct',
        'nombre_escuela',
        'localidad_id',
        'corde_id', 
        'turno', 
        'nivel_educativo', 
        'sostenimiento', 
        'domicilio_calle_numero', 
        'domicilio_colonia', 
        'domicilio_cp', 
        'telefono_plantel', 
        'correo_institucional',
        'accesibilidad_rampas', 
        'accesibilidad_banos_adaptados', 
        'accesibilidad_senaletica_braille', 
        'accesibilidad_otros', 
        'total_alumnos', 
        'total_docentes', 
        'total_administrativos', 
        'latitud', 
        'longitud', 
    ];

    public function localidad()
    {
        return $this->belongsTo(Localidad::class);
    }

    public function corde()
    {
        return $this->belongsTo(Corde::class);
    }

    public function casts():array
    {
        return [
            'accesibilidad_ramppas'=>'boolean', 
            'accesibilidad_banos_adaptados'=>'boolean', 
            'accesibilidad_senaletica_braile'=>'boolean', 
            'total_alumnos'=>'integer', 
            'total_docentes'=>'integer', 
            'total_administrativos'=>'integer', 
            'latitud'=>'float', 
            'longitud'=>'float',
        ]; 
    }
}
