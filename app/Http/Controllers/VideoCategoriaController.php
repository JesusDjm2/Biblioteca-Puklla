<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\VideoCategoria;
use Illuminate\Http\Request;

class VideoCategoriaController extends Controller
{
    public function index()
    {
        $videoCategorias = VideoCategoria::withCount([
                'videos',
                'videos as youtube_count' => fn ($q) => $q->whereNotNull('youtube')->where('youtube', '!=', ''),
                'videos as drive_count' => fn ($q) => $q->whereNotNull('drive')->where('drive', '!=', ''),
                'videos as archivo_count' => fn ($q) => $q->whereNotNull('archivo'),
            ])
            ->with(['videos' => fn ($q) => $q->select('id', 'categoria_id', 'nombre', 'youtube', 'drive', 'archivo', 'created_at')->latest()->latest('id')])
            ->orderBy('nombre')
            ->get();

        return view('videos.categorias.index', [
            'videoCategorias' => $videoCategorias,
            'totalVideos' => $videoCategorias->sum('videos_count'),
        ]);
    }
    public function lista()
    {
        $categorias = VideoCategoria::withCount('videos') // Aquí se añade el conteo
            ->orderBy('nombre', 'asc')
            ->get();

        $videos = Video::orderBy('created_at', 'desc')->get();
        return view('videos.lista', compact('videos', 'categorias'));
    }
    public function create()
    {
        return view('videos.categorias.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'url' => 'required|string|unique:video_categorias,url',
        ], [
            'url.unique' => 'Ya existe una categoría con esa URL; usa un nombre distinto.',
        ]);

        VideoCategoria::create($request->all());

        return redirect()->route('videoscat.index')->with('success', 'Video categoría creado correctamente!');
    }
    public function show($url)
    {
        $secciones = VideoCategoria::withCount('videos')->orderBy('nombre')->get();
        // Si por datos antiguos dos categorías comparten la URL, se muestra la que tiene videos.
        $categoria = VideoCategoria::where('url', $url)->withCount('videos')
            ->orderByDesc('videos_count')->firstOrFail();
        $videos = $categoria->videos()->orderBy('created_at', 'desc')->get();
        return view('videos.categorias.show', compact('secciones', 'categoria', 'videos'));
    }


    public function edit($id)
    {
        $videoCategoria = VideoCategoria::findOrFail($id);
        return view('videos.categorias.edit', compact('videoCategoria'));
    }
    public function update(Request $request, $id)
    {
        // Valida y actualiza los datos enviados por el formulario
        $request->validate([
            'nombre' => 'required|string',
            'url' => 'required|string|unique:video_categorias,url,'.(int) $id,
        ], [
            'url.unique' => 'Ya existe una categoría con esa URL; usa un nombre distinto.',
        ]);

        $videoCategoria = VideoCategoria::findOrFail($id);
        $videoCategoria->update($request->all());

        return redirect()->route('videoscat.index')
            ->with('success', 'Video categoría actualizado correctamente');
    }

    public function destroy($id)
    {
        $videoCategoria = VideoCategoria::findOrFail($id);

        if ($videoCategoria->videos()->exists()) {
            return redirect()->route('videoscat.index')
                ->with('error', 'No se puede eliminar "'.$videoCategoria->nombre.'" porque tiene videos. Muévelos a otra categoría o elimínalos primero.');
        }

        $videoCategoria->delete();

        return redirect()->route('videoscat.index')
            ->with('success', 'Video categoría eliminado correctamente');
    }
}
