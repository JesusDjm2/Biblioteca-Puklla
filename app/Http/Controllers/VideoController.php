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
}
