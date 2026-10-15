@extends('layouts.app')
@section('titulo', 'Préstamos')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<style>
    .select2-container .select2-selection--single { height: calc(1.5em + .5rem + 2px); }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 1.9; padding-left: .5rem; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: calc(1.5em + .5rem + 2px); }
</style>
@endpush

@section('contenido')
    @include('partials.biblioteca-fisica-crud-header', [
        'active' => 'prestamos',
        'stats' => [
            ['valor' => $totalEjemplares, 'singular' => 'ejemplar/unidad', 'plural' => 'ejemplares/unidades', 'icono' => 'fa-layer-group'],
            ['valor' => $prestadosActualmente, 'singular' => 'prestado', 'plural' => 'prestados', 'icono' => 'fa-hand-holding'],
            ['valor' => $disponibles, 'singular' => 'disponible', 'plural' => 'disponibles', 'icono' => 'fa-check-circle'],
            ['valor' => $atrasados, 'singular' => 'atrasado', 'plural' => 'atrasados', 'icono' => 'fa-exclamation-triangle', 'tono' => 'is-danger'],
        ],
        'botones' => [['route' => route('biblioteca-fisica.prestamos.create'), 'label' => 'Registrar préstamo']],
    ])

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show bv-alert" role="alert">
            <i class="fa fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show bv-alert" role="alert">
            <i class="fa fa-exclamation-circle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <form method="get" action="{{ route('biblioteca-fisica.prestamos.index') }}" class="bv-crud-toolbar">
        <div class="form-row align-items-end">
            <div class="form-group col-md-3 mb-2 mb-md-0">
                <label for="pre-q" class="small text-muted mb-1 d-block">
                    <i class="fa fa-search mr-1"></i>Buscar
                </label>
                <input type="text" id="pre-q" name="q" class="form-control form-control-sm bv-toolbar-control"
                    value="{{ request('q') }}" placeholder="Nombre o DNI del prestatario…" autocomplete="off">
            </div>
            <div class="form-group col-md-3 mb-2 mb-md-0">
                <label for="pre-libro" class="small text-muted mb-1 d-block">
                    <i class="fa fa-book mr-1"></i>Libro
                </label>
                <select id="pre-libro" name="libro_fisico_id" class="form-control form-control-sm" style="width:100%">
                    <option value="">Todos los libros</option>
                    @if ($libroFiltroSeleccionado)
                        <option value="{{ $libroFiltroSeleccionado->id }}" selected>
                            {{ $libroFiltroSeleccionado->nombre }}
                        </option>
                    @endif
                </select>
            </div>
            @if ($materialFiltroSeleccionado)
                <input type="hidden" name="material_didactico_id" value="{{ $materialFiltroSeleccionado->id }}">
            @endif
            <div class="form-group col-md-2 mb-2 mb-md-0">
                <label for="pre-tipo" class="small text-muted mb-1 d-block">
                    <i class="fa fa-tags mr-1"></i>Tipo
                </label>
                <select id="pre-tipo" name="tipo" class="custom-select custom-select-sm bv-toolbar-control">
                    <option value="">Todos</option>
                    <option value="libro" @selected(request('tipo') === 'libro')>Libros</option>
                    <option value="material" @selected(request('tipo') === 'material')>Material didáctico</option>
                </select>
            </div>
            <div class="form-group col-md-2 mb-2 mb-md-0">
                <label for="pre-estado" class="small text-muted mb-1 d-block">
                    <i class="fa fa-flag mr-1"></i>Estado
                </label>
                <select id="pre-estado" name="estado" class="custom-select custom-select-sm bv-toolbar-control">
                    <option value="">Todos</option>
                    <option value="prestado" @selected(request('estado') === 'prestado')>Prestado</option>
                    <option value="atrasado" @selected(request('estado') === 'atrasado')>Atrasado</option>
                    <option value="devuelto" @selected(request('estado') === 'devuelto')>Devuelto</option>
                </select>
            </div>
            <div class="form-group col-md-2 mb-0">
                <label class="small mb-1 d-block invisible" aria-hidden="true">Acciones</label>
                <div class="btn-group btn-group-sm bv-toolbar-btn-group w-100">
                    <button type="submit" class="btn btn-sm btn-info">
                        <i class="fa fa-filter mr-1"></i>Filtrar
                    </button>
                    <a href="{{ route('biblioteca-fisica.prestamos.index') }}" class="btn btn-sm btn-outline-secondary">
                        Limpiar
                    </a>
                </div>
            </div>
        </div>
    </form>

    @if ($materialFiltroSeleccionado)
        <p class="small mb-2">
            <i class="fa fa-cubes text-info mr-1"></i>Préstamos del material
            <strong>{{ $materialFiltroSeleccionado->nombre }}</strong>
            <code class="small">{{ $materialFiltroSeleccionado->codigo }}</code>
            <a href="{{ route('biblioteca-fisica.prestamos.index', request()->except('material_didactico_id', 'page')) }}"
                class="ml-2 small"><i class="fa fa-times"></i> quitar filtro</a>
        </p>
    @endif

    @if ($prestamos->total() > 0)
        <p class="bv-crud-meta mb-2">
            Mostrando {{ $prestamos->firstItem() }}–{{ $prestamos->lastItem() }} de {{ $prestamos->total() }} resultado(s)
        </p>
    @endif

    <div class="bv-crud-table-wrap table-responsive">
        <table class="table table-hover table-sm">
            <thead class="thead-light">
                <tr>
                    <th style="width:3rem;">#</th>
                    <th>Libro / material</th>
                    <th>Prestatario</th>
                    <th>Contacto</th>
                    <th style="width:6.5rem;">F. préstamo</th>
                    <th style="width:6.5rem;">F. límite</th>
                    <th style="width:6rem;">Estado</th>
                    <th style="width:9rem;" class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($prestamos as $prestamo)
                    <tr class="bv-table-row">
                        <td class="text-muted small align-middle">
                            {{ $prestamos->firstItem() + $loop->index }}
                        </td>
                        <td class="align-middle">
                            <div class="font-weight-bold">
                                @if ($prestamo->es_material)
                                    <i class="fa fa-cubes text-info mr-1" title="Material didáctico"></i>
                                @else
                                    <i class="fa fa-book text-muted mr-1" title="Libro"></i>
                                @endif
                                {{ $prestamo->item_nombre }}
                            </div>
                            <code class="small text-muted">{{ $prestamo->item_codigo }}</code>
                            @if ($prestamo->es_material)
                                <span class="badge badge-info ml-1">{{ $prestamo->cantidad }} {{ $prestamo->cantidad === 1 ? 'unidad' : 'unidades' }}</span>
                            @elseif ($prestamo->numero_ejemplar)
                                <span class="badge badge-secondary ml-1">Ejemplar {{ $prestamo->numero_ejemplar }}</span>
                            @endif
                        </td>
                        <td class="align-middle">
                            <div>{{ $prestamo->nombre_prestatario }}</div>
                            <span class="small text-muted">{{ $prestamo->dni_prestatario }}</span>
                        </td>
                        <td class="align-middle small text-muted">
                            {{ $prestamo->contacto_prestatario ?: '—' }}
                        </td>
                        <td class="align-middle small">{{ $prestamo->fecha_prestamo->format('d/m/Y') }}</td>
                        <td class="align-middle small">{{ $prestamo->fecha_limite->format('d/m/Y') }}</td>
                        <td class="align-middle">
                            <span class="badge {{ $prestamo->estado_badge_class }}">{{ $prestamo->estado_label }}</span>
                        </td>
                        <td class="bv-crud-actions text-center text-nowrap align-middle">
                            @if ($prestamo->estado !== 'devuelto')
                                <form method="POST" action="{{ route('biblioteca-fisica.prestamos.devolver', $prestamo) }}"
                                    class="d-inline js-confirm-devolver">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Registrar devolución">
                                        <i class="fa fa-undo"></i>
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('biblioteca-fisica.prestamos.edit', $prestamo) }}"
                                class="btn btn-sm btn-outline-warning" title="Editar">
                                <i class="fa fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-prestamo"
                                data-url="{{ route('biblioteca-fisica.prestamos.destroy', $prestamo) }}"
                                data-nombre="{{ $prestamo->nombre_prestatario }} — {{ $prestamo->item_nombre }}"
                                title="Eliminar">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-0 border-0">
                            <div class="bv-empty-state">
                                <i class="fa fa-hand-holding bv-empty-icon"></i>
                                <p>No se encontraron préstamos con esos criterios.</p>
                                @if (request('q') || request('libro_fisico_id') || request('material_didactico_id') || request('tipo') || request('estado'))
                                    <a href="{{ route('biblioteca-fisica.prestamos.index') }}"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="fa fa-times mr-1"></i>Limpiar filtros
                                    </a>
                                @else
                                    <a href="{{ route('biblioteca-fisica.prestamos.create') }}"
                                        class="btn btn-sm btn-info">
                                        <i class="fa fa-plus mr-1"></i>Registrar el primero
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bv-crud-pagination">{{ $prestamos->links() }}</div>

    <form id="deleteFormPrestamo" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>

    <script>
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-delete-prestamo');
            if (!btn) return;
            window.bvConfirm({
                titulo: 'Eliminar préstamo',
                mensaje: '¿Eliminar el préstamo de "' + btn.dataset.nombre + '"? Esta acción no se puede deshacer. Si el préstamo está activo, el ejemplar quedará disponible sin dejar rastro de este registro.',
                tono: 'danger',
                icono: 'fa-trash',
                textoConfirmar: 'Eliminar',
            }).then(function (ok) {
                if (!ok) return;
                var f = document.getElementById('deleteFormPrestamo');
                f.action = btn.dataset.url;
                f.submit();
            });
        });

        document.querySelectorAll('.js-confirm-devolver').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                if (form.dataset.confirmed) return;
                e.preventDefault();
                window.bvConfirm({
                    titulo: 'Registrar devolución',
                    mensaje: '¿Marcar este préstamo como devuelto?',
                    icono: 'fa-undo',
                    textoConfirmar: 'Sí, devolver',
                }).then(function (ok) {
                    if (!ok) return;
                    form.dataset.confirmed = '1';
                    form.submit();
                });
            });
        });
    </script>
