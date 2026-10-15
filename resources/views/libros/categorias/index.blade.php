@extends('layouts.app')
@section('titulo', 'Categorias')
@section('contenido')
    @include('partials.biblioteca-virtual-crud-header', [
        'active' => 'categorias',
        'stats' => [['valor' => $categorias->total(), 'singular' => 'categoría', 'plural' => 'categorías', 'icono' => 'fa-folder']],
        'botones' => [['route' => route('categorias.create'), 'label' => 'Nueva categoría']],
    ])

    <p class="bv-crud-meta mb-3">Agrupan libros dentro de cada sección. Busque por nombre, URL o sección.</p>

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
            <div class="form-group col-md-5 mb-2 mb-md-0">
                <label for="cat-q" class="small text-muted mb-1 d-block"><i class="fa fa-search mr-1"></i>Buscar</label>
                <input type="text" id="cat-q" name="q" class="form-control form-control-sm bv-toolbar-control"
                    value="{{ request('q') }}" placeholder="Nombre o fragmento de URL…" autocomplete="off">
            </div>
            <div class="form-group col-md-4 mb-2 mb-md-0">
                <label for="cat-seccion" class="small text-muted mb-1 d-block"><i class="fa fa-folder mr-1"></i>Sección</label>
                <select id="cat-seccion" name="seccion_id" class="custom-select custom-select-sm bv-toolbar-control">
                    <option value="">Todas</option>
                    @foreach ($seccionesFiltro as $s)
                        <option value="{{ $s->id }}" @selected((string) request('seccion_id') === (string) $s->id)>{{ $s->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-3 mb-0">
                <label class="small mb-1 d-block invisible" aria-hidden="true">Acciones</label>
                <div class="btn-group btn-group-sm bv-toolbar-btn-group w-100">
                    <button type="submit" class="btn btn-sm btn-info"><i class="fa fa-filter mr-1"></i>Filtrar</button>
                    <a href="{{ route('categorias.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fa fa-times mr-1"></i>Limpiar</a>
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
                            <div class="bv-cell-title">
                                <i class="fa fa-tag"></i>
                                <a href="{{ route('detallesCat', $categoria->id) }}">{{ $categoria->nombre }}</a>
                            </div>
                            <div class="bv-cell-sub text-truncate" style="max-width:14rem;">/{{ $categoria->url }}</div>
                        </td>
                        <td>
                            @if ($categoria->seccion)
                                <span class="bv-seccat-chip">
                                    <a href="{{ route('secciones.show', $categoria->seccion->id) }}"
                                        class="bv-seccat-chip__seccion" title="Sección">
                                        <i class="fa fa-folder"></i>{{ $categoria->seccion->nombre }}
                                    </a>
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="bv-stat-num" title="Libros en esta categoría">
                                <i class="fa fa-book"></i>{{ $categoria->libros_count }}
                            </span>
                        </td>
                        <td class="bv-crud-actions text-nowrap">
                            <a href="{{ route('categorias.show', $categoria->url) }}" class="btn btn-sm btn-outline-info"
                                title="Vista pública"><i class="fa fa-eye"></i></a>
                            <a href="{{ route('categorias.edit', $categoria->id) }}" class="btn btn-sm btn-warning"
                                title="Editar"><i class="fa fa-edit"></i></a>
                            <button type="button" class="btn btn-sm btn-danger btn-del-cat"
                                data-url="{{ route('categorias.destroy', $categoria->id) }}"
                                data-nombre="{{ $categoria->nombre }}"
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

    <form id="deleteForm" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>

    <script>
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-del-cat');
            if (!btn) return;
            window.bvConfirm({
                titulo: 'Eliminar categoría',
                mensaje: '¿Eliminar la categoría "' + btn.dataset.nombre + '"? Esta acción no se puede deshacer.',
                tono: 'danger',
                icono: 'fa-trash',
                textoConfirmar: 'Eliminar',
            }).then(function (ok) {
                if (!ok) return;
                var f = document.getElementById('deleteForm');
                f.action = btn.dataset.url;
                f.submit();
            });
        });
    </script>
@endsection
