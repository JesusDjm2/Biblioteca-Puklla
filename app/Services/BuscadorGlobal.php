<?php

namespace App\Services;

use App\Models\Canciones;
use App\Models\Libro;
use App\Models\LibroFisico;
use App\Models\MaterialDidactico;
use App\Models\RepoItem;
use App\Models\TaqeTrabajo;
use App\Models\Video;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Búsqueda global del sitio público (página "Resultados de búsqueda").
 *
 * Cada módulo define en qué campos busca, qué columna es su "título" (para
 * ordenar por relevancia) y qué relaciones precarga. Se busca por palabras:
 * todas deben aparecer en algún campo, en cualquier orden ("educación inicial"
 * encuentra "Inicial: guía de educación"). El orden pone primero los títulos
 * iguales a lo buscado, luego los que empiezan así, luego los que lo contienen.
 */
class BuscadorGlobal
{
    public const MIN_CARACTERES = 2;
    public const POR_CARRUSEL = 12;
    public const POR_PAGINA = 24;
    private const MAX_PALABRAS = 6;

    /**
     * Módulos en el orden en que se muestran.
     * clave => [nombre, singular, plural, icono (Font Awesome 6)]
     */
    public const MODULOS = [
        'libros' => ['Biblioteca virtual', 'texto', 'textos', 'fa-solid fa-book-open'],
        'fisicos' => ['Biblioteca física', 'libro físico', 'libros físicos', 'fa-solid fa-book'],
        'repositorio' => ['Repositorio Institucional', 'documento', 'documentos', 'fa-solid fa-landmark'],
        'taqe' => ["Taq'e", 'actividad', 'actividades', 'fa-solid fa-calendar-check'],
        'videos' => ['Videos', 'video', 'videos', 'fa-solid fa-film'],
        'canciones' => ['Canciones', 'canción', 'canciones', 'fa-solid fa-music'],
        'material' => ['Material didáctico', 'material', 'materiales', 'fa-solid fa-shapes'],
    ];

    private string $texto;

    /** @var string[] */
    private array $palabras;

    public function __construct(?string $texto)
    {
        $this->texto = Str::of((string) $texto)->squish()->limit(120, '')->toString();
        $this->palabras = $this->separarPalabras($this->texto);
    }

    public function texto(): string
    {
        return $this->texto;
    }

    /** ¿Hay algo que buscar? (evita listar todo el catálogo con una sola letra). */
    public function esValida(): bool
    {
        return mb_strlen($this->texto) >= self::MIN_CARACTERES && $this->palabras !== [];
    }

    public static function existe(?string $modulo): bool
    {
        return $modulo !== null && array_key_exists($modulo, self::MODULOS);
    }

    /** Resultados por módulo: clave => total (solo los que tienen resultados, en su orden). */
    public function conteos(): array
    {
        if (! $this->esValida()) {
            return [];
        }

        $conteos = [];
        foreach (array_keys(self::MODULOS) as $modulo) {
            $total = $this->consulta($modulo, false)->count();
            if ($total > 0) {
                $conteos[$modulo] = $total;
            }
        }

        return $conteos;
    }

    /** Primeros resultados de cada módulo para los carruseles de la vista "Todo". */
    public function vistaPrevia(array $conteos): array
    {
        $porModulo = [];
        foreach (array_keys($conteos) as $modulo) {
            $porModulo[$modulo] = $this->consulta($modulo)->limit(self::POR_CARRUSEL)->get();
        }

        return $porModulo;
    }

    /** Resultados paginados de un módulo (pestaña "Ver todos"). */
    public function paginar(string $modulo)
    {
        return $this->consulta($modulo)->paginate(self::POR_PAGINA)->withQueryString();
    }

