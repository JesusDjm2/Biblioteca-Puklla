<?php

namespace App\Http\Controllers;

use App\Models\RepoColeccion;
use App\Models\RepoComunidad;
use App\Models\RepoItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Administración de la jerarquía del Repositorio: comunidades, subcomunidades y colecciones.
 */
class RepoEstructuraAdminController extends Controller
{
    public function index()
    {
        $conteos = fn ($q) => $q->withCount(['items', 'items as borradores_count' => fn ($i) => $i->where('estado', '!=', 'publicado')]);

        $comunidades = RepoComunidad::raices()
            ->with([
                'colecciones' => $conteos,
                'subcomunidades.colecciones' => $conteos,
                'subcomunidades.subcomunidades.colecciones' => $conteos,
            ])
            ->get();

        return view('admin.repositorio.estructura.index', [
            'comunidades' => $comunidades,
            'totalItems' => RepoItem::count(),
        ]);
    }

    /* ───────── Comunidades ───────── */

    public function crearComunidad(Request $request)
    {
        return view('admin.repositorio.estructura.comunidad-form', [
            'comunidad' => new RepoComunidad([
                'parent_id' => $request->integer('parent_id') ?: null,
                'orden' => (RepoComunidad::max('orden') ?? 0) + 1,
            ]),
            'padres' => $this->padresPosibles(),
        ]);
    }

    public function guardarComunidad(Request $request)
    {
        $comunidad = RepoComunidad::create($this->validarComunidad($request));

        return redirect()->route('admin.repositorio.estructura.index')
            ->with('success', "Comunidad '{$comunidad->nombre}' creada.");
    }

    public function editarComunidad(RepoComunidad $comunidad)
    {
        return view('admin.repositorio.estructura.comunidad-form', [
            'comunidad' => $comunidad,
            'padres' => $this->padresPosibles($comunidad),
        ]);
    }

    public function actualizarComunidad(Request $request, RepoComunidad $comunidad)
    {
        $comunidad->update($this->validarComunidad($request, $comunidad));

        return redirect()->route('admin.repositorio.estructura.index')
            ->with('success', "Comunidad '{$comunidad->nombre}' actualizada.");
    }

    public function eliminarComunidad(RepoComunidad $comunidad)
    {
        if ($comunidad->subcomunidades()->exists() || $comunidad->colecciones()->exists()) {
            return back()->with('error', "No se puede eliminar '{$comunidad->nombre}': primero elimina o mueve sus subcomunidades y colecciones.");
        }
        $comunidad->delete();

        return redirect()->route('admin.repositorio.estructura.index')
            ->with('success', "Comunidad '{$comunidad->nombre}' eliminada.");
    }

    /* ───────── Colecciones ───────── */

    public function crearColeccion(Request $request)
    {
        return view('admin.repositorio.estructura.coleccion-form', [
            'coleccion' => new RepoColeccion([
                'comunidad_id' => $request->integer('comunidad_id') ?: null,
                'orden' => 1,
            ]),
            'comunidades' => $this->padresPosibles(),
            'tipos' => RepoItem::TIPOS_DOCUMENTO,
            'programas' => RepoItem::PROGRAMAS,
            'grados' => RepoItem::GRADOS,
        ]);
    }

    public function guardarColeccion(Request $request)
    {
        $coleccion = RepoColeccion::create($this->validarColeccion($request));

        return redirect()->route('admin.repositorio.estructura.index')
            ->with('success', "Colección '{$coleccion->nombre}' creada.");
    }

    public function editarColeccion(RepoColeccion $coleccion)
    {
        return view('admin.repositorio.estructura.coleccion-form', [
            'coleccion' => $coleccion,
            'comunidades' => $this->padresPosibles(),
            'tipos' => RepoItem::TIPOS_DOCUMENTO,
            'programas' => RepoItem::PROGRAMAS,
            'grados' => RepoItem::GRADOS,
        ]);
    }

    public function actualizarColeccion(Request $request, RepoColeccion $coleccion)
    {
        $coleccion->update($this->validarColeccion($request));

        return redirect()->route('admin.repositorio.estructura.index')
            ->with('success', "Colección '{$coleccion->nombre}' actualizada.");
    }

    public function eliminarColeccion(RepoColeccion $coleccion)
    {
        if ($coleccion->items()->exists()) {
            return back()->with('error', "No se puede eliminar '{$coleccion->nombre}' porque tiene documentos. Muévelos a otra colección primero.");
        }
        $coleccion->delete();

        return redirect()->route('admin.repositorio.estructura.index')
            ->with('success', "Colección '{$coleccion->nombre}' eliminada.");
    }

    /* ───────── Apoyo ───────── */

    private function validarComunidad(Request $request, ?RepoComunidad $comunidad = null): array
    {
        $prohibidos = $comunidad ? $comunidad->idsDescendientes() : [];

        $data = $request->validate([
            'nombre' => 'required|string|max:180',
            'parent_id' => ['nullable', 'integer', 'exists:repo_comunidades,id', Rule::notIn($prohibidos)],
            'descripcion' => 'nullable|string|max:5000',
            'orden' => 'nullable|integer|min:0|max:9999',
        ], [
            'parent_id.not_in' => 'Una comunidad no puede quedar dentro de sí misma ni de sus subcomunidades.',
        ]);

        $data['orden'] = $data['orden'] ?? 0;

        return $data;
    }

    private function validarColeccion(Request $request): array
    {
        $data = $request->validate([
            'comunidad_id' => 'required|exists:repo_comunidades,id',
            'nombre' => 'required|string|max:180',
            'descripcion' => 'nullable|string|max:5000',
            'programa' => 'nullable|string|max:20',
            'tipo_documento' => 'nullable|string|max:80',
            'grado_academico' => 'nullable|string|max:80',
            'duracion' => 'nullable|string|max:40',
            'orden' => 'nullable|integer|min:0|max:9999',
        ]);

        $data['orden'] = $data['orden'] ?? 0;

        return $data;
    }

    /** Comunidades ordenadas con su ruta, excluyendo la propia y sus descendientes. */
    private function padresPosibles(?RepoComunidad $excluir = null)
    {
        $prohibidos = $excluir ? $excluir->idsDescendientes() : [];

        return RepoComunidad::with('padre')
            ->whereNotIn('id', $prohibidos)
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get()
            ->sortBy('ruta', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
    }
}
