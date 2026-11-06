<?php

namespace App\Http\Controllers;

use App\Models\RepoColeccion;
use App\Models\RepoItem;
use App\Services\GoogleDriveVideos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RepoItemAdminController extends Controller
{
    /** Tamaño máximo de un PDF subido a Drive (no depende de los límites de PHP del servidor). */
    public const MAX_PDF_MB = 300;

    public function index(Request $request)
    {
        $items = RepoItem::with(['autores', 'coleccion.comunidad'])
            ->buscar($request->q)
            ->when($request->filled('coleccion_id'), fn ($q) => $q->where('coleccion_id', $request->integer('coleccion_id')))
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', $request->estado))
            ->when($request->filled('grado'), fn ($q) => $q->where('grado_academico', $request->grado))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.repositorio.items.index', [
            'items' => $items,
            'colecciones' => $this->coleccionesAgrupadas(),
            'conteoEstados' => RepoItem::selectRaw('estado, count(*) as total')->groupBy('estado')->pluck('total', 'estado'),
        ]);
    }

    public function create(Request $request)
    {
        $item = new RepoItem([
            'idioma' => 'es',
            'estado' => 'borrador',
            'editorial' => RepoItem::EDITORIAL_PREDETERMINADA,
            'licencia' => 'CC-BY-NC-SA-4.0',
            'fecha_publicacion' => now(),
        ]);

        if ($coleccion = RepoColeccion::find($request->integer('coleccion_id'))) {
            $item->coleccion_id = $coleccion->id;
            $item->programa = $coleccion->programa;
            $item->tipo_documento = $coleccion->tipo_documento;
            $item->grado_academico = $coleccion->grado_academico;
        }

        return view('admin.repositorio.items.create', $this->datosFormulario($item));
    }

    /**
     * Abre una sesión de subida a la carpeta "Repositorio" de Drive. El navegador
     * envía el PDF directamente a Google; el servidor nunca recibe el archivo.
     */
    public function iniciarSubidaDrive(Request $request, GoogleDriveVideos $drive)
    {
        abort_unless(GoogleDriveVideos::repositorioConfigurado(), 503, 'La subida a Google Drive no está configurada.');

        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|in:application/pdf',
            'tamano' => 'required|integer|min:1|max:' . (self::MAX_PDF_MB * 1024 * 1024),
        ], [
            'tipo.in' => 'El archivo elegido no es un PDF.',
            'tamano.max' => 'El PDF supera los ' . self::MAX_PDF_MB . ' MB.',
        ]);

        $nombre = Str::limit(Str::slug($request->input('nombre')), 150, '') ?: 'documento';

        try {
            $url = $drive->iniciarSubida($nombre . '.pdf', 'application/pdf', (int) $request->input('tamano'),
                $request->getSchemeAndHttpHost(), $drive->carpetaRepositorio());
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['message' => 'No se pudo conectar con Google Drive. Inténtalo de nuevo en unos minutos.'], 502);
        }

        return response()->json(['url' => $url]);
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        $pdf = $this->pdfRecibido($request);

        $item = DB::transaction(function () use ($request, $data, $pdf) {
            $item = new RepoItem($data);
            if ($pdf) {
                $item->archivo_pdf = $pdf;
            }
            $item->save();
            $item->sincronizarAutores($request->input('autores', []));

            return $item;
        });

        return redirect()->route('admin.repositorio.items.edit', ['item' => $item, 'volver' => $request->input('volver') === 'estructura' ? 'estructura' : null])
            ->with('success', "Documento '{$item->titulo}' registrado. Enlace corto: {$item->url_corta_completa}");
    }

    public function edit(RepoItem $item)
    {
        $item->load('autores');

        return view('admin.repositorio.items.edit', $this->datosFormulario($item));
    }

    public function update(Request $request, RepoItem $item)
    {
        $data = $this->validar($request);
        $pdf = $this->pdfRecibido($request);

        DB::transaction(function () use ($request, $data, $item, $pdf) {
            $item->fill($data);
            if ($pdf && $pdf !== $item->archivo_pdf) {
                $item->eliminarPdf();
                $item->archivo_pdf = $pdf;
            } elseif (!$pdf && $request->boolean('quitar_pdf')) {
                $item->eliminarPdf();
                $item->archivo_pdf = null;
            }
            if (empty($item->url_corta)) {
                $item->url_corta = RepoItem::generarCodigoCorto();
            }
            $item->save();
            $item->sincronizarAutores($request->input('autores', []));
        });

        return redirect()->route('admin.repositorio.items.edit', ['item' => $item, 'volver' => $request->input('volver') === 'estructura' ? 'estructura' : null])
            ->with('success', "Documento '{$item->titulo}' actualizado.");
    }

    public function destroy(RepoItem $item)
    {
        $titulo = $item->titulo;
        $item->delete();

        return redirect()->route('admin.repositorio.items.index')
            ->with('success', "Documento '{$titulo}' eliminado junto con su PDF.");
    }

    protected function validar(Request $request): array
    {
        $request->merge([
            'autores' => array_values(array_filter(array_map('trim', (array) $request->input('autores', [])), 'strlen')),
        ]);

        $data = $request->validate([
            'coleccion_id' => 'required|exists:repo_colecciones,id',
            'titulo' => 'required|string|max:500',
            'autores' => 'required|array|min:1|max:20',
            'autores.*' => 'string|max:250',
            'asesor' => 'nullable|string|max:255',
            'resumen' => 'nullable|string|max:20000',
            'palabras_clave' => 'nullable|string|max:1000',
            'fecha_publicacion' => 'nullable|date|after:1900-01-01|before:2100-01-01',
            'editorial' => 'nullable|string|max:255',
            'idioma' => ['required', Rule::in(array_keys(RepoItem::IDIOMAS))],
            'tipo_documento' => 'nullable|string|max:80',
            'programa' => 'nullable|string|max:20',
            'grado_academico' => 'nullable|string|max:80',
            'area_tematica' => 'nullable|string|max:255',
            'identificador_url' => 'nullable|url|max:500',
            'licencia' => ['nullable', Rule::in(array_keys(RepoItem::LICENCIAS))],
            'estado' => ['required', Rule::in(array_keys(RepoItem::ESTADOS))],
            'archivo_pdf' => 'nullable|file|mimes:pdf|mimetypes:application/pdf|max:51200',
            'drive_archivo_id' => ['nullable', 'string', 'max:120', 'regex:/^[\w-]{20,}$/'],
        ], [
            'autores.required' => 'Registra al menos un autor.',
            'archivo_pdf.mimes' => 'El archivo debe ser un PDF.',
            'archivo_pdf.mimetypes' => 'El archivo debe ser un PDF.',
            'archivo_pdf.max' => 'El PDF debe pesar como máximo 50 MB.',
        ]);

        return collect($data)->except(['autores', 'archivo_pdf', 'drive_archivo_id'])->all();
    }

    /**
     * PDF que llega con el formulario, ya listo para guardar en archivo_pdf:
     * "drive:{id}" si se subió a Drive desde el navegador, o la ruta en disco
     * (respaldo cuando Drive no está configurado). Null si no se envió ninguno.
     */
    protected function pdfRecibido(Request $request): ?string
    {
        if ($id = $request->input('drive_archivo_id')) {
            $drive = app(GoogleDriveVideos::class);
            try {
                $valido = $drive->estaEnCarpeta($id, $drive->carpetaRepositorio());
                if ($valido) {
                    $drive->hacerPublico($id);
                }
            } catch (\Throwable $e) {
                report($e);
                $valido = false;
            }
            if (!$valido) {
                throw ValidationException::withMessages([
                    'archivo_pdf' => 'No se pudo confirmar el PDF en la carpeta del Repositorio en Drive. Vuelve a seleccionarlo.',
                ]);
            }

            return RepoItem::PREFIJO_DRIVE . $id;
        }

        if ($request->hasFile('archivo_pdf')) {
            return $request->file('archivo_pdf')->store(RepoItem::CARPETA_PDF, RepoItem::DISCO);
        }

        return null;
    }

    protected function datosFormulario(RepoItem $item): array
    {
        return [
            'item' => $item,
            'colecciones' => $this->coleccionesAgrupadas(),
            'idiomas' => RepoItem::IDIOMAS,
            'tipos' => RepoItem::TIPOS_DOCUMENTO,
            'programas' => RepoItem::PROGRAMAS,
            'grados' => RepoItem::GRADOS,
            'licencias' => RepoItem::LICENCIAS,
            'estados' => RepoItem::ESTADOS,
            'subidaDrive' => GoogleDriveVideos::repositorioConfigurado(),
        ];
    }

    /** Colecciones agrupadas por la ruta de su comunidad, para <optgroup>. */
    protected function coleccionesAgrupadas()
    {
        return RepoColeccion::with('comunidad.padre')
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get()
            ->sortBy(fn ($c) => sprintf('%05d-%05d', $c->comunidad?->orden ?? 99999, $c->orden))
            ->groupBy(fn ($c) => $c->comunidad?->ruta ?? 'Sin comunidad');
    }
}
