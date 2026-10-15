@extends('layouts.app')
@section('titulo', 'Editar libro físico')
@section('contenido')
    @include('partials.biblioteca-fisica-crud-header', ['active' => 'libros'])
    @include('partials.admin-breadcrumb', [
        'items' => [
            ['label' => 'Panel',          'url' => route('home')],
            ['label' => 'Libros físicos', 'url' => route('biblioteca-fisica.libros.index')],
            ['label' => 'Editar',         'url' => null],
        ],
    ])

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="bv-crud-page-title mb-0">
            <i class="fa fa-edit text-warning mr-2"></i>Editar libro físico
        </h1>
        <a href="{{ route('biblioteca-fisica.libros.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fa fa-arrow-left mr-1"></i>Volver
        </a>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show bv-alert" role="alert">
            <i class="fa fa-exclamation-circle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

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
            No hay categorías.
            <a href="{{ route('biblioteca-fisica.categorias.create') }}" class="alert-link">Crear categoría</a>
        </div>
    @else
        <form action="{{ route('biblioteca-fisica.libros.update', $libro) }}" method="POST" id="libroForm">
            @csrf
            @method('PUT')

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
                            value="{{ old('nombre', $libro->nombre) }}" required maxlength="200"
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
                            value="{{ old('autor', $libro->autor) }}" required maxlength="150"
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
                            value="{{ old('editorial', $libro->editorial) }}" maxlength="255"
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
                            value="{{ old('numero_paginas', $libro->numero_paginas) }}"
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
                            value="{{ old('cantidad_ejemplares', $libro->cantidad_ejemplares) }}">
                        @error('cantidad_ejemplares')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if ($prestadosActivos > 0)
                            <small class="text-muted d-block mt-1">
                                <i class="fa fa-info-circle mr-1"></i>Actualmente hay {{ $prestadosActivos }}
                                {{ $prestadosActivos === 1 ? 'ejemplar prestado' : 'ejemplares prestados' }}.
                            </small>
                        @endif
                    </div>
                    <div class="form-group col-md-2">
                        <label class="bv-form-label" for="anio_publicacion">
                            Año de publicación <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="anio_publicacion" id="anio_publicacion"
                            class="form-control form-control-sm @error('anio_publicacion') is-invalid @enderror"
                            min="1000" max="{{ date('Y') + 1 }}"
                            value="{{ old('anio_publicacion', $libro->anio_publicacion) }}" required>
                        @error('anio_publicacion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-5">
                        <label class="bv-form-label" for="codigo">
                            Código único <span class="text-danger">*</span>
                            <small class="text-muted font-weight-normal">(identificador en estantería)</small>
                        </label>
                        <input type="text" name="codigo" id="codigo"
                            class="form-control form-control-sm @error('codigo') is-invalid @enderror"
                            value="{{ old('codigo', $libro->codigo) }}" required maxlength="50">
                        @error('codigo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group mb-0 mt-2 d-none" id="etiquetasEjemplaresWrap">
                    <label class="bv-form-label mb-1">
                        Etiquetas de cada ejemplar
                        <small class="text-muted font-weight-normal">(opcional — por defecto 1, 2, 3…; puedes usar A, B, Sala, etc.)</small>
                    </label>
                    <div class="bv-etiquetas-ejemplares-list" id="etiquetasEjemplaresList"></div>
                    @if (count($etiquetasEnPrestamo))
                        <small class="text-muted d-block mt-1">
                            <i class="fa fa-lock mr-1"></i>Las etiquetas con candado están prestadas ahora mismo; registra la devolución para poder renombrarlas.
                        </small>
                    @endif
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
                                <option value="{{ $cat->id }}" {{ (int) old('categoria_fisica_id', $libro->categoria_fisica_id) === $cat->id ? 'selected' : '' }}>
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
                                <option value="{{ $codigoTipo }}" {{ old('tipo_material', $libro->tipo_material) === $codigoTipo ? 'selected' : '' }}>
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
                                <option value="{{ $codigoUbicacion }}" {{ old('ubicacion', $libro->ubicacion) === $codigoUbicacion ? 'selected' : '' }}>
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
                    <i class="fa fa-save mr-1"></i>Actualizar libro
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
    .bv-etiquetas-ejemplares-list .input-group { width: 160px; }
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
        // Los ejemplares actualmente prestados quedan de solo lectura (ver guardia en el controlador).
        (function () {
            var cantidadInput = document.getElementById('cantidad_ejemplares');
            var wrap = document.getElementById('etiquetasEjemplaresWrap');
            var list = document.getElementById('etiquetasEjemplaresList');
            var valoresIniciales = @json(old('etiquetas_ejemplares', $libro->etiquetas_ejemplares_lista));
            var etiquetasBloqueadas = @json($etiquetasEnPrestamo);

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
                    var valorFinal = valor || String(i + 1);
                    var bloqueado = etiquetasBloqueadas.indexOf(valorFinal) !== -1;

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
                    input.value = valorFinal;
                    input.placeholder = String(i + 1);
                    if (bloqueado) {
                        input.readOnly = true;
                        input.classList.add('bg-light');
                        input.title = 'Este ejemplar está prestado; registra la devolución para poder renombrarlo.';
                    }
                    item.appendChild(input);

                    if (bloqueado) {
                        var appendWrap = document.createElement('div');
                        appendWrap.className = 'input-group-append';
                        var appendSpan = document.createElement('span');
                        appendSpan.className = 'input-group-text';
                        appendSpan.innerHTML = '<i class="fa fa-lock text-muted"></i>';
                        appendWrap.appendChild(appendSpan);
                        item.appendChild(appendWrap);
                    }

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
    })();
</script>
@endpush