@endsection

@push('scripts')
<script>
    (function () {
        window.bvPageAnimated = true;
        var rows = document.querySelectorAll('.bv-table-row');
        if (rows.length) {
            gsap.from(rows, { opacity: 0, y: 12, duration: 0.32, stagger: 0.04, ease: 'power2.out' });
        }
        gsap.from('.bv-crud-toolbar', { opacity: 0, y: -8, duration: 0.3, delay: 0.05, ease: 'power2.out' });
        gsap.from('.bv-crud-nav-actions', { opacity: 0, x: -10, duration: 0.3, delay: 0.1, ease: 'power2.out' });
    })();
</script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>
<script>
    // Tarjeta compacta por resultado: título en una línea + código, categoría
    // y disponibilidad en una segunda línea, para que 50 coincidencias quepan
    // en un scroll corto y legible (también en tema oscuro).
    function formatLibroResultado(data) {
        if (data.loading || !data.id) return data.text;

        var $wrap = $('<div class="bv-s2-result"></div>');
        $('<div class="bv-s2-result-title"></div>').text(data.nombre + ' — ' + data.autor).appendTo($wrap);

        var $meta = $('<div class="bv-s2-result-meta"></div>').appendTo($wrap);
        if (data.codigo) {
            $('<code></code>').text(data.codigo).appendTo($meta);
        }
        if (data.categoria) {
            $('<span class="bv-s2-result-badge"></span>')
                .css('background', data.categoria.color)
                .text(data.categoria.nombre)
                .appendTo($meta);
        }
        if (data.tipo_material) {
            $('<span class="bv-s2-result-badge"></span>')
                .css('background', data.tipo_material.color)
                .text(data.tipo_material.nombre)
                .appendTo($meta);
        }
        var dispClass = data.ejemplares_disponibles > 0 ? 'bv-s2-result-disp--ok' : 'bv-s2-result-disp--no';
        $('<span></span>').addClass('bv-s2-result-disp ' + dispClass)
            .text(data.ejemplares_disponibles + ' disp.')
            .appendTo($meta);

        return $wrap;
    }

    function formatLibroSeleccion(data) {
        if (!data.id) return data.text;
        return data.nombre ? (data.nombre + ' — ' + data.autor) : data.text;
    }

    // El evento "load" garantiza que jQuery y select2.min.js (ambos con defer)
    // ya terminaron de ejecutarse, sin depender del orden exacto entre scripts defer.
    window.addEventListener('load', function () {
        $('#pre-libro').select2({
            width: '100%',
            language: {
                inputTooShort: function () { return 'Escribe al menos 2 letras…'; },
                searching: function () { return 'Buscando…'; },
                noResults: function () { return 'Sin resultados'; },
                loadingMore: function () { return 'Cargando más resultados…'; },
            },
            placeholder: 'Todos los libros',
            allowClear: true,
            minimumInputLength: 2,
            templateResult: formatLibroResultado,
            templateSelection: formatLibroSeleccion,
            ajax: {
                // El jQuery local de este panel es un build sin el módulo $.ajax,
                // así que se reemplaza el transporte por fetch() nativo.
                transport: function (params, success, failure) {
                    var url = '{{ route("biblioteca-fisica.libros.buscar-ajax") }}'
                        + '?q=' + encodeURIComponent(params.data.q || '')
                        + '&page=' + (params.data.page || 1);
                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(function (r) { return r.json(); })
                        .then(success)
                        .catch(failure);
                },
                delay: 300,
                data: function (params) {
                    return { q: params.term, page: params.page || 1 };
                },
                processResults: function (data) {
                    return data;
                }
            }
        });
    });
</script>
@endpush
