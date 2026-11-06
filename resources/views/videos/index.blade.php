@extends('layouts.app')
@section('titulo', 'Videos')
@section('contenido')
    @include('partials.videos-crud-header', [
        'active' => 'videos',
        'stats' => [['valor' => $videos->total(), 'singular' => 'video', 'plural' => 'videos', 'icono' => 'fa-film']],
        'botones' => [
            ['route' => route('videos.import.form'), 'label' => 'Importar masivamente', 'icono' => 'fa-upload', 'clase' => 'btn-outline-info'],
            ['route' => route('videos.create', array_filter(['categoria_id' => request('categoria_id')])), 'label' => 'Agregar video', 'icono' => 'fa-plus'],
        ],
    ])

    @include('material-didactico.partials.alertas')

    <form method="get" action="{{ route('videos.index') }}" class="bv-crud-toolbar">
        <div class="form-row align-items-end">
            <div class="form-group col-md-4 mb-2 mb-md-0">
                <label for="vid-q" class="small text-muted mb-1 d-block"><i class="fa fa-search mr-1"></i>Buscar</label>
                <input type="text" id="vid-q" name="q" class="form-control form-control-sm bv-toolbar-control"
                    value="{{ request('q') }}" placeholder="Nombre o descripción…" autocomplete="off">
            </div>
            <div class="form-group col-md-3 mb-2 mb-md-0">
                <label for="vid-cat" class="small text-muted mb-1 d-block"><i class="fa fa-folder mr-1"></i>Categoría</label>
                <select id="vid-cat" name="categoria_id" class="form-control form-control-sm bv-toolbar-control">
                    <option value="">Todas</option>
                    @foreach ($categorias as $cat)
                        <option value="{{ $cat->id }}" @selected((string) request('categoria_id') === (string) $cat->id)>
                            {{ $cat->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-2 mb-2 mb-md-0">
                <label for="vid-fuente" class="small text-muted mb-1 d-block"><i class="fa fa-link mr-1"></i>Origen</label>
                <select id="vid-fuente" name="fuente" class="custom-select custom-select-sm bv-toolbar-control">
                    <option value="">Todos</option>
                    <option value="youtube" @selected(request('fuente') === 'youtube')>YouTube</option>
                    <option value="drive" @selected(request('fuente') === 'drive')>Drive</option>
                    <option value="archivo" @selected(request('fuente') === 'archivo')>Subidos</option>
                </select>
            </div>
            <div class="form-group col-md-3 mb-0">
                <label class="small mb-1 d-block invisible" aria-hidden="true">Acciones</label>
                <div class="btn-group btn-group-sm bv-toolbar-btn-group w-100">
                    <button type="submit" class="btn btn-info"><i class="fa fa-filter mr-1"></i>Filtrar</button>
                    <a href="{{ route('videos.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                </div>
            </div>
        </div>
    </form>

    @if ($modoOrden && $videos->total() > 1)
        <div class="alert bv-alert alert-light d-flex flex-wrap align-items-center justify-content-between" style="gap:.5rem;">
            <span class="small">
                <i class="fa fa-arrows-v mr-1"></i> <strong>Arrastra</strong> cada video desde <i class="fa fa-bars"></i> para cambiar el orden en que se ve y reproduce en la web.
                Se guarda solo al soltar.
            </span>
            <span class="d-flex align-items-center" style="gap:.5rem;">
                <span class="vo-estado small" id="vo-estado" aria-live="polite"></span>
                <a href="{{ route('videoscat.ordenar', request('categoria_id')) }}" class="btn btn-sm btn-outline-info" title="Ordenar por nombre, fecha o vistas">
                    <i class="fa fa-magic mr-1"></i>Ordenar rápido
                </a>
            </span>
        </div>
    @elseif (!request('categoria_id') && $videos->total() > 1)
        <p class="small text-muted mb-2"><i class="fa fa-info-circle mr-1"></i> Para cambiar el orden de los videos, elige una <strong>categoría</strong> en el filtro y arrástralos.</p>
    @endif

    @if ($videos->total() > 0)
        <p class="bv-crud-meta mb-2">
            Mostrando {{ $videos->firstItem() }}–{{ $videos->lastItem() }} de {{ $videos->total() }} resultado(s)
        </p>
    @endif

    <div class="bv-crud-table-wrap table-responsive">
        <table class="table table-hover table-sm">
            <thead class="thead-light">
                <tr>
                    @if ($modoOrden)<th style="width:2.2rem;" aria-label="Mover"></th>@endif
                    <th style="width:3rem;">#</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th style="width:9rem;">Origen</th>
                    <th style="width:6rem;" class="text-center">Vistas</th>
                    <th style="width:8rem;" class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody @if ($modoOrden) id="vo-lista" @endif>
                @forelse ($videos as $video)
                    <tr class="bv-table-row" data-id="{{ $video->id }}">
                        @if ($modoOrden)
                            <td class="align-middle text-center vo-asa" title="Arrastrar para cambiar el orden"><i class="fa fa-bars"></i></td>
                        @endif
                        <td class="text-muted small align-middle vo-num">{{ $videos->firstItem() + $loop->index }}</td>
                        <td class="align-middle">
                            <div class="font-weight-bold">{{ $video->nombre }}</div>
                            @if ($video->descripcion)
                                <span class="small text-muted">{{ \Illuminate\Support\Str::limit($video->descripcion, 90) }}</span>
                            @endif
                        </td>
                        <td class="small align-middle">{{ $video->categoria?->nombre ?? '—' }}</td>
                        <td class="align-middle">
                            @switch($video->fuente)
                                @case('youtube')
                                    <a class="btn btn-sm btn-outline-danger" target="_blank" rel="noopener"
                                        href="https://www.youtube.com/watch?v={{ $video->youtube }}">
                                        <i class="fa fa-youtube-play mr-1"></i>YouTube
                                    </a>
                                    @break
                                @case('drive')
                                    <a class="btn btn-sm btn-outline-success" target="_blank" rel="noopener"
                                        href="https://drive.google.com/file/d/{{ $video->drive }}/view">
                                        <i class="fa fa-google mr-1"></i>Drive
                                    </a>
                                    @break
                                @case('archivo')
                                    <a class="btn btn-sm btn-outline-info" target="_blank" rel="noopener"
                                        href="{{ $video->archivo_url }}">
                                        <i class="fa fa-file-video-o mr-1"></i>Subido
                                    </a>
                                    @break
                                @default
                                    <span class="text-muted small">Sin video</span>
                            @endswitch
                        </td>
                        <td class="text-center small align-middle" title="Visitas a la página pública del video">
                            <i class="fa fa-eye text-muted"></i> {{ number_format((int) $video->vistas) }}
                        </td>
                        <td class="bv-crud-actions text-center text-nowrap align-middle">
                            <a href="{{ $video->url_publica }}" target="_blank" rel="noopener"
                                class="btn btn-sm btn-outline-secondary" title="Ver página pública (para compartir)">
                                <i class="fa fa-external-link"></i>
                            </a>
                            <a href="{{ route('videos.edit', $video->id) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                <i class="fa fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-video"
                                data-url="{{ route('videos.destroy', $video->id) }}"
                                data-nombre="{{ $video->nombre }}" title="Eliminar">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $modoOrden ? 7 : 6 }}" class="p-0 border-0">
                            <div class="bv-empty-state">
                                <i class="fa fa-film bv-empty-icon"></i>
                                <p>No se encontraron videos.</p>
                                <a href="{{ route('videos.create') }}" class="btn btn-sm btn-info">
                                    <i class="fa fa-plus mr-1"></i>Agregar video
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bv-crud-pagination">{{ $videos->links() }}</div>

    <form id="deleteFormVideo" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>

    <script>
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-delete-video');
            if (!btn) return;
            window.bvConfirm({
                titulo: 'Eliminar video',
                mensaje: '¿Eliminar el video "' + btn.dataset.nombre + '"? Si fue subido al servidor, también se borrará el archivo.',
                tono: 'danger',
                icono: 'fa-trash',
                textoConfirmar: 'Eliminar',
            }).then(function (ok) {
                if (!ok) return;
                var f = document.getElementById('deleteFormVideo');
                f.action = btn.dataset.url;
                f.submit();
            });
        });
    </script>
