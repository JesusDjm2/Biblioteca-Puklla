<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Canciones extends Model
{
    use HasFactory;

    /** Plataformas donde puede estar publicada una canción (una por canción). */
    public const PLATAFORMAS = ['youtube', 'drive', 'spotify'];

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

    /** Plataforma en la que está la canción: youtube, drive, spotify o null. */
    public function getPlataformaAttribute(): ?string
    {
        foreach (self::PLATAFORMAS as $p) {
            if (filled($this->{$p})) {
                return $p;
            }
        }

        return null;
    }

    /** Enlace para abrir la canción en su plataforma. */
    public function getEnlaceAttribute(): ?string
    {
        return match ($this->plataforma) {
            'youtube' => 'https://www.youtube.com/watch?v='.$this->youtube,
            'drive' => 'https://drive.google.com/file/d/'.$this->drive.'/view',
            'spotify' => 'https://open.spotify.com/track/'.$this->spotify,
            default => null,
        };
    }

    /** Acepta un ID de pista de Spotify o su enlace (open.spotify.com/track/ID o spotify:track:ID). */
    public static function extraerSpotifyId(?string $valor): ?string
    {
        $valor = trim((string) $valor);
        if ($valor === '') {
            return null;
        }

        if (preg_match('~(?:track[/:])([A-Za-z0-9]{22})~', $valor, $m)) {
            return $m[1];
        }

        return $valor;
    }
}
