@extends('layouts.app')
@section('titulo', 'Categorias')
@section('contenido')
    @include('partials.biblioteca-virtual-crud-header', ['active' => 'categorias'])

    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h1 class="bv-crud-page-title mb-0">Categorías</h1>
            <p class="bv-crud-meta">Agrupan libros dentro de cada sección. Busque por nombre, URL o sección.</p>
        </div>
        <a href="{{ route('categorias.create') }}" class="btn btn-info btn-sm mt-2">+ Nueva categoría</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span
                    aria-hidden="true">&times;</span></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span
                    aria-hidden="true">&times;</span></button>
        </div>
    @endif

    <form method="get" action="{{ route('categorias.index') }}" class="bv-crud-toolbar">
        <div class="form-row align-items-end">
            <div class="form-group col-md-6 mb-2 mb-md-0">
                <label for="cat-q" class="small text-muted mb-1 d-block">Buscar</label>
                <input type="text" id="cat-q" name="q" class="form-control form-control-sm" value="{{ request('q') }}"
                    placeholder="Nombre o fragmento de URL…">
            </div>
            <div class="form-group col-md-4 mb-2 mb-md-0">
                <label for="cat-seccion" class="small text-muted mb-1 d-block">Sección</label>
                <select id="cat-seccion" name="seccion_id" class="custom-select custom-select-sm">
                    <option value="">Todas</option>
                    @foreach ($seccionesFiltro as $s)
                        <option value="{{ $s->id }}" @selected((string) request('seccion_id') === (string) $s->id)>{{ $s->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-2 mb-0">
                <div class="btn-group btn-group-sm w-100">
                    <button type="submit" class="btn btn-info">Filtrar</button>
                    <a href="{{ route('categorias.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                </div>
            </div>
        </div>
    </form>

    <p class="bv-crud-meta">
        @if ($categorias->total() > 0)
            Mostrando {{ $categorias->firstItem() }}–{{ $categorias->lastItem() }} de {{ $categorias->total() }}
        @else
            Sin resultados.
        @endif
    </p>

    <div class="bv-crud-table-wrap table-responsive">
        <table class="table table-hover table-sm">
            <thead class="thead-light">
                <tr>
                    <th style="width:3rem;">#</th>
                    <th>Nombre</th>
                    <th>Sección</th>
                    <th>Libros</th>
                    <th style="width:9rem;">Acciones</th>
                </tr>
            </thead>
            <tbody id="categoryTable">
                @forelse ($categorias as $categoria)
                    <tr>
                        <td>{{ $categorias->firstItem() + $loop->index }}</td>
                        <td>
                            <a href="{{ route('detallesCat', $categoria->id) }}" class="font-weight-bold">{{ $categoria->nombre }}</a>
                            <div class="small text-muted text-truncate" style="max-width:14rem;">/{{ $categoria->url }}
                            </div>
                        </td>
                        <td>{{ $categoria->seccion?->nombre ?? '—' }}</td>
                        <td><span class="badge badge-pill badge-secondary">{{ $categoria->libros_count }}</span></td>
                        <td class="bv-crud-actions text-nowrap">
                            <a href="{{ route('categorias.show', $categoria->url) }}" class="btn btn-sm btn-outline-info"
                                title="Vista pública"><i class="fa fa-eye"></i></a>
                            <a href="{{ route('categorias.edit', $categoria->id) }}" class="btn btn-sm btn-warning"
                                title="Editar"><i class="fa fa-edit"></i></a>
                            <button type="button" class="btn btn-sm btn-danger btn-del-cat"
                                data-url="{{ route('categorias.destroy', $categoria->id) }}"
                                title="Eliminar"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="bv-crud-empty">No hay categorías con estos criterios.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bv-crud-pagination">{{ $categorias->links() }}</div>

    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmar eliminación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">¿Eliminar esta categoría?</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancelar</button>
                    <form id="deleteForm" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-del-cat');
            if (!btn) return;
            document.getElementById('deleteForm').action = btn.getAttribute('data-url');
            $('#deleteModal').modal('show');
        });
    </script>
@endsection
