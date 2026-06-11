<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoCategoria extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'url'];
    public function videos()
    {
        return $this->hasMany(Video::class, 'categoria_id');
    }
}
