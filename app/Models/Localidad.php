<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Localidad extends Model
{
    protected $table = 'localidades';

    protected $fillable = ['municipio_id', 'nombre'];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function planteles()
    {
        return $this->hasMany(Plantel::class);
    }
}
