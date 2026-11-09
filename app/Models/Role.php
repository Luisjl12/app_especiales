<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public const ADMIN = 1; 
    public const USUARIO=2; 

    protected $fillable = ['name', 'description']; 

    public function users()
    {
        return $this->hasMany(User::class); 
    }
}
