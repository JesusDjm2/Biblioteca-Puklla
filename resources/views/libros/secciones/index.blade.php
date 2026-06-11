@extends('layouts.app')
@section('titulo', 'Secciones')
@section('contenido')
    @include('partials.biblioteca-virtual-crud-header', ['active' => 'secciones'])

    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h1 class="bv-crud-page-title mb-0">Secciones</h1>
            <p class="bv-crud-meta">Nivel superior del catálogo virtual; cada sección contiene categorías.</p>
        </div>
        <a href="{{ route('secciones.create') }}" class="btn btn-info btn-sm mt-2">+ Nueva sección</a>
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

    <form method="get" action="{{ route('secciones.index') }}" class="bv-crud-toolbar">
        <div class="form-row align-items-end">
            <div class="form-group col-md-8 mb-2 mb-md-0">
                <label for="sec-q" class="small text-muted mb-1 d-block">Buscar</label>
                <input type="text" id="sec-q" name="q" class="form-control form-control-sm" value="{{ request('q') }}"
                    placeholder="Nombre o fragmento de URL…">
            </div>
            <div class="form-group col-md-4 mb-0">
                <div class="btn-group btn-group-sm w-100">
                    <button type="submit" class="btn btn-info">Filtrar</button>
                    <a href="{{ route('secciones.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                </div>
            </div>
        </div>
    </form>

    <p class="bv-crud-meta">
        @if ($secciones->total() > 0)
            Mostrando {{ $secciones->firstItem() }}–{{ $secciones->lastItem() }} de {{ $secciones->total() }}
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
                    <th>Categorías</th>
                    <th style="width:9rem;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($secciones as $seccion)
                    <tr>
                        <td>{{ $secciones->firstItem() + $loop->index }}</td>
                        <td>
                            <div class="font-weight-bold">{{ $seccion->nombre }}</div>
                            <div class="small text-muted">/{{ $seccion->url }}</div>
                        </td>
                        <td>
                            @if ($seccion->categorias->isEmpty())
                                <span class="text-muted small"><em>Sin categorías</em></span>
                            @else
                                <ul class="list-unstyled small mb-0">
                                    @foreach ($seccion->categorias as $categoria)
                                        <li>
                                            <a href="{{ route('detallesCat', $categoria->id) }}">{{ $categoria->nombre }}</a>
                                            <span class="text-muted">({{ $categoria->libros_count }})</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </td>
                        <td class="bv-crud-actions text-nowrap">
                            <a href="{{ route('secciones.show', $seccion->id) }}" class="btn btn-sm btn-outline-info"
                                title="Detalle"><i class="fa fa-eye"></i></a>
                            <a href="{{ route('secciones.edit', $seccion->id) }}" class="btn btn-sm btn-warning"
                                title="Editar"><i class="fa fa-edit"></i></a>
                            <button type="button" class="btn btn-sm btn-danger btn-del-sec"
                                data-url="{{ route('secciones.destroy', $seccion->id) }}" title="Eliminar"><i
                                    class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="bv-crud-empty">No hay secciones con estos criterios.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bv-crud-pagination">{{ $secciones->links() }}</div>

    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmar eliminación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">¿Eliminar esta sección?</div>
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
            const btn = e.target.closest('.btn-del-sec');
            if (!btn) return;
            document.getElementById('deleteForm').action = btn.getAttribute('data-url');
            $('#deleteModal').modal('show');
        });
    </script>
@endsection
