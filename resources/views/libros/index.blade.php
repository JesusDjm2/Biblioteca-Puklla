@extends('layouts.app')
@section('titulo', 'Libros')
@section('contenido')
    @include('partials.biblioteca-virtual-crud-header', [
        'active' => 'libros',
        'stats' => [['valor' => $libros->total(), 'singular' => 'registro', 'plural' => 'registros', 'icono' => 'fa-book']],
        'botones' => [
            ['route' => route('libros.import.form'), 'label' => 'Importar masivamente', 'icono' => 'fa-upload', 'clase' => 'btn-outline-info'],
            ['route' => route('libros.create'), 'label' => 'Nuevo libro'],
        ],
    ])

    <p class="bv-crud-meta mb-3">Gestiona textos, autores y vínculos a Drive; filtra por sección o categoría.</p>

    @if (session('success'))
        <div class="alert alert-success bv-alert alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span
                    aria-hidden="true">&times;</span></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger bv-alert alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span
                    aria-hidden="true">&times;</span></button>
        </div>
    @endif

    <form method="get" action="{{ route('libros.index') }}" class="bv-crud-toolbar">
        <div class="form-row align-items-end">
            <div class="form-group col-md-4 mb-2 mb-md-0">
                <label for="filtro-q" class="small text-muted mb-1 d-block">Buscar</label>
                <input type="text" id="filtro-q" name="q" class="form-control form-control-sm bv-toolbar-control"
                    value="{{ request('q') }}" placeholder="Título, autor o ID de archivo…">
            </div>
            <div class="form-group col-md-4 col-lg-3 mb-2 mb-md-0">
                <label for="filtro-seccion" class="small text-muted mb-1 d-block">Sección</label>
                <select id="filtro-seccion" name="seccion_id" class="custom-select custom-select-sm bv-toolbar-control">
                    <option value="">Todas</option>
                    @foreach ($seccionesFiltro as $s)
                        <option value="{{ $s->id }}" @selected((string) request('seccion_id') === (string) $s->id)>{{ $s->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-4 col-lg-3 mb-2 mb-md-0">
                <label for="filtro-cat" class="small text-muted mb-1 d-block">Categoría</label>
                <select id="filtro-cat" name="categoria_id" class="custom-select custom-select-sm bv-toolbar-control">
                    <option value="">Todas</option>
                    @foreach ($categoriasFiltro as $c)
                        <option value="{{ $c->id }}" @selected((string) request('categoria_id') === (string) $c->id)>
                            {{ $c->seccion?->nombre ?? '—' }} › {{ $c->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-12 col-lg-2 mb-0">
                <label class="small mb-1 d-block invisible" aria-hidden="true">Acciones</label>
                <div class="btn-group btn-group-sm bv-toolbar-btn-group w-100">
                    <button type="submit" class="btn btn-info">Filtrar</button>
                    <a href="{{ route('libros.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                </div>
            </div>
        </div>
    </form>

    <p class="bv-crud-meta">
        @if ($libros->total() > 0)
            Mostrando {{ $libros->firstItem() }}–{{ $libros->lastItem() }} de {{ $libros->total() }} registros
        @else
            Sin resultados con los filtros actuales.
        @endif
    </p>

    <div class="bv-crud-table-wrap table-responsive">
        <table class="table table-hover table-sm">
            <thead class="thead-light">
                <tr>
                    <th style="width:3rem;">#</th>
                    <th>Libro</th>
                    <th>Sección y categoría</th>
                    <th style="width:7rem;">ID Drive</th>
                    <th style="width:9rem;">Acciones</th>
                </tr>
            </thead>
            <tbody id="librosTable">
                @forelse ($libros as $libro)
                    <tr class="libro-item">
                        <td>{{ $libros->firstItem() + $loop->index }}</td>
                        <td>
                            <div class="bv-libro-identidad">
                                <div class="bv-cell-title">
                                    <i class="fa fa-book"></i>{{ $libro->nombre }}
                                </div>
                                <div class="bv-cell-sub">{{ $libro->autor }}</div>
                            </div>
                        </td>
                        <td>
                            @forelse ($libro->categorias as $categoria)
                                <div class="bv-seccat-chip">
                                    @if ($categoria->seccion)
                                        <a href="{{ route('secciones.show', $categoria->seccion->id) }}"
                                            class="bv-seccat-chip__seccion" title="Sección">
                                            <i class="fa fa-folder"></i>{{ $categoria->seccion->nombre }}
                                        </a>
                                        <i class="fa fa-angle-right bv-seccat-chip__sep"></i>
                                    @endif
                                    <a href="{{ route('detallesCat', $categoria->id) }}"
                                        class="bv-seccat-chip__categoria" title="Categoría">
                                        <i class="fa fa-tag"></i>{{ $categoria->nombre }}
                                    </a>
                                </div>
                            @empty
                                <span class="text-muted small"><em>Sin categoría</em></span>
                            @endforelse
                        </td>
                        <td class="small text-truncate" style="max-width:7rem;" title="{{ $libro->identificador }}">
                            {{ \Illuminate\Support\Str::limit($libro->identificador, 14) }}</td>
                        <td class="bv-crud-actions text-nowrap">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-info" title="Vista previa"
                                    onclick="abrirVistaPreviaLibro({{ \Illuminate\Support\Js::from($libro->identificador) }}, {{ \Illuminate\Support\Js::from($libro->nombre) }})"><i
                                        class="fa fa-eye"></i></button>
                                <a href="{{ route('libros.edit', $libro->id) }}" class="btn btn-warning"
                                    title="Editar"><i class="fa fa-edit"></i></a>
                                <button type="button" class="btn btn-danger btn-delete"
                                    data-url="{{ route('libros.destroy', $libro->id) }}"
                                    data-nombre="{{ $libro->nombre }}" title="Eliminar"><i class="fa fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="bv-crud-empty">No hay libros que coincidan. Pruebe otros filtros o cree
                            un registro nuevo.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bv-crud-pagination">{{ $libros->links() }}</div>

    <form id="deleteForm" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>

    <script>
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-delete');
            if (!btn) return;
            window.bvConfirm({
                titulo: 'Eliminar libro',
                mensaje: '¿Eliminar el libro "' + btn.dataset.nombre + '"? Esta acción no se puede deshacer.',
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
