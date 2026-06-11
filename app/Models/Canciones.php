<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Canciones extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
        'autor',
        'youtube',
        'drive',
        'spotify',
        'categoria_id',
    ];
    public function categoria()
    {
        return $this->belongsTo(CancionesCategoria::class, 'categoria_id');
    }
}
