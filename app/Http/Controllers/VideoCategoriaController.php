<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\VideoCategoria;
use Illuminate\Http\Request;

class VideoCategoriaController extends Controller
{
    public function index()
    {
        $videoCategorias = VideoCategoria::all();
        return view('videos.categorias.index', compact('videoCategorias'));
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
            'url' => 'required|string',
        ]);

        VideoCategoria::create($request->all());

        return redirect()->route('videoscat.index')->with('success', 'Video categoría creado correctamente!');
    }
    public function show($url)
    {
        $secciones = VideoCategoria::withCount('videos')->orderBy('nombre')->get();
        $categoria = VideoCategoria::where('url', $url)->firstOrFail();
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
            'url' => 'required|string',
        ]);

        $videoCategoria = VideoCategoria::findOrFail($id);
        $videoCategoria->update($request->all());

        return redirect()->route('videoscat.index')
            ->with('success', 'Video categoría actualizado correctamente');
    }

    public function destroy($id)
    {
        $videoCategoria = VideoCategoria::findOrFail($id);
        $videoCategoria->delete();

        return redirect()->route('videoscat.index')
            ->with('success', 'Video categoría eliminado correctamente');
    }
}
