<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\VideoCategoria;
use App\Services\GoogleDriveVideos;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $query = Video::query()->with('categoria')->latest();

        if ($request->filled('q')) {
            $term = '%'.addcslashes($request->input('q'), '%_\\').'%';
            $query->where(function ($q) use ($term) {
                $q->where('nombre', 'like', $term)
                    ->orWhere('descripcion', 'like', $term);
            });
        }

        if ($request->filled('categoria_id')) {
            // Dentro de una categoría se listan en su orden de reproducción.
            $query->where('categoria_id', $request->integer('categoria_id'))->reorder()->ordenados();
        }

        match ($request->input('fuente')) {
            'youtube' => $query->whereNotNull('youtube')->where('youtube', '!=', ''),
            'drive' => $query->whereNotNull('drive')->where('drive', '!=', ''),
            'archivo' => $query->whereNotNull('archivo'),
            default => null,
        };

        $videos = $query->paginate(25)->withQueryString();
        $categorias = VideoCategoria::orderBy('nombre')->get();

        return view('videos.index', compact('videos', 'categorias'));
    }

    public function create(Request $request)
    {
        // Permite llegar desde una categoría con ella ya seleccionada.
        $video = new Video(['categoria_id' => $request->integer('categoria_id') ?: null]);
        $categorias = VideoCategoria::orderBy('nombre')->get();
        $maxMb = Video::maxSubidaMb();

        return view('videos.create', compact('video', 'categorias', 'maxMb'));
    }

    /**
     * Abre una sesión de subida directa a Google Drive (carpeta pukllaWeb/Biblioteca).
     * El navegador sube el archivo a la URL devuelta sin pasar por este servidor,
     * así que no aplica el límite de subida de PHP ni ocupa espacio del hosting.
     */
    public function iniciarSubidaDrive(Request $request, GoogleDriveVideos $drive)
    {
        abort_unless(GoogleDriveVideos::configurado(), 503, 'La subida a Google Drive no está configurada.');

        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => ['required', 'string', 'max:100', 'regex:/^video\//'],
            'tamano' => 'required|integer|min:1|max:'.(10 * 1024 * 1024 * 1024),
            'extension' => 'nullable|string|max:10',
        ], [
            'tipo.regex' => 'El archivo elegido no es un video.',
            'tamano.max' => 'El video supera los 10 GB.',
        ]);

        $extension = strtolower(preg_replace('/[^a-z0-9]/i', '', (string) $request->input('extension')));
        $nombre = Str::limit($request->input('nombre'), 150, '').($extension ? '.'.$extension : '');

        try {
            $url = $drive->iniciarSubida($nombre, $request->input('tipo'), (int) $request->input('tamano'), $request->getSchemeAndHttpHost());
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['message' => 'No se pudo conectar con Google Drive. Inténtalo de nuevo en unos minutos.'], 502);
        }

        return response()->json(['url' => $url]);
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        $video = new Video($data);
        $this->aplicarFuente($request, $video);
        $video->save();

        return $this->respuestaGuardado($request, 'Video "'.$video->nombre.'" creado correctamente.');
    }

    /**
     * El formulario envía por AJAX cuando sube un archivo (para mostrar el
     * progreso); en ese caso se responde JSON con la URL a la que ir.
     * Si se partió de la vista de Categorías (volver=categorias), se regresa ahí.
     */
    private function respuestaGuardado(Request $request, string $mensaje)
    {
        session()->flash('success', $mensaje);
        $destino = $request->input('volver') === 'categorias'
            ? route('videoscat.index')
            : route('videos.index');

        return $request->expectsJson()
            ? response()->json(['redirect' => $destino])
            : redirect($destino);
    }

    /**
     * Página pública del video: reproductor, datos, botones para compartir y
     * más videos de la misma categoría. Es la URL que se comparte en redes.
     */
    public function show(Request $request, $id)
    {
        $video = Video::with('categoria')->findOrFail($id);
        $this->registrarVista($request, $video);

        // Videos de la sección en su orden de reproducción (definido en el panel).
        $seccion = Video::where('categoria_id', $video->categoria_id)->ordenados()
            ->get(['id', 'nombre', 'youtube', 'drive', 'archivo', 'created_at', 'vistas']);
        $pos = $seccion->search(fn ($v) => $v->id === $video->id);
        $anterior = $pos > 0 ? $seccion[$pos - 1] : null;
        $siguiente = $pos !== false && $pos < $seccion->count() - 1 ? $seccion[$pos + 1] : null;

        // "A continuación": los que siguen y, al llegar al final, vuelve al inicio.
        $relacionados = $pos === false
            ? $seccion->take(6)
            : $seccion->slice($pos + 1)->concat($seccion->take($pos))->take(6)->values();
        $posicion = $pos === false ? null : $pos + 1;
        $totalSeccion = $seccion->count();

        return view('videos.show', compact('video', 'relacionados', 'anterior', 'siguiente', 'posicion', 'totalSeccion'));
    }

    /**
     * Suma una vista al video: una por sesión (recargar no cuenta) y sin contar
     * robots ni las vistas previas que generan WhatsApp, Facebook, etc. al compartir.
     */
    private function registrarVista(Request $request, Video $video): void
    {
        $agente = strtolower((string) $request->userAgent());
        if ($agente === '' || preg_match('/bot|crawl|spider|slurp|preview|facebookexternalhit|whatsapp|telegram|discord|skype|curl|wget|python|headless/', $agente)) {
            return;
        }

        $vistos = $request->session()->get('videos_vistos', []);
        if (in_array($video->id, $vistos, true)) {
            return;
        }
        $request->session()->put('videos_vistos', array_slice([...$vistos, $video->id], -200));

        // Por consulta directa: no toca updated_at y evita choques entre visitas simultáneas.
        Video::whereKey($video->id)->toBase()->increment('vistas');
        $video->vistas++;
    }

    public function edit($id)
    {
        $video = Video::findOrFail($id);
        $categorias = VideoCategoria::orderBy('nombre')->get();
        $maxMb = Video::maxSubidaMb();

        return view('videos.edit', compact('video', 'categorias', 'maxMb'));
    }

    public function update(Request $request, $id)
    {
        $video = Video::findOrFail($id);
        $driveAnterior = $video->drive;
        $video->fill($this->validar($request, $video));
        $this->aplicarFuente($request, $video);
        $video->save();

        // Si se reemplazó un video que Biblioteca había subido a Drive, se borra el anterior.
        if ($driveAnterior && $driveAnterior !== $video->drive) {
            $this->eliminarDeDriveSiEsPropio($driveAnterior);
        }

        return $this->respuestaGuardado($request, 'Video actualizado correctamente.');
    }

    public function destroy($id)
    {
        $video = Video::findOrFail($id);
        $video->eliminarArchivo();
        $this->eliminarDeDriveSiEsPropio($video->drive);
        $video->delete();

        return redirect()->route('videos.index')
            ->with('success', 'Video eliminado correctamente.');
    }

    private function validar(Request $request, ?Video $video = null): array
    {
        $maxKb = Video::maxSubidaMb() * 1024;
        $fuente = $request->input('fuente');
        // Al editar un video que ya tiene archivo, no es obligatorio volver a subirlo;
        // tampoco si el navegador ya lo subió a Drive (llega drive_subido_id).
        $archivoRequerido = $fuente === 'archivo' && ! ($video?->archivo) && ! $request->filled('drive_subido_id');

        // Solo se devuelven los campos de texto; la fuente (youtube/drive/archivo)
        // la resuelve aplicarFuente() para no pisar la ruta del archivo guardado.
        $request->validate([
            'nombre' => ['required', 'string', 'max:255', Rule::unique('videos', 'nombre')->ignore($video?->id)],
            'descripcion' => 'nullable|string|max:255',
            'categoria_id' => 'required|exists:video_categorias,id',
            'fuente' => 'required|in:youtube,drive,archivo',
            'youtube' => 'required_if:fuente,youtube|nullable|string|max:255',
            'drive' => 'required_if:fuente,drive|nullable|string|max:255',
            'drive_subido_id' => ['nullable', 'string', 'max:100', 'regex:/^[-\w]{20,}$/'],
            'archivo' => [
                Rule::requiredIf($archivoRequerido),
                'nullable',
                'file',
                'mimes:'.implode(',', Video::EXTENSIONES_PERMITIDAS),
                'max:'.$maxKb,
            ],
        ], [
            'youtube.required_if' => 'Pega el enlace o el identificador del video de YouTube.',
            'drive.required_if' => 'Pega el enlace o el identificador del video de Drive.',
            'archivo.required' => 'Selecciona el archivo de video a subir.',
            'archivo.mimes' => 'El video debe ser '.strtoupper(implode(', ', Video::EXTENSIONES_PERMITIDAS)).'.',
            'archivo.max' => 'El video supera el máximo permitido por el servidor ('.Video::maxSubidaMb().' MB).',
            'archivo.uploaded' => 'No se pudo subir el video: probablemente supera el máximo permitido por el servidor ('.Video::maxSubidaMb().' MB).',
        ]);

        return $request->only(['nombre', 'descripcion', 'categoria_id']);
    }

    /**
     * Deja guardada solo la fuente elegida (YouTube, Drive o archivo) y limpia las demás.
     */
    private function aplicarFuente(Request $request, Video $video): void
    {
        $fuente = $request->input('fuente');

        // Video subido por el navegador directamente a Drive: se guarda como video de Drive.
        if ($fuente === 'archivo' && $request->filled('drive_subido_id')) {
            $id = $request->input('drive_subido_id');
            $drive = app(GoogleDriveVideos::class);

            // Solo se aceptan archivos de la carpeta de Biblioteca: evita que alguien
            // envíe el ID de otro archivo de Drive y el sistema lo haga público.
            if (! $drive->esDeBiblioteca($id)) {
                throw ValidationException::withMessages([
                    'archivo' => 'No se encontró el video subido en Google Drive. Vuelve a subirlo.',
                ]);
            }

            try {
                $drive->hacerPublico($id);
            } catch (\Throwable $e) {
                report($e);
                throw ValidationException::withMessages([
                    'archivo' => 'El video se subió a Drive pero no se pudo publicar. Vuelve a guardar en unos segundos.',
                ]);
            }

            $video->eliminarArchivo();
            $video->archivo = null;
            $video->youtube = null;
            $video->drive = $id;

            return;
        }

        if ($fuente !== 'archivo' || $request->hasFile('archivo')) {
            $video->eliminarArchivo();
            $video->archivo = null;
        }

        $video->youtube = $fuente === 'youtube' ? Video::extraerYoutubeId($request->input('youtube')) : null;
        $video->drive = $fuente === 'drive' ? Video::extraerDriveId($request->input('drive')) : null;

        if ($fuente === 'archivo' && $request->hasFile('archivo')) {
            $archivo = $request->file('archivo');
            $nombre = Str::slug(Str::limit($video->nombre, 60, '')).'-'.Str::random(8)
                .'.'.strtolower($archivo->getClientOriginalExtension());
            $archivo->move(public_path(Video::CARPETA_ARCHIVOS), $nombre);
            $video->archivo = Video::CARPETA_ARCHIVOS.'/'.$nombre;
        }
    }

    /**
     * Borra de Drive un video solo si está en la carpeta de Biblioteca (nunca un
     * enlace de Drive pegado a mano que pertenezca a otra persona o proyecto).
     */
    private function eliminarDeDriveSiEsPropio(?string $id): void
    {
        try {
            $drive = app(GoogleDriveVideos::class);
            if ($drive->esDeBiblioteca($id)) {
                $drive->eliminar($id);
            }
        } catch (\Throwable $e) {
            report($e); // no impedir borrar/editar el video por un fallo de Drive
        }
    }

    public function importForm()
    {
        return view('videos.import');
    }

    public function importProcess(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:json,csv,txt|max:10240',
        ]);

        $file = $request->file('archivo');
        $extension = strtolower($file->getClientOriginalExtension());
        $data = [];

        if ($extension === 'json') {
            $jsonContent = file_get_contents($file->getRealPath());
            $data = json_decode($jsonContent, true);
            if (!is_array($data)) {
                return redirect()->back()->with('error', 'El archivo JSON no tiene un formato válido.');
            }
        } elseif ($extension === 'csv' || $extension === 'txt') {
            $path = $file->getRealPath();
            if (($handle = fopen($path, 'r')) !== false) {
                // Detect delimiter
                $firstLine = fgets($handle);
                rewind($handle);
                $delimiter = (strpos($firstLine, ';') !== false) ? ';' : ',';

                // Read headers
                $headers = fgetcsv($handle, 0, $delimiter);
                if ($headers) {
                    $headers = array_map(function($h) {
                        return strtolower(trim(preg_replace('/[\x{FEFF}"\']/u', '', $h)));
                    }, $headers);

                    $headerMap = [];
                    foreach ($headers as $index => $h) {
                        if (str_contains($h, 'categoria') || str_contains($h, 'categoría')) {
                            $headerMap['categoria'] = $index;
                        } elseif (str_contains($h, 'nombre') || str_contains($h, 'titulo') || str_contains($h, 'título') || str_contains($h, 'name')) {
                            $headerMap['nombre'] = $index;
                        } elseif (str_contains($h, 'descripcion') || str_contains($h, 'descripción') || str_contains($h, 'description')) {
                            $headerMap['descripcion'] = $index;
                        } elseif (str_contains($h, 'youtube') || str_contains($h, 'video') || str_contains($h, 'link') || str_contains($h, 'url') || str_contains($h, 'id')) {
                            $headerMap['youtube'] = $index;
                        } elseif (str_contains($h, 'drive')) {
                            $headerMap['drive'] = $index;
                        }
                    }
                }

                if (empty($headerMap)) {
                    $headerMap = [
                        'categoria' => 0,
                        'nombre' => 1,
                        'descripcion' => 2,
                        'youtube' => 3,
                        'drive' => 4
                    ];
                }

                while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                    if (count($row) < 2) continue;
                    $categoriaName = isset($headerMap['categoria']) && isset($row[$headerMap['categoria']]) ? trim($row[$headerMap['categoria']]) : 'General';
                    $nombre = isset($headerMap['nombre']) && isset($row[$headerMap['nombre']]) ? trim($row[$headerMap['nombre']]) : '';
                    $descripcion = isset($headerMap['descripcion']) && isset($row[$headerMap['descripcion']]) ? trim($row[$headerMap['descripcion']]) : '';
                    $youtube = isset($headerMap['youtube']) && isset($row[$headerMap['youtube']]) ? trim($row[$headerMap['youtube']]) : '';
                    $drive = isset($headerMap['drive']) && isset($row[$headerMap['drive']]) ? trim($row[$headerMap['drive']]) : '';

                    if (empty($nombre)) continue;

                    $data[] = [
                        'categoria' => $categoriaName,
                        'nombre' => $nombre,
                        'descripcion' => $descripcion,
                        'youtube' => $youtube,
                        'drive' => $drive
                    ];
                }
                fclose($handle);
            }
        }

        if (empty($data)) {
            return redirect()->back()->with('error', 'No se encontraron registros válidos para importar.');
        }

        $importedCount = 0;
        $updatedCount = 0;

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            foreach ($data as $item) {
                $categoriaName = !empty($item['categoria']) ? $item['categoria'] : 'General';
                $nombre = $item['nombre'];
                $descripcion = $item['descripcion'];
                $rawYoutube = $item['youtube'];
                $rawDrive = $item['drive'];

                $youtubeId = $this->extractYoutubeId($rawYoutube);
                $driveId = $this->extractDriveId($rawDrive);

                if (empty($youtubeId) && empty($driveId)) {
                    continue;
                }

                // 1. Find or create Category
                $categoria = VideoCategoria::firstOrCreate(
                    ['nombre' => $categoriaName],
                    ['url' => \Illuminate\Support\Str::slug($categoriaName)]
                );

                // 2. Find or update Video
                $video = null;
                if (!empty($youtubeId)) {
                    $video = Video::where('youtube', $youtubeId)->first();
                }
                
                if (!$video && !empty($driveId)) {
                    $video = Video::where('drive', $driveId)->first();
                }

                if (!$video) {
                    $video = Video::where('nombre', $nombre)->first();
                }

                if ($video) {
                    $video->update([
                        'nombre' => $nombre,
                        'descripcion' => $descripcion,
                        'categoria_id' => $categoria->id,
                        'youtube' => $youtubeId ?: $video->youtube,
                        'drive' => $driveId ?: $video->drive,
                    ]);
                    $updatedCount++;
                } else {
                    Video::create([
                        'nombre' => $nombre,
                        'descripcion' => $descripcion,
                        'categoria_id' => $categoria->id,
                        'youtube' => $youtubeId,
                        'drive' => $driveId,
                    ]);
                    $importedCount++;
                }
            }

            \Illuminate\Support\Facades\DB::commit();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Error al procesar la importación: ' . $e->getMessage());
        }

        return redirect()->route('videos.index')->with('success', "Importación masiva completada. Videos creados: {$importedCount}, videos actualizados: {$updatedCount}.");
    }

    private function extractYoutubeId($urlOrId)
    {
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|win/.+|watch\?v=|\?v=)|youtu\.be/)([^"&?/\s]{11})%i', $urlOrId, $match)) {
            return $match[1];
        }
        return $urlOrId;
    }

    private function extractDriveId($urlOrId)
    {
        if (preg_match('/[-\w]{25,}/', $urlOrId, $matches)) {
            return $matches[0];
        }
        return $urlOrId;
    }
}
