@extends('layouts.app')
@section('titulo', 'Nuevo préstamo')

@section('contenido')
    @include('partials.biblioteca-fisica-crud-header', ['active' => 'prestamos'])
    @include('partials.admin-breadcrumb', [
        'items' => [
            ['label' => 'Panel',          'url' => route('home')],
            ['label' => 'Préstamos',      'url' => route('biblioteca-fisica.prestamos.index')],
            ['label' => 'Nuevo registro', 'url' => null],
        ],
    ])

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="bv-crud-page-title mb-0">
            <i class="fa fa-plus-circle text-info mr-2"></i>Registrar préstamo
        </h1>
        <a href="{{ route('biblioteca-fisica.prestamos.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fa fa-arrow-left mr-1"></i>Volver
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger bv-alert mb-3">
            <i class="fa fa-exclamation-circle mr-2"></i><strong>Corrige los siguientes errores:</strong>
            <ul class="mb-0 mt-2 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (!$hayLibros)
        <div class="alert alert-warning bv-alert">
            <i class="fa fa-exclamation-triangle mr-2"></i>
            Primero debe
            <a href="{{ route('biblioteca-fisica.libros.create') }}" class="alert-link">registrar un libro físico</a>
            o un <a href="{{ route('biblioteca-fisica.materiales.create') }}" class="alert-link">material didáctico</a>
            antes de crear préstamos.
        </div>
    @else
        <form action="{{ route('biblioteca-fisica.prestamos.store') }}" method="POST" id="prestamoForm">
            @csrf

            {{-- 1. SELECTOR DEL TIPO DE MATERIAL (PASO INICIAL) --}}
            <div class="bv-tipo-selector-wrap mb-3" id="bvCardTipoSelector">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="bv-tipo-selector-title mb-0">
                        <i class="fa fa-check-circle text-info mr-1"></i>¿Qué tipo de material deseas prestar?
                    </div>
                    <span class="small text-muted font-italic d-none d-sm-inline">Paso 1: Selecciona una opción</span>
                </div>

                <div class="bv-tipo-selector-grid">
                    <label class="bv-tipo-card is-active" id="optTipoLibro">
                        <input type="radio" name="tipo_material_prestamo" value="libro" checked>
                        <span class="bv-tipo-icon">
                            <i class="fa fa-book"></i>
                        </span>
                        <span class="bv-tipo-text">
                            <span class="bv-tipo-name">Libro Físico</span>
                            <span class="bv-tipo-sub">Catálogo general de libros</span>
                        </span>
                    </label>

                    <label class="bv-tipo-card" id="optTipoMaterial">
                        <input type="radio" name="tipo_material_prestamo" value="material">
                        <span class="bv-tipo-icon">
                            <i class="fa fa-cubes"></i>
                        </span>
                        <span class="bv-tipo-text">
                            <span class="bv-tipo-name">Material Didáctico</span>
                            <span class="bv-tipo-sub">Juegos, maquetas y recursos</span>
                        </span>
                    </label>

                    <label class="bv-tipo-card bv-tipo-card--sm" id="optTipoAmbos">
                        <input type="radio" name="tipo_material_prestamo" value="ambos">
                        <span class="bv-tipo-icon">
                            <i class="fa fa-layer-group"></i>
                        </span>
                        <span class="bv-tipo-text">
                            <span class="bv-tipo-name">Ambos</span>
                            <span class="bv-tipo-sub">Libro + Material</span>
                        </span>
                    </label>
                </div>
            </div>

            {{-- DISTRIBUCIÓN COMPACTA EN 2 COLUMNAS --}}
            <div class="row">
                {{-- COLUMNA IZQUIERDA: BÚSQUEDA Y SELECCIÓN DE ITEMS --}}
                <div class="col-lg-7 mb-3">
                    {{-- Tarjeta de Búsqueda de Libros --}}
                    <div class="bv-form-card bv-form-card--compact mb-3" id="bvCardLibro">
                        <div class="bv-form-section-title d-flex justify-content-between align-items-center">
                            <span><i class="fa fa-book mr-1"></i>Libros a prestar</span>
                            <span class="badge badge-light border small font-weight-normal" id="badgeLibrosCount">0 seleccionados</span>
                        </div>

                        {{-- Libros ya elegidos --}}
                        <div id="librosSeleccionadosWrap" class="mb-2 d-none">
                            <div id="librosSeleccionadosList"></div>
                        </div>

                        <div class="form-group mb-0">
                            <label class="bv-form-label small" for="libroBuscar">
                                Buscar libro por título, autor o código:
                            </label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-transparent text-muted"><i class="fa fa-search"></i></span>
                                </div>
                                <input type="text" id="libroBuscar"
                                    class="form-control form-control-sm @error('libro_fisico_id') is-invalid @enderror"
                                    placeholder="Escribe título, autor o código… (mínimo 2 letras)" autocomplete="off">
                            </div>

                            <div class="bv-libro-results" id="libroResultadosWrap" style="display:none;">
                                <table class="table table-sm table-hover mb-0">
                                    <tbody id="libroResultadosBody"></tbody>
                                </table>
                                <div class="text-center py-2 small text-muted" id="libroResultadosEstado"></div>
                            </div>

                            <div class="invalid-feedback d-block d-none" id="libroSeleccionError">
                                Debes seleccionar al menos un libro o material didáctico.
                            </div>
                            @error('libro_fisico_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Tarjeta de Búsqueda de Material Didáctico --}}
                    <div class="bv-form-card bv-form-card--compact mb-3 d-none" id="bvCardMaterial">
                        <div class="bv-form-section-title d-flex justify-content-between align-items-center">
                            <span><i class="fa fa-cubes mr-1"></i>Material didáctico a prestar</span>
                            <span class="badge badge-light border small font-weight-normal" id="badgeMaterialesCount">0 seleccionados</span>
                        </div>

                        <div id="materialesSeleccionadosWrap" class="mb-2 d-none">
                            <div id="materialesSeleccionadosList"></div>
                        </div>

                        <div class="form-group mb-0">
                            <label class="bv-form-label small" for="materialBuscar">
                                Buscar material por nombre o código:
                            </label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-transparent text-muted"><i class="fa fa-search"></i></span>
                                </div>
                                <input type="text" id="materialBuscar"
                                    class="form-control form-control-sm @error('material_didactico_id') is-invalid @enderror"
                                    placeholder="Escribe nombre o código… (mínimo 2 letras)" autocomplete="off">
                            </div>

                            <div class="bv-libro-results" id="materialResultadosWrap" style="display:none;">
                                <table class="table table-sm table-hover mb-0">
                                    <tbody id="materialResultadosBody"></tbody>
                                </table>
                                <div class="text-center py-2 small text-muted" id="materialResultadosEstado"></div>
                            </div>

                            @error('material_didactico_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @foreach ($errors->getMessages() as $campo => $mensajes)
                                @if (str_starts_with($campo, 'cantidad_material.'))
                                    <div class="invalid-feedback d-block">{{ $mensajes[0] }}</div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- COLUMNA DERECHA: PRESTATARIO, FECHAS Y ACCIONES --}}
                <div class="col-lg-5 mb-3">
                    <div class="bv-form-card bv-form-card--compact" id="bvCardDatosPrestamo">
                        <div class="bv-form-section-title">
                            <i class="fa fa-user-check mr-1"></i>Datos del prestatario y plazo
                        </div>

                        {{-- DNI y Nombre en una fila ágil --}}
                        <div class="form-row">
                            <div class="form-group col-sm-5 mb-2">
                                <label class="bv-form-label small" for="dni_prestatario">
                                    DNI / código <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="dni_prestatario" id="dni_prestatario"
                                    class="form-control form-control-sm @error('dni_prestatario') is-invalid @enderror"
                                    value="{{ old('dni_prestatario') }}" required maxlength="20"
                                    placeholder="Ej: 70458123">
                                @error('dni_prestatario')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-sm-7 mb-2">
                                <label class="bv-form-label small" for="nombre_prestatario">
                                    Nombre completo <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nombre_prestatario" id="nombre_prestatario"
                                    class="form-control form-control-sm @error('nombre_prestatario') is-invalid @enderror"
                                    value="{{ old('nombre_prestatario') }}" required maxlength="255"
                                    placeholder="Ej: Ana Quispe Mamani">
                                @error('nombre_prestatario')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Contacto --}}
                        <div class="form-group mb-2">
                            <label class="bv-form-label small" for="contacto_prestatario">
                                Contacto <small class="text-muted font-weight-normal">(opcional)</small>
                            </label>
                            <input type="text" name="contacto_prestatario" id="contacto_prestatario"
                                class="form-control form-control-sm @error('contacto_prestatario') is-invalid @enderror"
                                value="{{ old('contacto_prestatario') }}" maxlength="255"
                                placeholder="Teléfono o email">
                            @error('contacto_prestatario')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-2 border-secondary" style="opacity: 0.15;">

                        {{-- Fechas compactas con botones rápidos de plazo --}}
                        <div class="form-row align-items-end mb-1">
                            <div class="form-group col-6 mb-2">
                                <label class="bv-form-label small" for="fecha_prestamo">
                                    F. Préstamo <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="fecha_prestamo" id="fecha_prestamo"
                                    class="form-control form-control-sm @error('fecha_prestamo') is-invalid @enderror"
                                    value="{{ old('fecha_prestamo', now()->toDateString()) }}" required>
                                @error('fecha_prestamo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-6 mb-2">
                                <label class="bv-form-label small" for="fecha_limite">
                                    F. Límite <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="fecha_limite" id="fecha_limite"
                                    class="form-control form-control-sm @error('fecha_limite') is-invalid @enderror"
                                    value="{{ old('fecha_limite', now()->addDays(7)->toDateString()) }}" required>
                                @error('fecha_limite')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Botones de plazo rápido --}}
                        <div class="d-flex align-items-center mb-3">
                            <span class="small text-muted mr-2 font-weight-500">Plazo rápido:</span>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Plazos rápidos">
                                <button type="button" class="btn btn-outline-secondary bv-btn-plazo py-0 px-2 small" data-dias="3">+3 días</button>
                                <button type="button" class="btn btn-outline-secondary bv-btn-plazo py-0 px-2 small active" data-dias="7">+7 días</button>
                                <button type="button" class="btn btn-outline-secondary bv-btn-plazo py-0 px-2 small" data-dias="15">+15 días</button>
                                <button type="button" class="btn btn-outline-secondary bv-btn-plazo py-0 px-2 small" data-dias="30">+30 días</button>
                            </div>
                        </div>

                        {{-- Observaciones compactas --}}
                        <div class="form-group mb-3">
                            <label class="bv-form-label small" for="observaciones">
                                Observaciones <small class="text-muted font-weight-normal">(opcional)</small>
                            </label>
                            <input type="text" name="observaciones" id="observaciones"
                                class="form-control form-control-sm @error('observaciones') is-invalid @enderror"
                                maxlength="2000"
                                placeholder="Notas opcionales…" value="{{ old('observaciones') }}">
                            @error('observaciones')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Acciones de Envío integradas en la tarjeta --}}
                        <div class="pt-2 border-top">
                            <button type="submit" class="btn btn-info btn-block py-2">
                                <i class="fa fa-save mr-1"></i><strong>Registrar préstamo</strong>
                            </button>
                            <div class="text-center mt-2">
                                <a href="{{ route('biblioteca-fisica.prestamos.index') }}" class="small text-muted text-decoration-none">
                                    <i class="fa fa-times mr-1"></i>Cancelar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endif
