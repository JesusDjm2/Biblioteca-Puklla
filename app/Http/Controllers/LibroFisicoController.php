<?php

namespace App\Http\Controllers;

use App\Models\CategoriaFisica;
use App\Models\LibroFisico;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LibroFisicoController extends Controller
{
    public function index(Request $request)
    {
        $query = LibroFisico::query()
            ->with('categoriasFisicas')
            ->orderBy('nombre');

        if ($request->filled('q')) {
            $term = '%'.addcslashes($request->input('q'), '%_\\').'%';
            $query->where(function ($q) use ($term) {
                $q->where('nombre', 'like', $term)
                    ->orWhere('autor', 'like', $term)
                    ->orWhere('codigo', 'like', $term);
            });
        }

        if ($request->filled('categoria_fisica_id')) {
            $cid = (int) $request->input('categoria_fisica_id');
            $query->whereHas('categoriasFisicas', function ($q) use ($cid) {
                $q->where('categorias_fisicas.id', $cid);
            });
        }

        $libros = $query->paginate(25)->withQueryString();
        $categoriasFiltro = CategoriaFisica::orderBy('nombre')->get();

        return view('libros-fisicos.index', compact('libros', 'categoriasFiltro'));
    }

    public function create()
    {
        $categoriasFisicas = CategoriaFisica::orderBy('nombre')->get();

        return view('libros-fisicos.create', compact('categoriasFisicas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'autor' => 'required|string|max:255',
            'anio_publicacion' => 'required|integer|min:1000|max:' . (date('Y') + 1),
            'codigo' => 'required|string|max:100|unique:libros_fisicos,codigo',
            'categorias_fisicas' => 'required|array|min:1',
            'categorias_fisicas.*' => 'exists:categorias_fisicas,id',
        ]);

        $libro = LibroFisico::create($request->only(['nombre', 'autor', 'anio_publicacion', 'codigo']));
        $libro->categoriasFisicas()->sync($request->input('categorias_fisicas'));

        return redirect()->route('biblioteca-fisica.libros.index')
            ->with('success', 'Libro físico registrado correctamente.');
    }

    public function edit(LibroFisico $libro_fisico)
    {
        $categoriasFisicas = CategoriaFisica::orderBy('nombre')->get();
        $categoriasSeleccionadas = $libro_fisico->categoriasFisicas->pluck('id')->all();

        return view('libros-fisicos.edit', [
            'libro' => $libro_fisico,
            'categoriasFisicas' => $categoriasFisicas,
            'categoriasSeleccionadas' => $categoriasSeleccionadas,
        ]);
    }

    public function update(Request $request, LibroFisico $libro_fisico)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'autor' => 'required|string|max:255',
            'anio_publicacion' => 'required|integer|min:1000|max:' . (date('Y') + 1),
            'codigo' => ['required', 'string', 'max:100', Rule::unique('libros_fisicos', 'codigo')->ignore($libro_fisico->id)],
            'categorias_fisicas' => 'required|array|min:1',
            'categorias_fisicas.*' => 'exists:categorias_fisicas,id',
        ]);

        $libro_fisico->update($request->only(['nombre', 'autor', 'anio_publicacion', 'codigo']));
        $libro_fisico->categoriasFisicas()->sync($request->input('categorias_fisicas'));

        return redirect()->route('biblioteca-fisica.libros.index')
            ->with('success', 'Libro físico actualizado correctamente.');
    }

    public function destroy(LibroFisico $libro_fisico)
    {
        $libro_fisico->delete();

        return redirect()->route('biblioteca-fisica.libros.index')
            ->with('success', 'Libro físico eliminado correctamente.');
    }
}
