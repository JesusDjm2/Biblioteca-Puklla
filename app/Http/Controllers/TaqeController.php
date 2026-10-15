<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaqeCategoria;
use App\Models\TaqeTrabajo;

class TaqeController extends Controller
{
    /**
     * Catálogo público de Taq'e (Repositorio de Trabajos de Estudiantes).
     */
    public function index(Request $request)
    {
        $categorias = TaqeCategoria::activas()
            ->withCount('trabajosPublicados')
            ->get();

        $query = TaqeTrabajo::publicados()->with('categoria')->filtrar($request->all());

        // Ordenamiento
        $orden = $request->get('orden', 'recientes');
        match ($orden) {
            'antiguos' => $query->oldest(),
            'populares' => $query->orderByDesc('vistas'),
            'titulo' => $query->orderBy('titulo', 'asc'),
            default => $query->latest(),
        };

        $trabajos = $query->paginate(12)->withQueryString();

        // Conteo rápido por formato para los filtros pill
        $formatoCounts = TaqeTrabajo::publicados()
            ->selectRaw('formato_principal, count(*) as total')
            ->groupBy('formato_principal')
            ->pluck('total', 'formato_principal')
            ->all();

        $totalTrabajos = TaqeTrabajo::publicados()->count();

        // Lista de periodos disponibles
        $periodos = TaqeTrabajo::publicados()
            ->whereNotNull('año_periodo')
            ->where('año_periodo', '!=', '')
            ->distinct()
            ->orderByDesc('año_periodo')
            ->pluck('año_periodo');

        return view('taqe.index', [
            'categorias' => $categorias,
            'trabajos' => $trabajos,
            'formatos' => TaqeTrabajo::FORMATOS,
            'idiomas' => TaqeTrabajo::IDIOMAS,
            'periodos' => $periodos,
            'formatoCounts' => $formatoCounts,
            'totalTrabajos' => $totalTrabajos,
            'catActiva' => $request->filled('categoria_id') ? $categorias->firstWhere('id', $request->categoria_id) : null,
        ]);
    }

    /**
     * Detalle y reproductor/visor interactivo de un trabajo estudiantil.
     */
    public function show(string $slug)
    {
        $trabajo = TaqeTrabajo::publicados()
            ->with('categoria')
            ->where('slug', $slug)
            ->firstOrFail();

        // Registrar visualización única básica
        $trabajo->increment('vistas');

        // Trabajos relacionados de la misma categoría o temática
        $relacionados = TaqeTrabajo::publicados()
            ->with('categoria')
            ->where('categoria_id', $trabajo->categoria_id)
            ->where('id', '!=', $trabajo->id)
            ->limit(3)
            ->get();

        return view('taqe.show', [
            'trabajo' => $trabajo,
            'relacionados' => $relacionados,
        ]);
    }

    /**
     * Descarga de documento PDF con contador.
     */
    public function descargar(string $slug)
    {
        $trabajo = TaqeTrabajo::publicados()->where('slug', $slug)->firstOrFail();

        if (empty($trabajo->archivo_pdf)) {
            abort(404, 'Este trabajo no cuenta con archivo PDF descargable.');
        }

        $trabajo->increment('descargas');

        if (str_starts_with($trabajo->archivo_pdf, 'http://') || str_starts_with($trabajo->archivo_pdf, 'https://')) {
            return redirect()->away($trabajo->archivo_pdf);
        }

        $path = storage_path('app/public/' . ltrim($trabajo->archivo_pdf, '/'));
        if (!file_exists($path)) {
            abort(404, 'El archivo físico no fue encontrado.');
        }

        return response()->download($path, Str::slug($trabajo->titulo) . '.pdf');
    }
}