@endsection

@push('styles')
<style>
    .vo-asa { cursor: grab; color: var(--bv-text-muted); touch-action: none; }
    .vo-asa:hover { color: var(--bv-accent); }
    .vo-asa:active { cursor: grabbing; }
    #vo-lista tr.sortable-ghost { opacity: .35; background: var(--bv-accent-soft); }
    #vo-lista tr.sortable-chosen { box-shadow: var(--bv-shadow-lg); }
    #vo-lista tr.sortable-drag { background: var(--bv-surface); }
    .vo-estado { white-space: nowrap; font-weight: 600; }
    .vo-estado.is-ok { color: #2e9e62; }
    .vo-estado.is-error { color: var(--bv-danger); }
    :root[data-theme="dark"] .vo-estado.is-ok { color: #7bd8a4; }
</style>
@endpush

@push('scripts')
@if ($modoOrden)
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
@endif
<script>
    (function () {
        window.bvPageAnimated = true;
        // Solo se animan las primeras filas: con muchas, la entrada tardaría demasiado.
        var rows = Array.prototype.slice.call(document.querySelectorAll('.bv-table-row'), 0, 25);
        if (rows.length) {
            gsap.from(rows, { opacity: 0, y: 12, duration: 0.32, stagger: 0.03, ease: 'power2.out', clearProps: 'transform,opacity' });
        }
        gsap.from('.bv-crud-toolbar', { opacity: 0, y: -8, duration: 0.3, delay: 0.05, ease: 'power2.out' });
    })();

    @if ($modoOrden)
    (function () {
        var lista = document.getElementById('vo-lista');
        var estado = document.getElementById('vo-estado');
        if (!lista || !window.Sortable) return;
        var url = @json(route('videoscat.ordenar.guardar', request('categoria_id')));
        var token = @json(csrf_token());
        var envio = 0;
        var ocultar;

        function mostrar(texto, clase) {
            if (!estado) return;
            clearTimeout(ocultar);
            estado.className = 'vo-estado small ' + (clase || '');
            estado.innerHTML = texto;
            if (clase === 'is-ok') ocultar = setTimeout(function () { estado.innerHTML = ''; }, 2500);
        }

        function guardar() {
            var ids = Array.prototype.map.call(lista.querySelectorAll('tr[data-id]'), function (tr, i) {
                tr.querySelector('.vo-num').textContent = i + 1;
                return parseInt(tr.dataset.id, 10);
            });
            var actual = ++envio;
            mostrar('<i class="fa fa-spinner fa-spin"></i> Guardando…');
            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest', 'X-HTTP-Method-Override': 'PUT' },
                body: JSON.stringify({ orden: ids }),
            }).then(function (r) {
                if (!r.ok) throw new Error(r.status);
                if (actual === envio) mostrar('<i class="fa fa-check"></i> Orden guardado', 'is-ok');
            }).catch(function () {
                if (actual === envio) mostrar('<i class="fa fa-exclamation-triangle"></i> No se pudo guardar. Recarga la página e inténtalo de nuevo.', 'is-error');
            });
        }

        Sortable.create(lista, {
            handle: '.vo-asa',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            onEnd: function (e) { if (e.oldIndex !== e.newIndex) guardar(); },
        });
    })();
    @endif
</script>
@endpush
