@extends('layouts.app')
@section('titulo', 'Nuevo libro físico')
@section('contenido')
    @include('partials.biblioteca-fisica-crud-header', ['active' => 'libros'])
    @include('partials.admin-breadcrumb', [
        'items' => [
            ['label' => 'Panel',          'url' => route('home')],
            ['label' => 'Libros físicos', 'url' => route('biblioteca-fisica.libros.index')],
            ['label' => 'Nuevo registro', 'url' => null],
        ],
    ])

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="bv-crud-page-title mb-0">
            <i class="fa fa-plus-circle text-info mr-2"></i>Registrar libro físico
        </h1>
        <a href="{{ route('biblioteca-fisica.libros.index') }}" class="btn btn-sm btn-outline-secondary">
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

    @if ($categoriasFisicas->isEmpty())
        <div class="alert alert-warning bv-alert">
            <i class="fa fa-exclamation-triangle mr-2"></i>
            Primero debe crear al menos una
            <a href="{{ route('biblioteca-fisica.categorias.create') }}" class="alert-link">categoría física</a>
            antes de registrar libros.
        </div>
    @else
        <form action="{{ route('biblioteca-fisica.libros.store') }}" method="POST" id="libroForm">
            @csrf

            {{-- Sección 1: datos del libro --}}
            <div class="bv-form-card" id="bvCardInfo">
                <div class="bv-form-section-title">
                    <i class="fa fa-info-circle mr-1"></i>Información del libro
                </div>

                <div class="form-row">
                    <div class="form-group col-md-5">
                        <label class="bv-form-label" for="nombre">
                            Nombre del libro <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nombre" id="nombre"
                            class="form-control form-control-sm @error('nombre') is-invalid @enderror"
                            value="{{ old('nombre') }}" required maxlength="200"
                            placeholder="Ej: El Quijote de la Mancha" autofocus>
                        <div class="bv-char-counter" id="nombreCounter">0 / 200</div>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label class="bv-form-label" for="autor">
                            Autor <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="autor" id="autor"
                            class="form-control form-control-sm @error('autor') is-invalid @enderror"
                            value="{{ old('autor') }}" required maxlength="150"
                            placeholder="Ej: Miguel de Cervantes">
                        <div class="bv-char-counter" id="autorCounter">0 / 150</div>
                        @error('autor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-3">
                        <label class="bv-form-label" for="editorial">
                            Editorial
                        </label>
                        <input type="text" name="editorial" id="editorial"
                            class="form-control form-control-sm @error('editorial') is-invalid @enderror"
                            value="{{ old('editorial') }}" maxlength="255"
                            placeholder="Ej: Editorial Planeta">
                        @error('editorial')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-2">
                        <label class="bv-form-label" for="numero_paginas">
                            N.° de páginas
                        </label>
                        <input type="number" name="numero_paginas" id="numero_paginas"
                            class="form-control form-control-sm @error('numero_paginas') is-invalid @enderror"
                            min="1" max="65535"
                            value="{{ old('numero_paginas') }}"
                            placeholder="Ej: 320">
                        @error('numero_paginas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-3">
                        <label class="bv-form-label" for="cantidad_ejemplares">
                            Cantidad de ejemplares <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="cantidad_ejemplares" id="cantidad_ejemplares"
                            class="form-control form-control-sm @error('cantidad_ejemplares') is-invalid @enderror"
                            min="1" max="9999" required
                            value="{{ old('cantidad_ejemplares', 1) }}">
                        @error('cantidad_ejemplares')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-2">
                        <label class="bv-form-label" for="anio_publicacion">
                            Año de publicación <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="anio_publicacion" id="anio_publicacion"
                            class="form-control form-control-sm @error('anio_publicacion') is-invalid @enderror"
                            min="1000" max="{{ date('Y') + 1 }}"
                            value="{{ old('anio_publicacion') }}" required
                            placeholder="{{ date('Y') }}">
                        @error('anio_publicacion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-5">
                        <label class="bv-form-label" for="codigo">
                            Código único <span class="text-danger">*</span>
                            <small class="text-muted font-weight-normal">(identificador en estantería)</small>
                        </label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="codigo" id="codigo"
                                class="form-control @error('codigo') is-invalid @enderror"
                                value="{{ old('codigo') }}" required maxlength="50"
                                placeholder="Selecciona categoría y haz clic en Sugerir">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-info" id="btnSugerirCodigo"
                                    title="Generar código basado en la categoría seleccionada (Dewey)">
                                    <i class="fa fa-magic"></i> Sugerir
                                </button>
                            </div>
                            @error('codigo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="text-muted">
                            Selecciona primero la categoría del libro y haz clic en
                            <em>Sugerir</em> — se generará el código Dewey automáticamente.
                        </small>
                    </div>
                </div>

                <div class="form-group mb-0 mt-2 d-none" id="etiquetasEjemplaresWrap">
                    <label class="bv-form-label mb-1">
                        Etiquetas de cada ejemplar
                        <small class="text-muted font-weight-normal">(opcional — por defecto 1, 2, 3…; puedes usar A, B, Sala, etc.)</small>
                    </label>
                    <div class="bv-etiquetas-ejemplares-list" id="etiquetasEjemplaresList"></div>
                    @error('etiquetas_ejemplares')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Sección 2: categoría y tipo de material --}}
            <div class="bv-form-card" id="bvCardCats">
                <div class="bv-form-section-title">
                    <i class="fa fa-tags mr-1"></i>Clasificación
                </div>

                <div class="form-row">
                    <div class="form-group col-md-5">
                        <label class="bv-form-label" for="categoria_fisica_id">
                            Categoría Dewey
                            <small class="text-muted font-weight-normal">(materia — opcional)</small>
                        </label>
                        <select name="categoria_fisica_id" id="categoria_fisica_id"
                            class="form-control form-control-sm @error('categoria_fisica_id') is-invalid @enderror">
                            <option value="">Sin clasificación Dewey…</option>
                            @foreach ($categoriasFisicas as $cat)
                                <option value="{{ $cat->id }}" {{ (int) old('categoria_fisica_id') === $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('categoria_fisica_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label class="bv-form-label" for="tipo_material">
                            Tipo de material <span class="text-danger">*</span>
                        </label>
                        <select name="tipo_material" id="tipo_material"
                            class="form-control form-control-sm @error('tipo_material') is-invalid @enderror">
                            <option value="">Selecciona…</option>
                            @foreach (\App\Models\CategoriaFisica::TIPOS_MATERIAL as $codigoTipo => $nombreTipo)
                                <option value="{{ $codigoTipo }}" {{ old('tipo_material') === $codigoTipo ? 'selected' : '' }}>
                                    {{ $nombreTipo }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo_material')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-3">
                        <label class="bv-form-label" for="ubicacion">
                            Ubicación <span class="text-danger">*</span>
                        </label>
                        <select name="ubicacion" id="ubicacion"
                            class="form-control form-control-sm @error('ubicacion') is-invalid @enderror">
                            <option value="">Selecciona…</option>
                            @foreach (\App\Models\LibroFisico::UBICACIONES as $codigoUbicacion => $nombreUbicacion)
                                <option value="{{ $codigoUbicacion }}" {{ old('ubicacion') === $codigoUbicacion ? 'selected' : '' }}>
                                    {{ $nombreUbicacion }}
                                </option>
                            @endforeach
                        </select>
                        @error('ubicacion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="bv-form-actions">
                <button type="submit" class="btn btn-info btn-sm px-4">
                    <i class="fa fa-save mr-1"></i>Guardar libro
                </button>
                <a href="{{ route('biblioteca-fisica.libros.index') }}" class="btn btn-outline-secondary btn-sm">
                    Cancelar
                </a>
            </div>
        </form>
    @endif
@endsection

@push('styles')
<style>
    .bv-etiquetas-ejemplares-list { display: flex; flex-wrap: wrap; gap: .5rem; }
    .bv-etiquetas-ejemplares-list .input-group { width: 140px; }
    .bv-etiquetas-ejemplares-list .input-group-text { padding: .25rem .5rem; }
</style>
@endpush

@push('scripts')
<script>
    (function () {
        window.bvPageAnimated = true;
        gsap.from(['#bvCardInfo', '#bvCardCats'], {
            opacity: 0, y: 22, duration: 0.42, stagger: 0.11, ease: 'power2.out'
        });

        // Muestra un input de texto por ejemplar para poder nombrarlo (1, 2… o A, B, "Sala"…)
        // en vez de solo un correlativo fijo; se regenera al cambiar la cantidad sin perder lo ya escrito.
        (function () {
            var cantidadInput = document.getElementById('cantidad_ejemplares');
            var wrap = document.getElementById('etiquetasEjemplaresWrap');
            var list = document.getElementById('etiquetasEjemplaresList');
            var valoresIniciales = @json(old('etiquetas_ejemplares', []));

            function valorActualEn(index) {
                var existente = list.children[index];
                if (existente) return existente.querySelector('input').value;
                return valoresIniciales[index] || '';
            }

            function render() {
                var cantidad = parseInt(cantidadInput.value, 10) || 0;
                if (cantidad <= 1) {
                    wrap.classList.add('d-none');
                    list.innerHTML = '';
                    return;
                }
                wrap.classList.remove('d-none');

                var valores = [];
                for (var i = 0; i < cantidad; i++) valores.push(valorActualEn(i));

                list.innerHTML = '';
                valores.forEach(function (valor, i) {
                    var item = document.createElement('div');
                    item.className = 'input-group input-group-sm';

                    var prependWrap = document.createElement('div');
                    prependWrap.className = 'input-group-prepend';
                    var prepend = document.createElement('span');
                    prepend.className = 'input-group-text';
                    prepend.textContent = (i + 1) + '.';
                    prependWrap.appendChild(prepend);
                    item.appendChild(prependWrap);

                    var input = document.createElement('input');
                    input.type = 'text';
                    input.className = 'form-control';
                    input.name = 'etiquetas_ejemplares[]';
                    input.maxLength = 50;
                    input.value = valor;
                    input.placeholder = String(i + 1);
                    item.appendChild(input);

                    list.appendChild(item);
                });
            }

            cantidadInput.addEventListener('input', render);
            render();
        })();

        function attachCounter(inputId, counterId, max) {
            var el = document.getElementById(inputId);
            var counter = document.getElementById(counterId);
            if (!el || !counter) return;
            function update() {
                var len = el.value.length;
                counter.textContent = len + ' / ' + max;
                counter.className = 'bv-char-counter' +
                    (len >= max ? ' at-limit' : len >= max * 0.85 ? ' near-limit' : '');
            }
            el.addEventListener('input', update);
            update();
        }

        attachCounter('nombre', 'nombreCounter', 200);
        attachCounter('autor',  'autorCounter',  150);

        // Botón sugerir código basado en el tipo de material y la categoría Dewey
        var btnSugerir = document.getElementById('btnSugerirCodigo');
        var selectCategoria = document.getElementById('categoria_fisica_id');
        var selectTipoMaterial = document.getElementById('tipo_material');
        if (btnSugerir && selectCategoria && selectTipoMaterial) {
            btnSugerir.addEventListener('click', function () {
                var params = new URLSearchParams();
                if (selectTipoMaterial.value) params.set('tipo_material', selectTipoMaterial.value);
                if (selectCategoria.value) params.set('categoria_fisica_id', selectCategoria.value);

                var url = '{{ route('libros-fisicos.sugerir-codigo') }}?' + params.toString();

                btnSugerir.disabled = true;
                btnSugerir.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        document.getElementById('codigo').value = data.codigo;
                        document.getElementById('codigo').focus();
                        btnSugerir.disabled = false;
                        btnSugerir.innerHTML = '<i class="fa fa-magic"></i> Sugerir';

                        // Resaltar brevemente el campo para indicar que se llenó
                        var inp = document.getElementById('codigo');
                        inp.style.transition = 'background .3s';
                        inp.style.background = '#d1fae5';
                        setTimeout(function () { inp.style.background = ''; }, 900);
                    })
                    .catch(function () {
                        btnSugerir.disabled = false;
                        btnSugerir.innerHTML = '<i class="fa fa-magic"></i> Sugerir';
                    });
            });

            // Actualizar tooltip del botón cuando cambia el tipo de material o la categoría
            function actualizarTooltip() {
                if (selectTipoMaterial.value && selectTipoMaterial.value !== 'BIB-GEN') {
                    btnSugerir.title = 'Generar código basado en el tipo de material (' + selectTipoMaterial.value + ')';
                } else if (selectCategoria.value) {
                    btnSugerir.title = 'Generar código basado en la categoría Dewey seleccionada';
                } else {
                    btnSugerir.title = 'Generar código genérico (PF-XXXX)';
                }
            }
            selectCategoria.addEventListener('change', actualizarTooltip);
            selectTipoMaterial.addEventListener('change', actualizarTooltip);
            actualizarTooltip();
        }
    })();
</script>
@endpush
