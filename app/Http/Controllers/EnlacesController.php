<?php

namespace App\Http\Controllers;

use App\Models\Canciones;
use App\Models\Categoria;
use App\Models\CategoriaFisica;
use App\Models\Libro;
use App\Models\LibroFisico;
use App\Models\Seccion;
use App\Models\User;
use App\Models\Video;
use App\Models\VideoCategoria;
use Illuminate\Http\Request;

class EnlacesController extends Controller
{
    public function index()
    {
        $libros = Libro::latest()->take(8)->get();
        $videos = Video::latest()->take(8)->get();
        $librosFisicos = LibroFisico::latest()->with('categoriaFisica')->take(16)->get();

        $categoriasVirtuales = Categoria::withCount('libros')
            ->has('libros')
            ->orderBy('nombre')
            ->get();
        $categoriasFisicas = CategoriaFisica::withCount('librosFisicos')
            ->has('librosFisicos')
            ->orderBy('nombre')
            ->get();
        $categoriasVideos = VideoCategoria::withCount('videos')
            ->has('videos')
            ->orderBy('nombre')
            ->get();

        return view('welcome', compact(
            'libros',
            'videos',
            'librosFisicos',
            'categoriasVirtuales',
            'categoriasFisicas',
            'categoriasVideos'
        ));
    }
    public function busqueda(Request $request)
    {
        $query = $request->input('query');
        // Buscar en Canciones
        $canciones = Canciones::where('nombre', 'like', "%$query%")
            ->orWhere('autor', 'like', "%$query%")
            ->get();
        $cancionesCount = $canciones->count();

        // Buscar en Videos
        $videos = Video::where('nombre', 'like', "%$query%")
            ->get();
        $videosCount = $videos->count();

        // Buscar en Libros
        $libros = Libro::where('nombre', 'like', "%$query%")
            ->orWhere('autor', 'like', "%$query%")
            ->get();
        $librosCount = $libros->count();

        // Buscar en Libros físicos
        $librosFisicos = LibroFisico::where('nombre', 'like', "%$query%")
            ->orWhere('autor', 'like', "%$query%")
            ->orWhere('editorial', 'like', "%$query%")
            ->with('categoriaFisica')
            ->get();
        $librosFisicosCount = $librosFisicos->count();

        $hasResults = !$canciones->isEmpty() || !$videos->isEmpty() || !$libros->isEmpty() || !$librosFisicos->isEmpty();

        // Devolver resultados a la vista buscador
        return view('buscador', [
            'canciones' => $canciones,
            'videos' => $videos,
            'libros' => $libros,
            'librosFisicos' => $librosFisicos,
            'query' => $query,
            'hasResults' => $hasResults,
            'cancionesCount' => $cancionesCount,
            'videosCount' => $videosCount,
            'librosCount' => $librosCount,
            'librosFisicosCount' => $librosFisicosCount,
        ]);
    }
    public function admin()
    {
        $admins = User::all();
        return view('auth.admins.index', compact('admins'));
    }
}