    /**
     * Consulta de un módulo con el filtro de palabras aplicado. Con $completa
     * añade relaciones y orden (no hace falta para contar).
     */
    public function consulta(string $modulo, bool $completa = true): Builder
    {
        [$query, $campos, $titulo, $relaciones, $ordenSecundario] = match ($modulo) {
            'libros' => [
                Libro::query(),
                ['nombre', 'autor'],
                'nombre', [],
                fn (Builder $q) => $q->orderBy('nombre'),
            ],
            'fisicos' => [
                LibroFisico::query(),
                ['nombre', 'autor', 'editorial', 'codigo'],
                'nombre', ['categoriaFisica'],
                fn (Builder $q) => $q->orderBy('nombre'),
            ],
            'repositorio' => [
                RepoItem::publicados(),
                ['titulo', 'resumen', 'asesor', 'area_tematica', 'palabras_clave', 'autores.nombre'],
                'titulo', ['autores', 'coleccion'],
                fn (Builder $q) => $q->orderByDesc('fecha_publicacion'),
            ],
            'taqe' => [
                TaqeTrabajo::publicados(),
                ['titulo', 'descripcion', 'autores', 'lugar'],
                'titulo', ['categoria'],
                fn (Builder $q) => $q->orderByDesc('fecha'),
            ],
            'videos' => [
                Video::query(),
                ['nombre', 'descripcion'],
                'nombre', ['categoria'],
                fn (Builder $q) => $q->orderByDesc('vistas')->orderByDesc('created_at'),
            ],
            'canciones' => [
                Canciones::query(),
                ['nombre', 'autor'],
                'nombre', ['categoria'],
                fn (Builder $q) => $q->orderBy('nombre'),
            ],
            'material' => [
                MaterialDidactico::query(),
                ['nombre', 'codigo', 'descripcion'],
                'nombre', ['categoria', 'imagenes'],
                fn (Builder $q) => $q->orderBy('nombre'),
            ],
        };

        $this->filtrarPorPalabras($query, $campos);

        if (! $completa) {
            return $query;
        }

        if ($modulo === 'material') {
            $query->withSum('prestamosActivos', 'cantidad');
        }

        $query->with($relaciones)->reorder();
        $this->ordenarPorRelevancia($query, $titulo);
        $ordenSecundario($query);

        return $query->orderByDesc($query->getModel()->getQualifiedKeyName());
    }

    /** Cada palabra debe aparecer en al menos uno de los campos ("relacion.campo" busca en una relación). */
    private function filtrarPorPalabras(Builder $query, array $campos): void
    {
        foreach ($this->palabras as $palabra) {
            $like = '%'.addcslashes($palabra, '%_\\').'%';
            $query->where(function (Builder $q) use ($campos, $like) {
                foreach ($campos as $campo) {
                    if (str_contains($campo, '.')) {
                        [$relacion, $columna] = explode('.', $campo, 2);
                        $q->orWhereHas($relacion, fn (Builder $r) => $r->where($columna, 'like', $like));
                    } else {
                        $q->orWhere($campo, 'like', $like);
                    }
                }
            });
        }
    }

    private function ordenarPorRelevancia(Builder $query, string $titulo): void
    {
        $columna = $query->getModel()->qualifyColumn($titulo);
        $frase = addcslashes($this->texto, '%_\\');

        $query->orderByRaw(
            "CASE WHEN {$columna} = ? THEN 0 WHEN {$columna} LIKE ? THEN 1 WHEN {$columna} LIKE ? THEN 2 ELSE 3 END",
            [$this->texto, $frase.'%', '%'.$frase.'%']
        );
    }

    /** Palabras de búsqueda: sin repetidas, ignorando las de una letra si hay otras más largas. */
    private function separarPalabras(string $texto): array
    {
        $palabras = collect(preg_split('/\s+/u', $texto, -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn ($p) => trim($p, " \t\n\r\0\x0B.,;:¿?¡!\"'()[]"))
            ->filter(fn ($p) => $p !== '')
            ->unique(fn ($p) => mb_strtolower($p));

        $largas = $palabras->filter(fn ($p) => mb_strlen($p) >= self::MIN_CARACTERES);

        return ($largas->isNotEmpty() ? $largas : $palabras)
            ->take(self::MAX_PALABRAS)
            ->values()
            ->all();
    }

    /** Texto para mostrar un total: "1 texto", "1.344 libros físicos". */
    public static function totalTexto(string $modulo, int $total): string
    {
        [, $singular, $plural] = self::MODULOS[$modulo];

        return number_format($total, 0, ',', '.').' '.($total === 1 ? $singular : $plural);
    }

    /** Enlace al catálogo propio del módulo con la búsqueda aplicada (si tiene búsqueda propia). */
    public function enlaceCatalogo(string $modulo): ?array
    {
        return match ($modulo) {
            'fisicos' => ['Abrir en el catálogo con filtros', route('catalogo-fisico', ['q' => $this->texto])],
            'repositorio' => ['Abrir en el Repositorio con filtros', route('repositorio.index', ['q' => $this->texto])],
            'taqe' => ["Abrir en Taq'e con filtros", route('taqe.index', ['q' => $this->texto])],
            default => null,
        };
    }
}
