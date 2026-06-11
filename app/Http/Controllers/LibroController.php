<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Libro;
use App\Models\Seccion;
use Illuminate\Http\Request;

class LibroController extends Controller
{
    public function index(Request $request)
    {
        $query = Libro::query()
            ->with(['categorias.seccion'])
            ->orderBy('nombre');

        if ($request->filled('q')) {
            $term = '%'.addcslashes($request->input('q'), '%_\\').'%';
            $query->where(function ($q) use ($term) {
                $q->where('nombre', 'like', $term)
                    ->orWhere('autor', 'like', $term)
                    ->orWhere('identificador', 'like', $term);
            });
        }

        if ($request->filled('categoria_id')) {
            $cid = (int) $request->input('categoria_id');
            $query->whereHas('categorias', function ($q) use ($cid) {
                $q->where('categorias.id', $cid);
            });
        }

        if ($request->filled('seccion_id')) {
            $sid = (int) $request->input('seccion_id');
            $query->whereHas('categorias', function ($q) use ($sid) {
                $q->where('seccion_id', $sid);
            });
        }

        $libros = $query->paginate(20)->withQueryString();
        $categoriasFiltro = Categoria::with('seccion')->orderBy('seccion_id')->orderBy('nombre')->get();
        $seccionesFiltro = Seccion::orderBy('nombre')->get();

        return view('libros.index', compact('libros', 'categoriasFiltro', 'seccionesFiltro'));
    }
    public function libros()
    {
        $secciones = Seccion::withCount('categorias')
            ->with(['categorias' => function ($q) {
                $q->withCount('libros')->orderBy('nombre');
            }])
            ->orderBy('nombre')
            ->get();

        $libros = Libro::orderBy('created_at', 'desc')->take(24)->get();

        return view('libros.lista', compact('libros', 'secciones'));
    }

    public function create($categoriaId = null)
    {
        $secciones = Seccion::with('categorias')->get();
        $categorias = Categoria::all();
        $categoriaSeleccionada = $categoriaId ? [$categoriaId] : [];

        return view('libros.create', compact('secciones', 'categorias', 'categoriaSeleccionada'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'autor' => 'required|string|max:255',
            'identificador' => 'required|string|max:255',
            'categorias' => 'required|array',
            'categorias.*' => 'exists:categorias,id'
        ]);

        $libro = Libro::create($request->only('nombre', 'autor', 'identificador'));
        $libro->categorias()->attach($request->categorias);

        return redirect()->route('libros.index')->with('success', 'Libro creado exitosamente.');
    }

    public function show($id)
    {
        $secciones = Seccion::all();
        $libros = Libro::all();
        $libro = Libro::findOrFail($id);
        return view('libros.show', compact('libro', 'secciones', 'libros'));
    }

    public function edit($id)
    {
        $libro = Libro::findOrFail($id);
        $categorias = Categoria::all();
        $libroCategorias = $libro->categorias->pluck('id')->toArray(); // Obteniendo los IDs de las categorías asociadas al libro
        return view('libros.edit', compact('libro', 'categorias', 'libroCategorias'));
    }

    public function update(Request $request, $id)
    {
        $libro = Libro::findOrFail($id);

        // Validación de los datos
        $request->validate([
            'nombre' => 'required|string|max:255',
            'autor' => 'required|string|max:255',
            'identificador' => 'required|string|max:255',
            'categorias' => 'array',
            'categorias.*' => 'exists:categorias,id',
        ]);

        // Actualizar los datos del libro
        $libro->update([
            'nombre' => $request->input('nombre'),
            'autor' => $request->input('autor'),
            'identificador' => $request->input('identificador'),
        ]);

        // Actualizar las categorías asociadas al libro
        if ($request->has('categorias')) {
            $libro->categorias()->sync($request->input('categorias'));
        } else {
            // Si no se seleccionó ninguna categoría, desasociar todas las categorías
            $libro->categorias()->detach();
        }

        return redirect()->route('libros.index')->with('success', 'Libro actualizado correctamente');
    }

    public function destroy($id)
    {
        $libro = Libro::findOrFail($id);
        $libro->delete();

        return redirect()->route('libros.index')->with('success', 'Libro eliminado correctamente');
    }

    public function importForm()
    {
        return view('libros.import');
    }