@endsection

@push('styles')
<style>
    /* Estilos del Selector Inicial de Tipo de Material */
    .bv-tipo-selector-wrap {
        background: var(--bv-surface);
        border: 1px solid var(--bv-border);
        border-radius: 10px;
        padding: 0.85rem 1.15rem;
        box-shadow: var(--bv-shadow);
    }
    .bv-tipo-selector-title {
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: var(--bv-accent);
    }
    .bv-tipo-selector-grid {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: 0.65rem;
    }
    @media (max-width: 576px) {
        .bv-tipo-selector-grid {
            grid-template-columns: 1fr;
        }
    }
    .bv-tipo-card {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.65rem 0.95rem;
        border: 1.5px solid var(--bv-border-strong);
        border-radius: 8px;
        background: var(--bv-surface-2);
        cursor: pointer;
        transition: all 0.16s ease-in-out;
        user-select: none;
        margin-bottom: 0;
    }
    .bv-tipo-card input[type="radio"] {
        display: none;
    }
    .bv-tipo-card:hover {
        border-color: var(--bv-accent);
        background: var(--bv-accent-soft);
        transform: translateY(-1px);
    }
    .bv-tipo-card.is-active {
        border-color: var(--bv-accent);
        background: var(--bv-accent-soft);
        box-shadow: 0 0 0 1px var(--bv-accent);
    }
    .bv-tipo-icon {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        background: var(--bv-surface);
        color: var(--bv-accent);
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        flex-shrink: 0;
        transition: all 0.16s ease;
    }
    .bv-tipo-card.is-active .bv-tipo-icon {
        background: var(--bv-accent);
        color: #fff;
    }
    .bv-tipo-text {
        line-height: 1.25;
    }
    .bv-tipo-name {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--bv-text);
        display: block;
    }
    .bv-tipo-sub {
        font-size: 0.72rem;
        color: var(--bv-text-muted);
    }

    /* Tarjetas de formulario más compactas */
    .bv-form-card--compact {
        padding: 1.1rem 1.25rem;
        margin-bottom: 1rem;
    }
    .bv-form-card--compact .bv-form-section-title {
        margin-bottom: 0.8rem;
        padding-bottom: 0.45rem;
        font-size: 0.74rem;
    }
    .bv-form-card--compact .form-group {
        margin-bottom: 0.75rem;
    }

    /* Botones rápidos de plazo */
    .bv-btn-plazo {
        font-size: 0.73rem !important;
        font-weight: 500;
        border-color: var(--bv-border-strong);
        color: var(--bv-text-muted);
        background: var(--bv-surface-2);
        transition: all 0.15s ease;
    }
    .bv-btn-plazo:hover,
    .bv-btn-plazo.active {
        background-color: var(--bv-accent) !important;
        border-color: var(--bv-accent-hover) !important;
        color: #fff !important;
    }
