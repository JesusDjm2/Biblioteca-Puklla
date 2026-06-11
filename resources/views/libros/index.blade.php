@extends('layouts.app')
@section('titulo', 'Libros')
@section('contenido')
    @include('partials.biblioteca-virtual-crud-header', ['active' => 'libros'])

    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h1 class="bv-crud-page-title mb-0">Libros (biblioteca virtual)</h1>
            <p class="bv-crud-meta">Gestiona textos, autores y vínculos a Drive; filtra por sección o categoría.</p>
        </div>
        <div class="mt-2">
            <a href="{{ route('libros.import.form') }}" class="btn btn-outline-info btn-sm mr-2"><i class="fa fa-upload"></i> Importar masivamente</a>
            <a href="{{ route('libros.create') }}" class="btn btn-info btn-sm">+ Nuevo libro</a>
        </div>
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

    <form method="get" action="{{ route('libros.index') }}" class="bv-crud-toolbar">
        <div class="form-row">
            <div class="form-group col-md-4 mb-2 mb-md-0">
                <label for="filtro-q" class="small text-muted mb-1 d-block">Buscar</label>
                <input type="text" id="filtro-q" name="q" class="form-control form-control-sm"
                    value="{{ request('q') }}" placeholder="Título, autor o ID de archivo…">
            </div>
            <div class="form-group col-md-4 col-lg-3 mb-2 mb-md-0">
                <label for="filtro-seccion" class="small text-muted mb-1 d-block">Sección</label>
                <select id="filtro-seccion" name="seccion_id" class="custom-select custom-select-sm">
                    <option value="">Todas</option>
                    @foreach ($seccionesFiltro as $s)
                        <option value="{{ $s->id }}" @selected((string) request('seccion_id') === (string) $s->id)>{{ $s->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-4 col-lg-3 mb-2 mb-md-0">
                <label for="filtro-cat" class="small text-muted mb-1 d-block">Categoría</label>
                <select id="filtro-cat" name="categoria_id" class="custom-select custom-select-sm">
                    <option value="">Todas</option>
                    @foreach ($categoriasFiltro as $c)
                        <option value="{{ $c->id }}" @selected((string) request('categoria_id') === (string) $c->id)>
                            {{ $c->seccion?->nombre ?? '—' }} › {{ $c->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-12 col-lg-2 d-flex align-items-end mb-0">
                <div class="btn-group btn-group-sm w-100">
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
                    <th>Texto / autor</th>
                    <th>Sección › categoría</th>
                    <th style="width:7rem;">ID Drive</th>
                    <th style="width:9rem;">Acciones</th>
                </tr>
            </thead>
            <tbody id="librosTable">
                @forelse ($libros as $libro)
                    <tr class="libro-item">
                        <td>{{ $libros->firstItem() + $loop->index }}</td>
                        <td>
                            <div class="font-weight-bold">{{ $libro->nombre }}</div>
                            <div class="text-muted small">{{ $libro->autor }}</div>
                        </td>
                        <td>
                            <ul class="list-unstyled small mb-0">
                                @forelse ($libro->categorias as $categoria)
                                    <li>
                                        @if ($categoria->seccion)
                                            <a
                                                href="{{ route('secciones.show', $categoria->seccion->id) }}">{{ $categoria->seccion->nombre }}</a>
                                            <span class="text-muted">›</span>
                                        @endif
                                        <a href="{{ route('detallesCat', $categoria->id) }}">{{ $categoria->nombre }}</a>
                                    </li>
                                @empty
                                    <li class="text-muted"><em>Sin categoría</em></li>
                                @endforelse
                            </ul>
                        </td>
                        <td class="small text-truncate" style="max-width:7rem;" title="{{ $libro->identificador }}">
                            {{ \Illuminate\Support\Str::limit($libro->identificador, 14) }}</td>
                        <td class="bv-crud-actions text-nowrap">
                            <a href="https://drive.google.com/file/d/{{ $libro->identificador }}/view" target="_blank"
                                rel="noopener" class="btn btn-sm btn-outline-info" title="Ver en Drive"><i
                                    class="fa fa-eye"></i></a>
                            <a href="{{ route('libros.edit', $libro->id) }}" class="btn btn-sm btn-warning"
                                title="Editar"><i class="fa fa-edit"></i></a>
                            <button type="button" class="btn btn-sm btn-danger btn-delete"
                                data-url="{{ route('libros.destroy', $libro->id) }}"
                                data-nombre="{{ $libro->nombre }}" title="Eliminar"><i class="fa fa-trash"></i></button>
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

    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmar eliminación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body text-center">
                    <p class="mb-0">¿Eliminar el libro <strong id="libroNombre" class="text-primary"></strong>?</p>
                </div>
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
            const btn = e.target.closest('.btn-delete');
            if (!btn) return;
            document.getElementById('deleteForm').action = btn.getAttribute('data-url');
            document.getElementById('libroNombre').textContent = btn.getAttribute('data-nombre');
            $('#deleteModal').modal('show');
        });
    </script>
@endsection