    public function importProcess(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:json,csv,txt|max:10240', // max 10MB
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
                // Detect delimiter (comma or semicolon)
                $firstLine = fgets($handle);
                rewind($handle);
                $delimiter = (strpos($firstLine, ';') !== false) ? ';' : ',';

                // Read headers
                $headers = fgetcsv($handle, 0, $delimiter);
                if ($headers) {
                    // Normalize headers (lowercase, strip whitespace/quotes)
                    $headers = array_map(function($h) {
                        return strtolower(trim(preg_replace('/[\x{FEFF}"\']/u', '', $h)));
                    }, $headers);

                    // Map header index
                    $headerMap = [];
                    foreach ($headers as $index => $h) {
                        if (str_contains($h, 'seccion') || str_contains($h, 'sección')) {
                            $headerMap['seccion'] = $index;
                        } elseif (str_contains($h, 'categoria') || str_contains($h, 'categoría')) {
                            $headerMap['categoria'] = $index;
                        } elseif (str_contains($h, 'nombre') || str_contains($h, 'titulo') || str_contains($h, 'título') || str_contains($h, 'name')) {
                            $headerMap['nombre'] = $index;
                        } elseif (str_contains($h, 'autor') || str_contains($h, 'author')) {
                            $headerMap['autor'] = $index;
                        } elseif (str_contains($h, 'identificador') || str_contains($h, 'drive') || str_contains($h, 'link') || str_contains($h, 'id')) {
                            $headerMap['identificador'] = $index;
                        }
                    }
                }

                // If headers not found, try default index mapping
                if (empty($headerMap)) {
                    $headerMap = [
                        'seccion' => 0,
                        'categoria' => 1,
                        'nombre' => 2,
                        'autor' => 3,
                        'identificador' => 4
                    ];
                }

                while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                    if (count($row) < 2) continue; // Skip empty rows
                    $seccionName = isset($headerMap['seccion']) && isset($row[$headerMap['seccion']]) ? trim($row[$headerMap['seccion']]) : 'General';
                    $categoriaName = isset($headerMap['categoria']) && isset($row[$headerMap['categoria']]) ? trim($row[$headerMap['categoria']]) : 'General';
                    $nombre = isset($headerMap['nombre']) && isset($row[$headerMap['nombre']]) ? trim($row[$headerMap['nombre']]) : '';
                    $autor = isset($headerMap['autor']) && isset($row[$headerMap['autor']]) ? trim($row[$headerMap['autor']]) : 'Desconocido';
                    $identificador = isset($headerMap['identificador']) && isset($row[$headerMap['identificador']]) ? trim($row[$headerMap['identificador']]) : '';

                    if (empty($nombre)) continue;

                    $data[] = [
                        'seccion' => $seccionName,
                        'categoria' => $categoriaName,
                        'nombre' => $nombre,
                        'autor' => $autor,
                        'identificador' => $identificador
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
                $seccionName = !empty($item['seccion']) ? $item['seccion'] : 'General';
                $categoriaName = !empty($item['categoria']) ? $item['categoria'] : 'General';
                $nombre = $item['nombre'];
                $autor = !empty($item['autor']) ? $item['autor'] : 'Desconocido';
                $rawIdentificador = !empty($item['identificador']) ? $item['identificador'] : '';

                $identificador = $this->extractDriveId($rawIdentificador);

                if (empty($identificador)) {
                    continue;
                }

                // 1. Find or create Section
                $seccion = \App\Models\Seccion::firstOrCreate(
                    ['nombre' => $seccionName],
                    ['url' => \Illuminate\Support\Str::slug($seccionName)]
                );

                // 2. Find or create Category
                $categoria = \App\Models\Categoria::firstOrCreate(
                    [
                        'nombre' => $categoriaName,
                        'seccion_id' => $seccion->id
                    ],
                    ['url' => \Illuminate\Support\Str::slug($categoriaName)]
                );

                // 3. Find or create Book
                $libro = \App\Models\Libro::where('identificador', $identificador)->first();
                if ($libro) {
                    $libro->update([
                        'nombre' => $nombre,
                        'autor' => $autor,
                    ]);
                    $updatedCount++;
                } else {
                    $libro = \App\Models\Libro::create([
                        'nombre' => $nombre,
                        'autor' => $autor,
                        'identificador' => $identificador,
                    ]);
                    $importedCount++;
                }

                // 4. Attach/Sync Category relationship
                if (!$libro->categorias()->where('categoria_id', $categoria->id)->exists()) {
                    $libro->categorias()->attach($categoria->id);
                }
            }

            \Illuminate\Support\Facades\DB::commit();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Error al procesar la importación: ' . $e->getMessage());
        }

        return redirect()->route('libros.index')->with('success', "Importación masiva completada. Libros creados: {$importedCount}, libros actualizados: {$updatedCount}.");
    }

    private function extractDriveId($urlOrId)
    {
        if (preg_match('/[-\w]{25,}/', $urlOrId, $matches)) {
            return $matches[0];
        }
        return $urlOrId;
    }
}