</style>
@endpush

@push('scripts')
<script>
    window.bvPageAnimated = true;
    if (typeof gsap !== 'undefined') {
        gsap.from(['#bvCardTipoSelector', '#bvCardLibro', '#bvCardMaterial', '#bvCardDatosPrestamo'], {
            opacity: 0, y: 16, duration: 0.35, stagger: 0.08, ease: 'power2.out'
        });
    }

    // Repuebla la selección múltiple con los libros ya elegidos por el servidor
    window.bvLibrosPreseleccionados = @json($librosSeleccionados);
    window.bvMaterialesPreseleccionados = @json($materialesSeleccionados);

    // ==========================================
    // 1. CONTROLADOR DEL SELECTOR DE TIPO (PASO 1)
    // ==========================================
    (function () {
        var cardLibro = document.getElementById('bvCardLibro');
        var cardMaterial = document.getElementById('bvCardMaterial');
        var radios = document.querySelectorAll('input[name="tipo_material_prestamo"]');
        var cards = document.querySelectorAll('.bv-tipo-card');

        function actualizarTipo(tipo, autoFocus) {
            cards.forEach(function (c) {
                var r = c.querySelector('input[type="radio"]');
                c.classList.toggle('is-active', r.value === tipo);
            });

            if (tipo === 'libro') {
                cardLibro.classList.remove('d-none');
                cardMaterial.classList.add('d-none');
                if (autoFocus) document.getElementById('libroBuscar').focus();
            } else if (tipo === 'material') {
                cardLibro.classList.add('d-none');
                cardMaterial.classList.remove('d-none');
                if (autoFocus) document.getElementById('materialBuscar').focus();
            } else if (tipo === 'ambos') {
                cardLibro.classList.remove('d-none');
                cardMaterial.classList.remove('d-none');
                if (autoFocus) document.getElementById('libroBuscar').focus();
            }
        }

        radios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                actualizarTipo(this.value, true);
            });
        });

        // Detectar si ya vienen preseleccionados materiales o libros tras un error de validación
        var tieneLibros = (window.bvLibrosPreseleccionados && window.bvLibrosPreseleccionados.length > 0);
        var tieneMateriales = (window.bvMaterialesPreseleccionados && window.bvMaterialesPreseleccionados.length > 0);

        if (tieneLibros && tieneMateriales) {
            var r = document.querySelector('input[name="tipo_material_prestamo"][value="ambos"]');
            if (r) { r.checked = true; actualizarTipo('ambos', false); }
        } else if (tieneMateriales) {
            var r = document.querySelector('input[name="tipo_material_prestamo"][value="material"]');
            if (r) { r.checked = true; actualizarTipo('material', false); }
        } else {
            actualizarTipo('libro', false);
        }
    })();

    // ==========================================
    // 2. BOTONES RÁPIDOS DE PLAZO (+3, +7, +15, +30 DÍAS)
    // ==========================================
    (function () {
        var fPrestamo = document.getElementById('fecha_prestamo');
        var fLimite = document.getElementById('fecha_limite');
        var botonesPlazo = document.querySelectorAll('.bv-btn-plazo');

        function calcularFechaLimite(dias) {
            var base = fPrestamo.value ? new Date(fPrestamo.value + 'T00:00:00') : new Date();
            base.setDate(base.getDate() + dias);
            var y = base.getFullYear();
            var m = String(base.getMonth() + 1).padStart(2, '0');
            var d = String(base.getDate()).padStart(2, '0');
            fLimite.value = y + '-' + m + '-' + d;
        }

        botonesPlazo.forEach(function (btn) {
            btn.addEventListener('click', function () {
                botonesPlazo.forEach(function (b) { b.classList.remove('active'); });
                this.classList.add('active');
                var dias = parseInt(this.dataset.dias, 10);
                calcularFechaLimite(dias);
            });
        });

        fPrestamo.addEventListener('change', function () {
            var activo = document.querySelector('.bv-btn-plazo.active');
            if (activo) {
                calcularFechaLimite(parseInt(activo.dataset.dias, 10));
            }
        });
    })();

    // ==========================================
    // 3. BUSCADOR Y SELECCIÓN DE LIBROS
    // ==========================================
    (function () {
        var seleccionados     = new Map(); // id -> datos del libro
        var searchInput       = document.getElementById('libroBuscar');
        var resultsWrap        = document.getElementById('libroResultadosWrap');
        var resultsBody        = document.getElementById('libroResultadosBody');
        var resultsEstado      = document.getElementById('libroResultadosEstado');
        var libroError          = document.getElementById('libroSeleccionError');
        var seleccionadosWrap   = document.getElementById('librosSeleccionadosWrap');
        var seleccionadosList   = document.getElementById('librosSeleccionadosList');
        var badgeCount          = document.getElementById('badgeLibrosCount');
        var prestamoForm        = document.getElementById('prestamoForm');
        var debounceTimer       = null;

        function badge(nombre, color) {
            var span = document.createElement('span');
            span.className = 'bv-s2-result-badge';
            span.style.background = color;
            span.textContent = nombre;
            return span;
        }

        function construirDetalle(libro) {
            var wrap = document.createElement('div');
            wrap.className = 'bv-s2-result';

            var title = document.createElement('div');
            title.className = 'bv-s2-result-title';
            title.textContent = libro.nombre + ' — ' + libro.autor;
            wrap.appendChild(title);

            var meta = document.createElement('div');
            meta.className = 'bv-s2-result-meta';
            if (libro.codigo) {
                var code = document.createElement('code');
                code.textContent = libro.codigo;
                meta.appendChild(code);
            }
            if (libro.categoria) meta.appendChild(badge(libro.categoria.nombre, libro.categoria.color));
            if (libro.tipo_material) meta.appendChild(badge(libro.tipo_material.nombre, libro.tipo_material.color));

            var dispSpan = document.createElement('span');
            dispSpan.className = 'bv-s2-result-disp ' + (libro.ejemplares_disponibles > 0 ? 'bv-s2-result-disp--ok' : 'bv-s2-result-disp--no');
            dispSpan.textContent = libro.ejemplares_disponibles + ' disp.';
            meta.appendChild(dispSpan);

            wrap.appendChild(meta);
            return wrap;
        }

        function construirSelectorEjemplar(libro) {
            var etiquetas = libro.etiquetas_ejemplares_disponibles || [];
            if (libro.numero_ejemplar_actual && etiquetas.indexOf(libro.numero_ejemplar_actual) === -1) {
                etiquetas = etiquetas.concat([libro.numero_ejemplar_actual]);
            }

            var wrap = document.createElement('div');
            wrap.className = 'form-group mt-1 mb-0';

            var label = document.createElement('label');
            label.className = 'bv-form-label small mb-1';
            label.textContent = 'Ejemplar a prestar:';
            wrap.appendChild(label);

            var select = document.createElement('select');
            select.className = 'form-control form-control-sm';
            select.name = 'numero_ejemplar[' + libro.id + ']';

            var optionVacia = document.createElement('option');
            optionVacia.value = '';
            optionVacia.textContent = etiquetas.length ? 'Selecciona…' : 'Sin ejemplares disponibles';
            select.appendChild(optionVacia);

            etiquetas.forEach(function (etiqueta) {
                var opt = document.createElement('option');
                opt.value = etiqueta;
                opt.textContent = 'Ejemplar ' + etiqueta;
                if (libro.numero_ejemplar_actual === etiqueta) opt.selected = true;
                select.appendChild(opt);
            });

            select.required = true;
            select.disabled = etiquetas.length === 0;
            wrap.appendChild(select);

            return wrap;
        }

        function renderChip(libro) {
            var chip = document.createElement('div');
            chip.className = 'bv-libro-chip mb-2';
            chip.dataset.id = libro.id;
            chip.appendChild(construirDetalle(libro));

            if (libro.cantidad_ejemplares > 1) {
                chip.appendChild(construirSelectorEjemplar(libro));
            }

            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-outline-danger btn-quitar-libro';
            btn.title = 'Quitar';
            btn.innerHTML = '<i class="fa fa-times"></i>';
            chip.appendChild(btn);

            var hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'libro_fisico_id[]';
            hidden.value = libro.id;
            chip.appendChild(hidden);

            return chip;
        }

        function actualizarListaSeleccionados() {
            seleccionadosList.innerHTML = '';
            seleccionados.forEach(function (libro) {
                seleccionadosList.appendChild(renderChip(libro));
            });
            if (badgeCount) badgeCount.textContent = seleccionados.size + (seleccionados.size === 1 ? ' libro' : ' libros');
            seleccionadosWrap.classList.toggle('d-none', seleccionados.size === 0);
        }

        function agregarLibro(libro) {
            if (seleccionados.has(libro.id)) return;
            seleccionados.set(libro.id, libro);
            actualizarListaSeleccionados();
            libroError.classList.add('d-none');
            searchInput.value = '';
            resultsWrap.style.display = 'none';
            searchInput.focus();
        }

        seleccionadosList.addEventListener('click', function (e) {
            var btn = e.target.closest('.btn-quitar-libro');
            if (!btn) return;
            var chip = btn.closest('.bv-libro-chip');
            seleccionados.delete(parseInt(chip.dataset.id, 10));
            actualizarListaSeleccionados();
        });

        function renderResultados(data) {
            resultsBody.innerHTML = '';
            var libros = data.results || [];

            if (!libros.length) {
                resultsEstado.textContent = 'Sin resultados.';
                return;
            }

            libros.forEach(function (libro) {
                var yaElegido = seleccionados.has(libro.id);
                var tr = document.createElement('tr');
                tr.className = 'bv-libro-row' + ((libro.disabled || yaElegido) ? ' bv-libro-row--disabled' : '');

                var tdLibro = document.createElement('td');
                tdLibro.appendChild(construirDetalle(libro));

                var tdAccion = document.createElement('td');
                tdAccion.className = 'text-right align-middle text-nowrap';
                if (yaElegido) {
                    var yaSpan = document.createElement('span');
                    yaSpan.className = 'small text-muted';
                    yaSpan.innerHTML = '<i class="fa fa-check mr-1"></i>Agregado';
                    tdAccion.appendChild(yaSpan);
                } else {
                    var dispSpan = document.createElement('span');
                    dispSpan.className = 'bv-s2-result-disp ' + (libro.ejemplares_disponibles > 0 ? 'bv-s2-result-disp--ok' : 'bv-s2-result-disp--no');
                    dispSpan.textContent = libro.ejemplares_disponibles + ' disp.';
                    tdAccion.appendChild(dispSpan);
                }

                tr.appendChild(tdLibro);
                tr.appendChild(tdAccion);

                if (!libro.disabled && !yaElegido) {
                    tr.addEventListener('click', function () { agregarLibro(libro); });
                }

                resultsBody.appendChild(tr);
            });

            resultsEstado.textContent = (data.pagination && data.pagination.more)
                ? 'Mostrando los primeros resultados — afina tu búsqueda.'
                : '';
        }

        function buscar(term) {
            if (term.length < 2) {
                resultsWrap.style.display = 'none';
                return;
            }
            resultsWrap.style.display = '';
            resultsBody.innerHTML = '';
            resultsEstado.textContent = 'Buscando…';

            var url = '{{ route("biblioteca-fisica.libros.buscar-ajax") }}'
                + '?q=' + encodeURIComponent(term) + '&page=1';
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (r) { return r.json(); })
                .then(renderResultados)
                .catch(function () { resultsEstado.textContent = 'Ocurrió un error al buscar.'; });
        }

        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            var term = searchInput.value.trim();
            debounceTimer = setTimeout(function () { buscar(term); }, 300);
        });

        document.addEventListener('click', function (e) {
            if (!searchInput.contains(e.target) && !resultsWrap.contains(e.target)) {
                resultsWrap.style.display = 'none';
            }
        });

        prestamoForm.addEventListener('submit', function (e) {
            if (seleccionados.size === 0 && !(window.bvMaterialesSeleccionadosCount && window.bvMaterialesSeleccionadosCount())) {
                e.preventDefault();
                libroError.classList.remove('d-none');
                searchInput.focus();
            }
        });

        (window.bvLibrosPreseleccionados || []).forEach(function (libro) {
            seleccionados.set(libro.id, libro);
        });
        actualizarListaSeleccionados();
    })();

    // ==========================================
    // 4. BUSCADOR Y SELECCIÓN DE MATERIAL DIDÁCTICO
    // ==========================================
    (function () {
        var seleccionados = new Map(); // id -> datos del material
        var searchInput   = document.getElementById('materialBuscar');
        var resultsWrap   = document.getElementById('materialResultadosWrap');
        var resultsBody   = document.getElementById('materialResultadosBody');
        var resultsEstado = document.getElementById('materialResultadosEstado');
        var listWrap      = document.getElementById('materialesSeleccionadosWrap');
        var list          = document.getElementById('materialesSeleccionadosList');
        var badgeCount    = document.getElementById('badgeMaterialesCount');
        var libroError    = document.getElementById('libroSeleccionError');
        var debounceTimer = null;

        window.bvMaterialesSeleccionadosCount = function () { return seleccionados.size; };

        function construirDetalle(material) {
            var wrap = document.createElement('div');
            wrap.className = 'bv-s2-result';

            var title = document.createElement('div');
            title.className = 'bv-s2-result-title';
            title.textContent = material.nombre;
            wrap.appendChild(title);

            var meta = document.createElement('div');
            meta.className = 'bv-s2-result-meta';
            var code = document.createElement('code');
            code.textContent = material.codigo;
            meta.appendChild(code);
            if (material.categoria) {
                var cat = document.createElement('span');
                cat.className = 'bv-s2-result-badge';
                cat.style.background = '#0891b2';
                cat.textContent = material.categoria;
                meta.appendChild(cat);
            }
            var disp = document.createElement('span');
            disp.className = 'bv-s2-result-disp ' + (material.unidades_disponibles > 0 ? 'bv-s2-result-disp--ok' : 'bv-s2-result-disp--no');
            disp.textContent = material.unidades_disponibles + ' de ' + material.cantidad + ' disp.';
            meta.appendChild(disp);
            wrap.appendChild(meta);

            return wrap;
        }

        function renderChip(material) {
            var chip = document.createElement('div');
            chip.className = 'bv-libro-chip mb-2';
            chip.dataset.id = material.id;
            chip.appendChild(construirDetalle(material));

            var group = document.createElement('div');
            group.className = 'form-group mt-1 mb-0';
            group.style.maxWidth = '160px';
            var label = document.createElement('label');
            label.className = 'bv-form-label small mb-1';
            label.textContent = 'Unidades:';
            group.appendChild(label);
            var input = document.createElement('input');
            input.type = 'number';
            input.min = 1;
            input.max = Math.max(1, material.unidades_disponibles);
            input.step = 1;
            input.required = true;
            input.className = 'form-control form-control-sm';
            input.name = 'cantidad_material[' + material.id + ']';
            input.value = material.cantidad_actual || 1;
            input.addEventListener('input', function () { material.cantidad_actual = input.value; });
            group.appendChild(input);
            chip.appendChild(group);

            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-outline-danger btn-quitar-material';
            btn.title = 'Quitar';
            btn.innerHTML = '<i class="fa fa-times"></i>';
            chip.appendChild(btn);

            var hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'material_didactico_id[]';
            hidden.value = material.id;
            chip.appendChild(hidden);

            return chip;
        }

        function actualizarLista() {
            list.innerHTML = '';
            seleccionados.forEach(function (material) { list.appendChild(renderChip(material)); });
            if (badgeCount) badgeCount.textContent = seleccionados.size + (seleccionados.size === 1 ? ' material' : ' materiales');
            listWrap.classList.toggle('d-none', seleccionados.size === 0);
        }

        function agregar(material) {
            if (seleccionados.has(material.id)) return;
            seleccionados.set(material.id, material);
            actualizarLista();
            libroError.classList.add('d-none');
            searchInput.value = '';
            resultsWrap.style.display = 'none';
            searchInput.focus();
        }

        list.addEventListener('click', function (e) {
            var btn = e.target.closest('.btn-quitar-material');
            if (!btn) return;
            seleccionados.delete(parseInt(btn.closest('.bv-libro-chip').dataset.id, 10));
            actualizarLista();
        });

        function renderResultados(data) {
            resultsBody.innerHTML = '';
            var materiales = data.results || [];
            if (!materiales.length) {
                resultsEstado.textContent = 'Sin resultados.';
                return;
            }
            resultsEstado.textContent = '';

            materiales.forEach(function (material) {
                var yaElegido = seleccionados.has(material.id);
                var tr = document.createElement('tr');
                tr.className = 'bv-libro-row' + ((material.disabled || yaElegido) ? ' bv-libro-row--disabled' : '');

                var td = document.createElement('td');
                td.appendChild(construirDetalle(material));
                tr.appendChild(td);

                var tdAccion = document.createElement('td');
                tdAccion.className = 'text-right align-middle text-nowrap small text-muted';
                if (yaElegido) tdAccion.innerHTML = '<i class="fa fa-check mr-1"></i>Agregado';
                else if (material.disabled) tdAccion.textContent = 'Sin unidades';
                tr.appendChild(tdAccion);

                if (!material.disabled && !yaElegido) {
                    tr.addEventListener('click', function () { agregar(material); });
                }
                resultsBody.appendChild(tr);
            });

            if (data.pagination && data.pagination.more) {
                resultsEstado.textContent = 'Mostrando los primeros resultados — afina tu búsqueda.';
            }
        }

        function buscar(term) {
            if (term.length < 2) {
                resultsWrap.style.display = 'none';
                return;
            }
            resultsWrap.style.display = '';
            resultsBody.innerHTML = '';
            resultsEstado.textContent = 'Buscando…';

            var url = '{{ route("biblioteca-fisica.materiales.buscar-ajax") }}'
                + '?q=' + encodeURIComponent(term) + '&page=1';
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (r) { return r.json(); })
                .then(renderResultados)
                .catch(function () { resultsEstado.textContent = 'Ocurrió un error al buscar.'; });
        }

        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            var term = searchInput.value.trim();
            debounceTimer = setTimeout(function () { buscar(term); }, 300);
        });

        document.addEventListener('click', function (e) {
            if (!searchInput.contains(e.target) && !resultsWrap.contains(e.target)) {
                resultsWrap.style.display = 'none';
            }
        });

        (window.bvMaterialesPreseleccionados || []).forEach(function (material) {
            seleccionados.set(material.id, material);
        });
        actualizarLista();
    })();
</script>
@endpush
