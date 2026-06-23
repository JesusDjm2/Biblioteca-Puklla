<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\VideoCategoria;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::all();
        return view('videos.index', compact('videos'));
    }
    
    public function create()
    {
        $categorias = VideoCategoria::all();
        return view('videos.create', compact('categorias'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:videos,nombre',
            'descripcion' => 'nullable|string|max:255',
            'categoria_id' => 'required|exists:video_categorias,id',
            'youtube' => 'nullable|string|max:255',
            'drive' => 'nullable|string|max:255',
        ]);

        Video::create($request->all());

        return redirect()->route('videos.index')
            ->with('success', 'Video creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $video = Video::findOrFail($id);
        return view('videos.show', compact('video'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $video = Video::findOrFail($id);
        $categorias = VideoCategoria::all();
        return view('videos.edit', compact('video', 'categorias'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:255',
            'categoria_id' => 'required|exists:video_categorias,id',
            'youtube' => 'nullable|string|max:255',
            'drive' => 'nullable|string|max:255',
        ]);

        $video = Video::findOrFail($id);
        $video->nombre = $request->nombre;
        $video->descripcion = $request->descripcion;
        $video->categoria_id = $request->categoria_id;
        $video->youtube = $request->youtube;
        $video->drive = $request->drive;
        $video->save();

        return redirect()->route('videos.index')
            ->with('success', 'Video actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $video = Video::findOrFail($id);
        $video->delete();

        return redirect()->route('videos.index')
            ->with('success', 'Video eliminado exitosamente.');
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
