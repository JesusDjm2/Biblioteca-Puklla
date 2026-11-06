@extends('layouts.app')
@section('titulo', 'Ordenar videos — '.$videoCategoria->nombre)
@section('contenido')
    @include('partials.videos-crud-header', ['active' => 'categorias'])
    @include('partials.admin-breadcrumb', [
        'items' => [
            ['label' => 'Panel', 'url' => route('home')],
            ['label' => 'Videos', 'url' => route('videoscat.index')],
            ['label' => 'Ordenar: '.$videoCategoria->nombre, 'url' => null],
        ],
    ])

    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h1 class="bv-crud-page-title mb-0">Ordenar videos <small class="text-muted" style="font-size:.6em;">en <i class="fa fa-film"></i> {{ $videoCategoria->nombre }}</small></h1>
            <p class="bv-crud-meta">Arrastra los videos (o usa las flechas) para definir el orden en que se ven y se reproducen en la web. El primero aparece primero.</p>
        </div>
        <a href="{{ route('videoscat.index') }}" class="btn btn-outline-secondary btn-sm mt-2"><i class="fa fa-arrow-left"></i> Volver</a>
    </div>

    @include('material-didactico.partials.errores')

    @if ($videos->isEmpty())
        <div class="bv-form-card text-center text-muted">Esta categoría todavía no tiene videos.</div>
    @else
        <form action="{{ route('videoscat.ordenar.guardar', $videoCategoria->id) }}" method="POST" id="form-orden">
            @csrf
            @method('PUT')

            <div class="ov-barra">
                <span class="small text-muted mr-1"><i class="fa fa-magic"></i> Ordenar rápido:</span>
                <div class="btn-group btn-group-sm flex-wrap" role="group" aria-label="Ordenar rápido">
                    <button type="button" class="btn btn-outline-secondary" data-ordenar="nombre"><i class="fa fa-sort-alpha-asc"></i> Por nombre</button>
                    <button type="button" class="btn btn-outline-secondary" data-ordenar="nombre-desc"><i class="fa fa-sort-alpha-desc"></i> Nombre (Z-A)</button>
                    <button type="button" class="btn btn-outline-secondary" data-ordenar="antiguos"><i class="fa fa-sort-numeric-asc"></i> Más antiguos primero</button>
                    <button type="button" class="btn btn-outline-secondary" data-ordenar="recientes"><i class="fa fa-sort-numeric-desc"></i> Más recientes primero</button>
                    <button type="button" class="btn btn-outline-secondary" data-ordenar="vistas"><i class="fa fa-eye"></i> Más vistos</button>
                </div>
                <button type="button" class="btn btn-sm btn-link text-muted" id="ov-deshacer" hidden><i class="fa fa-undo"></i> Deshacer cambios</button>
            </div>

            <ol class="ov-lista" id="ov-lista">
                @foreach ($videos as $video)
                    <li class="ov-item" data-id="{{ $video->id }}" data-nombre="{{ $video->nombre }}"
                        data-fecha="{{ $video->created_at?->timestamp ?? 0 }}" data-vistas="{{ (int) $video->vistas }}">
                        <input type="hidden" name="orden[]" value="{{ $video->id }}">
                        <span class="ov-asa" title="Arrastrar para mover" aria-hidden="true"><i class="fa fa-bars"></i></span>
                        <span class="ov-num">{{ $loop->iteration }}</span>
                        <span class="ov-mini">
                            @if ($video->fuente === 'archivo')
                                <i class="fa fa-file-video-o"></i>
                            @else
                                <img src="{{ $video->miniatura_url }}" alt="" loading="lazy">
                            @endif
                        </span>
                        <span class="ov-info">
                            <span class="ov-nombre">{{ $video->nombre }}</span>
                            <span class="ov-meta">
                                {{ $video->created_at?->format('d/m/Y') }} · <i class="fa fa-eye"></i> {{ number_format((int) $video->vistas) }}
                            </span>
                        </span>
                        <span class="ov-acciones">
                            <button type="button" class="cg-fila__btn" data-mover="inicio" title="Mover al inicio"><i class="fa fa-angle-double-up"></i></button>
                            <button type="button" class="cg-fila__btn" data-mover="arriba" title="Subir"><i class="fa fa-angle-up"></i></button>
                            <button type="button" class="cg-fila__btn" data-mover="abajo" title="Bajar"><i class="fa fa-angle-down"></i></button>
                            <button type="button" class="cg-fila__btn" data-mover="final" title="Mover al final"><i class="fa fa-angle-double-down"></i></button>
                        </span>
                    </li>
                @endforeach
            </ol>

            <div class="ov-guardar">
                <span class="small text-muted" id="ov-estado">Sin cambios.</span>
                <div>
                    <a href="{{ route('videoscat.index') }}" class="btn btn-secondary btn-sm mr-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary btn-sm px-4" id="ov-btn-guardar"><i class="fa fa-save mr-1"></i> Guardar orden</button>
                </div>
            </div>
        </form>
    @endif

    @push('styles')
        <style>
            .ov-barra { display: flex; flex-wrap: wrap; align-items: center; gap: .5rem; margin-bottom: .75rem; }
            .ov-lista { list-style: none; margin: 0 0 1rem; padding: 0; max-width: 60rem; }
            .ov-item {
                --cg: var(--bv-accent);
                display: flex; align-items: center; gap: .75rem; padding: .5rem .75rem; margin-bottom: .4rem;
                background: var(--bv-surface); color: var(--bv-text);
                border: 1px solid var(--bv-border); border-radius: 10px; box-shadow: var(--bv-shadow);
            }
            .ov-item.sortable-chosen { box-shadow: var(--bv-shadow-lg); border-color: var(--bv-accent-border); }
            .ov-item.sortable-ghost { opacity: .35; background: var(--bv-accent-soft); }
            .ov-item.is-movido { animation: ov-destello .9s ease; }
            @keyframes ov-destello { from { background: var(--bv-accent-soft); } to { background: var(--bv-surface); } }
            .ov-asa { cursor: grab; color: var(--bv-text-muted); padding: .25rem; touch-action: none; }
            .ov-asa:active { cursor: grabbing; }
            .ov-num { flex: 0 0 2rem; text-align: center; font-weight: 700; font-size: .85rem; color: var(--bv-accent); }
            .ov-mini { flex: 0 0 72px; height: 40px; border-radius: 6px; overflow: hidden; background: #1a1612;
                display: flex; align-items: center; justify-content: center; color: #fff; }
            .ov-mini img { width: 100%; height: 100%; object-fit: cover; }
            .ov-info { flex: 1; min-width: 0; display: flex; flex-direction: column; }
            .ov-nombre { font-weight: 600; font-size: .88rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
            .ov-meta { font-size: .72rem; color: var(--bv-text-muted); }
            .ov-acciones { flex: 0 0 auto; display: flex; gap: .2rem; }
            .ov-guardar {
                position: sticky; bottom: 0; z-index: 5; max-width: 60rem;
                display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .5rem;
                padding: .75rem 1rem; margin-bottom: 1rem; border-radius: 10px;
                background: var(--bv-surface); border: 1px solid var(--bv-border); box-shadow: var(--bv-shadow-lg);
            }
            .ov-guardar.is-pendiente { border-color: var(--bv-accent-border); }
            @media (max-width: 575.98px) {
                .ov-mini { display: none; }
                .ov-acciones [data-mover="inicio"], .ov-acciones [data-mover="final"] { display: none; }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
        <script>
            (function () {
                var lista = document.getElementById('ov-lista');
                if (!lista) return;
                var estado = document.getElementById('ov-estado');
                var barra = document.querySelector('.ov-guardar');
                var deshacer = document.getElementById('ov-deshacer');
                var original = items().map(function (li) { return li.dataset.id; });
                var cambios = false;

                function items() { return Array.prototype.slice.call(lista.children); }

                function actualizar() {
                    var actual = items().map(function (li, i) {
                        li.querySelector('.ov-num').textContent = i + 1;
                        return li.dataset.id;
                    });
                    cambios = actual.join(',') !== original.join(',');
                    estado.textContent = cambios ? 'Hay cambios sin guardar.' : 'Sin cambios.';
                    barra.classList.toggle('is-pendiente', cambios);
                    deshacer.hidden = !cambios;
                }

                function destacar(li) {
                    li.classList.remove('is-movido'); void li.offsetWidth; li.classList.add('is-movido');
                }

                if (window.Sortable) {
                    Sortable.create(lista, { handle: '.ov-asa', animation: 160, ghostClass: 'sortable-ghost', chosenClass: 'sortable-chosen', onEnd: actualizar });
                }

                lista.addEventListener('click', function (e) {
                    var btn = e.target.closest('[data-mover]');
                    if (!btn) return;
                    var li = btn.closest('.ov-item');
                    switch (btn.dataset.mover) {
                        case 'inicio': lista.insertBefore(li, lista.firstElementChild); break;
                        case 'arriba': if (li.previousElementSibling) lista.insertBefore(li, li.previousElementSibling); break;
                        case 'abajo': if (li.nextElementSibling) lista.insertBefore(li.nextElementSibling, li); break;
                        case 'final': lista.appendChild(li); break;
                    }
                    destacar(li);
                    btn.focus();
                    actualizar();
                });

                // Orden "natural": "Video 2" va antes que "Video 10".
                var natural = new Intl.Collator('es', { numeric: true, sensitivity: 'base' });
                var criterios = {
                    'nombre': function (a, b) { return natural.compare(a.dataset.nombre, b.dataset.nombre); },
                    'nombre-desc': function (a, b) { return natural.compare(b.dataset.nombre, a.dataset.nombre); },
                    'antiguos': function (a, b) { return a.dataset.fecha - b.dataset.fecha || a.dataset.id - b.dataset.id; },
                    'recientes': function (a, b) { return b.dataset.fecha - a.dataset.fecha || b.dataset.id - a.dataset.id; },
                    'vistas': function (a, b) { return b.dataset.vistas - a.dataset.vistas; },
                };
                document.querySelectorAll('[data-ordenar]').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        items().sort(criterios[btn.dataset.ordenar]).forEach(function (li) { lista.appendChild(li); });
                        actualizar();
                    });
                });

                deshacer.addEventListener('click', function () {
                    var porId = {};
                    items().forEach(function (li) { porId[li.dataset.id] = li; });
                    original.forEach(function (id) { lista.appendChild(porId[id]); });
                    actualizar();
                });

                window.addEventListener('beforeunload', function (e) {
                    if (cambios) { e.preventDefault(); e.returnValue = ''; }
                });
                document.getElementById('form-orden').addEventListener('submit', function () { cambios = false; });
            })();
        </script>
    @endpush
@endsection
