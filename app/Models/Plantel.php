<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plantel extends Model
{
    protected $table = 'planteles';

    protected $fillable = ['cct', 'nombre_escuela', 'localidad_id', 'corde_id'];

    public function localidad()
    {
        return $this->belongsTo(Localidad::class);
    }

    public function corde()
    {
        return $this->belongsTo(Corde::class);
    }
}
