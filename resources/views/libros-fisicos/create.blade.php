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
                    <div class="form-group col-md-6">
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
                    <div class="form-group col-md-6">
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
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
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
                    <div class="form-group col-md-8">
                        <label class="bv-form-label" for="codigo">
                            Código único <span class="text-danger">*</span>
                            <small class="text-muted font-weight-normal">(identificador en estantería)</small>
                        </label>
                        <input type="text" name="codigo" id="codigo"
                            class="form-control form-control-sm @error('codigo') is-invalid @enderror"
                            value="{{ old('codigo') }}" required maxlength="50"
                            placeholder="Ej: BIO-042">
                        @error('codigo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Sección 2: categorías --}}
            <div class="bv-form-card" id="bvCardCats">
                <div class="bv-form-section-title">
                    <i class="fa fa-tags mr-1"></i>Categorías <span class="text-danger">*</span>
                    <small class="text-muted font-weight-normal ml-1">(puede elegir varias)</small>
                </div>

                <div class="bv-cat-pills">
                    @foreach ($categoriasFisicas as $cat)
                        <div class="bv-cat-pill">
                            <input type="checkbox" name="categorias_fisicas[]"
                                id="catfis_{{ $cat->id }}" value="{{ $cat->id }}"
                                {{ is_array(old('categorias_fisicas')) && in_array($cat->id, old('categorias_fisicas')) ? 'checked' : '' }}>
                            <label for="catfis_{{ $cat->id }}">{{ $cat->nombre }}</label>
                        </div>
                    @endforeach
                </div>

                @error('categorias_fisicas')
                    <div class="text-danger small mt-1">
                        <i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}
                    </div>
                @enderror
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

@push('scripts')
<script>
    (function () {
        gsap.from(['#bvCardInfo', '#bvCardCats'], {
            opacity: 0, y: 22, duration: 0.42, stagger: 0.11, ease: 'power2.out'
        });

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
