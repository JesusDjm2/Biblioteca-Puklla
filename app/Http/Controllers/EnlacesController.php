<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\CategoriaFisica;
use App\Models\Libro;
use App\Models\LibroFisico;
use App\Models\Seccion;
use App\Models\User;
use App\Models\Video;
use App\Models\VideoCategoria;
use App\Services\BuscadorGlobal;
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
        $buscador = new BuscadorGlobal($request->input('query'));
        $conteos = $buscador->conteos();

        // Pestaña de un módulo ("Ver todos"): solo si ese módulo tiene resultados.
        $tipo = $request->input('tipo');
        $tipo = BuscadorGlobal::existe($tipo) && isset($conteos[$tipo]) ? $tipo : null;

        return view('buscador', [
            'query' => $buscador->texto(),
            'buscador' => $buscador,
            'conteos' => $conteos,
            'total' => array_sum($conteos),
            'tipo' => $tipo,
            'resultados' => $tipo ? $buscador->paginar($tipo) : null,
            'carruseles' => $tipo ? [] : $buscador->vistaPrevia($conteos),
        ]);
    }
    public function admin()
    {
        $admins = User::all();
        return view('auth.admins.index', compact('admins'));
    }
}
