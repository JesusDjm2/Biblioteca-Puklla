@extends('layouts.app')
@section('titulo', 'Secciones')
@section('contenido')
    @include('partials.biblioteca-virtual-crud-header', [
        'active' => 'secciones',
        'stats' => [['valor' => $secciones->total(), 'singular' => 'sección', 'plural' => 'secciones', 'icono' => 'fa-layer-group']],
        'botones' => [['route' => route('secciones.create'), 'label' => 'Nueva sección']],
    ])

    <p class="bv-crud-meta mb-3">Nivel superior del catálogo virtual; cada sección contiene categorías.</p>

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
                <input type="text" id="sec-q" name="q" class="form-control form-control-sm bv-toolbar-control"
                    value="{{ request('q') }}" placeholder="Nombre o fragmento de URL…">
            </div>
            <div class="form-group col-md-4 mb-0">
                <label class="small mb-1 d-block invisible" aria-hidden="true">Acciones</label>
                <div class="btn-group btn-group-sm bv-toolbar-btn-group w-100">
                    <button type="submit" class="btn btn-sm btn-info">Filtrar</button>
                    <a href="{{ route('secciones.index') }}" class="btn btn-sm btn-outline-secondary">Limpiar</a>
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
                                data-url="{{ route('secciones.destroy', $seccion->id) }}"
                                data-nombre="{{ $seccion->nombre }}" title="Eliminar"><i
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

    <form id="deleteForm" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>

    <script>
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-del-sec');
            if (!btn) return;
            window.bvConfirm({
                titulo: 'Eliminar sección',
                mensaje: '¿Eliminar la sección "' + btn.dataset.nombre + '"? Esta acción no se puede deshacer.',
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
