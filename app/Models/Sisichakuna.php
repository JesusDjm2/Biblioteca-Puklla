<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sisichakuna extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
        'youtube',
        'spotify',
        'drive',
        'facebook',
    ];
}
