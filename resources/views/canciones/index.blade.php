@extends('layouts.app')
@section('titulo', 'Canciones')
@section('contenido')
    @include('partials.canciones-crud-header', [
        'active' => 'canciones',
        'stats' => [['valor' => $canciones->total(), 'singular' => 'canción', 'plural' => 'canciones', 'icono' => 'fa-music']],
        'botones' => [
            ['route' => route('canciones.create', array_filter(['categoria_id' => request('categoria_id')])), 'label' => 'Nueva canción', 'icono' => 'fa-plus'],
        ],
    ])

    @include('material-didactico.partials.alertas')

    <form method="get" action="{{ route('canciones.index') }}" class="bv-crud-toolbar">
        <div class="form-row align-items-end">
            <div class="form-group col-md-4 mb-2 mb-md-0">
                <label for="can-q" class="small text-muted mb-1 d-block"><i class="fa fa-search mr-1"></i>Buscar</label>
                <input type="text" id="can-q" name="q" class="form-control form-control-sm bv-toolbar-control"
                    value="{{ request('q') }}" placeholder="Nombre o autor…" autocomplete="off">
            </div>
            <div class="form-group col-md-3 mb-2 mb-md-0">
                <label for="can-cat" class="small text-muted mb-1 d-block"><i class="fa fa-folder mr-1"></i>Categoría</label>
                <select id="can-cat" name="categoria_id" class="custom-select custom-select-sm bv-toolbar-control">
                    <option value="">Todas</option>
                    @foreach ($categorias as $cat)
                        <option value="{{ $cat->id }}" @selected((string) request('categoria_id') === (string) $cat->id)>{{ $cat->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-2 mb-2 mb-md-0">
                <label for="can-plat" class="small text-muted mb-1 d-block"><i class="fa fa-link mr-1"></i>Plataforma</label>
                <select id="can-plat" name="plataforma" class="custom-select custom-select-sm bv-toolbar-control">
                    <option value="">Todas</option>
                    <option value="youtube" @selected(request('plataforma') === 'youtube')>YouTube</option>
                    <option value="spotify" @selected(request('plataforma') === 'spotify')>Spotify</option>
                    <option value="drive" @selected(request('plataforma') === 'drive')>Drive</option>
                </select>
            </div>
            <div class="form-group col-md-3 mb-0">
                <label class="small mb-1 d-block invisible" aria-hidden="true">Acciones</label>
                <div class="btn-group btn-group-sm bv-toolbar-btn-group w-100">
                    <button type="submit" class="btn btn-info"><i class="fa fa-filter mr-1"></i>Filtrar</button>
                    <a href="{{ route('canciones.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                </div>
            </div>
        </div>
    </form>

    <p class="bv-crud-meta mb-2">
        @if ($canciones->total() > 0)
            Mostrando {{ $canciones->firstItem() }}–{{ $canciones->lastItem() }} de {{ $canciones->total() }} canciones
        @else
            Sin resultados con los filtros actuales.
        @endif
    </p>

    @php
        $plataformas = [
            'youtube' => ['YouTube', 'fa-youtube-play', 'btn-outline-danger'],
            'spotify' => ['Spotify', 'fa-spotify', 'btn-outline-success'],
            'drive' => ['Drive', 'fa-google', 'btn-outline-info'],
        ];
    @endphp

    <div class="bv-crud-table-wrap table-responsive">
        <table class="table table-hover table-sm">
            <thead class="thead-light">
                <tr>
                    <th style="width:3rem;">#</th>
                    <th>Canción</th>
                    <th>Categoría</th>
                    <th style="width:8rem;">Plataforma</th>
                    <th style="width:7rem;" class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($canciones as $cancion)
                    <tr class="bv-table-row">
                        <td class="text-muted small align-middle">{{ $canciones->firstItem() + $loop->index }}</td>
                        <td class="align-middle">
                            <div class="font-weight-bold">{{ $cancion->nombre }}</div>
                            <span class="small text-muted"><i class="fa fa-user"></i> {{ $cancion->autor }}</span>
                        </td>
                        <td class="small align-middle">{{ $cancion->categoria?->nombre ?? '—' }}</td>
                        <td class="align-middle">
                            @if ($p = $plataformas[$cancion->plataforma] ?? null)
                                <a class="btn btn-sm {{ $p[2] }}" target="_blank" rel="noopener" href="{{ $cancion->enlace }}">
                                    <i class="fa {{ $p[1] }} mr-1"></i>{{ $p[0] }}
                                </a>
                            @else
                                <span class="text-muted small">Sin enlace</span>
                            @endif
                        </td>
                        <td class="bv-crud-actions text-center text-nowrap align-middle">
                            <a href="{{ route('canciones.edit', $cancion->id) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                <i class="fa fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete"
                                data-url="{{ route('canciones.destroy', $cancion->id) }}"
                                data-nombre="{{ $cancion->nombre }}" data-titulo="Eliminar canción" title="Eliminar">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-0 border-0">
                            <div class="bv-empty-state">
                                <i class="fa fa-music bv-empty-icon"></i>
                                <p>No se encontraron canciones.</p>
                                <a href="{{ route('canciones.create', array_filter(['categoria_id' => request('categoria_id')])) }}" class="btn btn-sm btn-info">
                                    <i class="fa fa-plus mr-1"></i>Nueva canción
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bv-crud-pagination">{{ $canciones->links() }}</div>

    <form id="deleteForm" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>

    @include('partials.cg-scripts', ['titulo' => 'Eliminar canción'])
@endsection

@push('scripts')
<script>
    (function () {
        if (typeof gsap === 'undefined') return;
        window.bvPageAnimated = true;
        var rows = document.querySelectorAll('.bv-table-row');
        if (rows.length) gsap.from(rows, { opacity: 0, y: 12, duration: 0.32, stagger: 0.04, ease: 'power2.out' });
    })();
</script>
@endpush
