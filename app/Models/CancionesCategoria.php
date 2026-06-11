<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CancionesCategoria extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
        'url',
    ];
    public function canciones()
    {
        return $this->hasMany(Canciones::class, 'categoria_id');
    }
}
