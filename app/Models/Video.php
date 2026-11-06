<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    /**
     * Carpeta dentro de public/ donde se guardan los videos subidos. Se usa
     * public/ directamente (y no storage/ + symlink) porque el hosting se
     * actualiza por FTP y no siempre existe el enlace public/storage.
     */
    public const CARPETA_ARCHIVOS = 'videos-subidos';

    public const EXTENSIONES_PERMITIDAS = ['mp4', 'webm', 'ogg', 'mov', 'm4v'];

    protected $fillable = ['nombre', 'descripcion', 'youtube', 'drive', 'archivo', 'categoria_id'];

    protected static function booted()
    {
        // Un video nuevo, o que se mueve a otra categoría, queda al final de esa categoría.
        static::creating(function (Video $video) {
            if (! $video->orden) {
                $video->orden = static::siguienteOrden($video->categoria_id);
            }
        });
        static::updating(function (Video $video) {
            if ($video->isDirty('categoria_id') && ! $video->isDirty('orden')) {
                $video->orden = static::siguienteOrden($video->categoria_id);
            }
        });
    }

    public static function siguienteOrden($categoriaId): int
    {
        return (int) static::where('categoria_id', $categoriaId)->max('orden') + 1;
    }

    /** Orden de reproducción definido en el panel (Ordenar videos). */
    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden')->orderByDesc('created_at')->orderByDesc('id');
    }

    public function categoria()
    {
        return $this->belongsTo(VideoCategoria::class, 'categoria_id');
    }

    /**
     * Origen del video: 'youtube', 'drive' o 'archivo' (subido al servidor).
     */
    public function getFuenteAttribute(): ?string
    {
        return match (true) {
            ! empty($this->youtube) => 'youtube',
            ! empty($this->drive) => 'drive',
            ! empty($this->archivo) => 'archivo',
            default => null,
        };
    }

    /**
     * Vistas en texto corto: "1 vista", "245 vistas", "1,2 mil vistas".
     */
    public function getVistasTextoAttribute(): string
    {
        $n = (int) $this->vistas;
        if ($n >= 1000000) {
            $cifra = rtrim(rtrim(number_format($n / 1000000, 1, ',', '.'), '0'), ',').' M';
        } elseif ($n >= 1000) {
            $cifra = rtrim(rtrim(number_format($n / 1000, 1, ',', '.'), '0'), ',').' mil';
        } else {
            $cifra = (string) $n;
        }

        return $cifra.' '.($n === 1 ? 'vista' : 'vistas');
    }

    /**
     * Página pública del video (la que se comparte).
     */
    public function getUrlPublicaAttribute(): string
    {
        return route('videos.show', $this->id);
    }

    /**
     * Imagen para tarjetas y para la vista previa al compartir (og:image).
     */
    public function getMiniaturaUrlAttribute(): string
    {
        return match ($this->fuente) {
            'youtube' => 'https://img.youtube.com/vi/'.$this->youtube.'/hqdefault.jpg',
            'drive' => 'https://drive.google.com/thumbnail?id='.$this->drive.'&sz=w1200',
            default => asset('img/biblioteca-muestra.jpg'),
        };
    }

    /**
     * URL del reproductor embebido (YouTube o Drive); null si es un archivo subido.
     */
    public function getEmbedUrlAttribute(): ?string
    {
        return match ($this->fuente) {
            'youtube' => 'https://www.youtube-nocookie.com/embed/'.$this->youtube.'?rel=0',
            'drive' => 'https://drive.google.com/file/d/'.$this->drive.'/preview',
            default => null,
        };
    }

    public function getArchivoUrlAttribute(): ?string
    {
        return $this->archivo ? asset($this->archivo) : null;
    }

    public function getArchivoMimeAttribute(): string
    {
        return match (strtolower(pathinfo((string) $this->archivo, PATHINFO_EXTENSION))) {
            'webm' => 'video/webm',
            'ogg' => 'video/ogg',
            'mov' => 'video/quicktime',
            default => 'video/mp4',
        };
    }

    /**
     * Borra del disco el archivo subido (si lo hay).
     */
    public function eliminarArchivo(): void
    {
        if ($this->archivo && str_starts_with($this->archivo, self::CARPETA_ARCHIVOS.'/')) {
            $ruta = public_path($this->archivo);
            if (is_file($ruta)) {
                @unlink($ruta);
            }
        }
    }

    /**
     * Acepta un ID de YouTube o cualquier URL de YouTube (watch, youtu.be, embed, shorts).
     */
    public static function extraerYoutubeId(?string $valor): ?string
    {
        $valor = trim((string) $valor);
        if ($valor === '') {
            return null;
        }

        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?|shorts|live)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $valor, $m)) {
            return $m[1];
        }

        return $valor;
    }

    /**
     * Acepta un ID de Drive o una URL de Drive (file/d/ID/view, open?id=ID, etc.).
     */
    public static function extraerDriveId(?string $valor): ?string
    {
        $valor = trim((string) $valor);
        if ($valor === '') {
            return null;
        }

        if (preg_match('/[-\w]{25,}/', $valor, $m)) {
            return $m[0];
        }

        return $valor;
    }

    /**
     * Tamaño máximo de subida que permite PHP en este servidor, en megabytes.
     */
    public static function maxSubidaMb(): int
    {
        $aBytes = function (string $v): int {
            $v = trim($v);
            $n = (int) $v;

            return match (strtolower(substr($v, -1))) {
                'g' => $n * 1024 * 1024 * 1024,
                'm' => $n * 1024 * 1024,
                'k' => $n * 1024,
                default => $n,
            };
        };

        $limites = array_filter([
            $aBytes((string) ini_get('upload_max_filesize')),
            $aBytes((string) ini_get('post_max_size')),
        ]);

        return $limites ? max(1, intdiv(min($limites), 1024 * 1024)) : 50;
    }
}
