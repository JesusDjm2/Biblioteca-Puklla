<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TaqeTrabajo extends Model
{
    use HasFactory;

    protected $table = 'taqe_trabajos';

    protected $fillable = [
        'categoria_id',
        'titulo',
        'slug',
        'autores',
        'descripcion',
        'formato_principal',
        'idioma',
        'año_periodo',
        'ciclo_carrera',
        'archivo_pdf',
        'archivo_audio',
        'url_video',
        'portada',
        'vistas',
        'descargas',
        'destacado',
        'estado',
    ];

    protected $casts = [
        'vistas' => 'integer',
        'descargas' => 'integer',
        'destacado' => 'boolean',
    ];

    const FORMATOS = [
        'pdf' => ['nombre' => 'Documento PDF', 'icono' => 'fa-file-pdf-o', 'color' => '#b91c1c'],
        'audio' => ['nombre' => 'Audio / Relato', 'icono' => 'fa-volume-up', 'color' => '#0369a1'],
        'video' => ['nombre' => 'Video / Stopmotion', 'icono' => 'fa-video-camera', 'color' => '#6d28d9'],
        'imagen' => ['nombre' => 'Cartilla / Gráfico', 'icono' => 'fa-picture-o', 'color' => '#047857'],
        'multimedia' => ['nombre' => 'Multimedia mixto', 'icono' => 'fa-cubes', 'color' => '#b45309'],
    ];

    const IDIOMAS = [
        'castellano' => ['nombre' => 'Castellano', 'icono' => 'fa-language'],
        'quechua' => ['nombre' => 'Runasimi (Quechua)', 'icono' => 'fa-commenting-o'],
        'bilingue' => ['nombre' => 'Bilingüe (Quechua - Castellano)', 'icono' => 'fa-exchange'],
    ];

    protected static function booted()
    {
        static::creating(function ($trabajo) {
            if (empty($trabajo->slug)) {
                $base = Str::slug($trabajo->titulo);
                $slug = $base;
                $i = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = "{$base}-" . (++$i);
                }
                $trabajo->slug = $slug;
            }
        });
    }

    public function categoria()
    {
        return $this->belongsTo(TaqeCategoria::class, 'categoria_id');
    }

    public function scopePublicados($query)
    {
        return $query->where('estado', 'publicado');
    }

    public function scopeFiltrar($query, array $filtros)
    {
        if (!empty($filtros['q'])) {
            $term = trim($filtros['q']);
            $query->where(function ($q) use ($term) {
                $q->where('titulo', 'like', "%{$term}%")
                    ->orWhere('autores', 'like', "%{$term}%")
                    ->orWhere('descripcion', 'like', "%{$term}%")
                    ->orWhere('ciclo_carrera', 'like', "%{$term}%");
            });
        }

        if (!empty($filtros['categoria_id'])) {
            $query->where('categoria_id', $filtros['categoria_id']);
        }

        if (!empty($filtros['formato'])) {
            $query->where('formato_principal', $filtros['formato']);
        }

        if (!empty($filtros['idioma'])) {
            $query->where('idioma', $filtros['idioma']);
        }

        if (!empty($filtros['periodo'])) {
            $query->where('año_periodo', $filtros['periodo']);
        }

        return $query;
    }

    public function getFormatoInfoAttribute(): array
    {
        return self::FORMATOS[$this->formato_principal] ?? [
            'nombre' => ucfirst($this->formato_principal),
            'icono' => 'fa-file-o',
            'color' => '#6b7280',
        ];
    }

    public function getIdiomaInfoAttribute(): array
    {
        return self::IDIOMAS[$this->idioma] ?? [
            'nombre' => ucfirst($this->idioma),
            'icono' => 'fa-globe',
        ];
    }

    public function getAnioAttribute(): ?string
    {
        return $this->attributes['año_periodo'] ?? null;
    }

    public function getFormatoLabelAttribute(): string
    {
        return $this->formato_info['nombre'] ?? ucfirst($this->formato_principal);
    }

    public function getIconoAttribute(): string
    {
        return 'fa ' . ($this->formato_info['icono'] ?? 'fa-file-o');
    }

    public function getIdiomaLabelAttribute(): string
    {
        return $this->idioma_info['nombre'] ?? ucfirst($this->idioma);
    }

    public function getPortadaUrlAttribute(): string
    {
        if (!empty($this->portada)) {
            if (str_starts_with($this->portada, 'http://') || str_starts_with($this->portada, 'https://')) {
                return $this->portada;
            }
            return asset('storage/' . ltrim($this->portada, '/'));
        }

        return asset('img/fondo/taqe-hero.jpg');
    }

    public function getPdfUrlAttribute(): ?string
    {
        if (empty($this->archivo_pdf)) return null;
        if (str_starts_with($this->archivo_pdf, 'http://') || str_starts_with($this->archivo_pdf, 'https://')) {
            return $this->archivo_pdf;
        }
        return asset('storage/' . ltrim($this->archivo_pdf, '/'));
    }

    public function getAudioUrlAttribute(): ?string
    {
        if (empty($this->archivo_audio)) return null;
        if (str_starts_with($this->archivo_audio, 'http://') || str_starts_with($this->archivo_audio, 'https://')) {
            return $this->archivo_audio;
        }
        return asset('storage/' . ltrim($this->archivo_audio, '/'));
    }

    /**
     * Convierte URLs de YouTube o Google Drive al formato embed para iframe responsivo.
     */
    public function getVideoEmbedUrlAttribute(): ?string
    {
        if (empty($this->url_video)) return null;

        $url = trim($this->url_video);

        // YouTube: watch?v=ID o youtu.be/ID
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $m)) {
            return "https://www.youtube.com/embed/{$m[1]}?rel=0";
        }

        // Google Drive: /file/d/ID/view -> /file/d/ID/preview
        if (preg_match('/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/', $url, $m)) {
            return "https://drive.google.com/file/d/{$m[1]}/preview";
        }

        return $url;
    }
}
