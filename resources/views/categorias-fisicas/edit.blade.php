@extends('layouts.app')
@section('titulo', 'Editar categoría física')
@section('contenido')
    @include('partials.biblioteca-fisica-crud-header', ['active' => 'categorias'])
    @include('partials.admin-breadcrumb', [
        'items' => [
            ['label' => 'Panel',              'url' => route('home')],
            ['label' => 'Categorías físicas', 'url' => route('biblioteca-fisica.categorias.index')],
            ['label' => 'Editar',             'url' => null],
        ],
    ])

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="bv-crud-page-title mb-0">
            <i class="fa fa-edit text-warning mr-2"></i>Editar categoría física
        </h1>
        <a href="{{ route('biblioteca-fisica.categorias.index') }}" class="btn btn-sm btn-outline-secondary">
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

    <form action="{{ route('biblioteca-fisica.categorias.update', $categoria) }}" method="POST" id="catForm">
        @csrf
        @method('PUT')

        <div class="bv-form-card" id="bvCardCat" style="max-width:680px;">
            <div class="bv-form-section-title">
                <i class="fa fa-tag mr-1"></i>Datos de la categoría
            </div>

            <div class="form-row">
                <div class="form-group col-md-8">
                    <label class="bv-form-label" for="nombre">
                        Nombre <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="nombre" id="nombre"
                        class="form-control form-control-sm @error('nombre') is-invalid @enderror"
                        value="{{ old('nombre', $categoria->nombre) }}" required maxlength="120"
                        placeholder="Ej: Literatura, Ciencias, Historia…" autofocus>
                    <div class="bv-char-counter" id="nombreCounter">0 / 120</div>
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label class="bv-form-label" for="codigo">
                        Código <small class="text-muted font-weight-normal">(opcional)</small>
                    </label>
                    <input type="text" name="codigo" id="codigo"
                        class="form-control form-control-sm @error('codigo') is-invalid @enderror"
                        value="{{ old('codigo', $categoria->codigo) }}" maxlength="50"
                        placeholder="Ej: LIT-01">
                    @error('codigo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group mb-0">
                <label class="bv-form-label" for="descripcion">
                    Descripción <small class="text-muted font-weight-normal">(opcional)</small>
                </label>
                <textarea name="descripcion" id="descripcion" rows="4"
                    class="form-control form-control-sm @error('descripcion') is-invalid @enderror"
                    maxlength="500"
                    placeholder="Describe brevemente qué tipo de libros abarca esta categoría…">{{ old('descripcion', $categoria->descripcion) }}</textarea>
                <div class="bv-char-counter" id="descCounter">0 / 500</div>
                @error('descripcion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="bv-form-actions">
            <button type="submit" class="btn btn-info btn-sm px-4">
                <i class="fa fa-save mr-1"></i>Actualizar categoría
            </button>
            <a href="{{ route('biblioteca-fisica.categorias.index') }}" class="btn btn-outline-secondary btn-sm">
                Cancelar
            </a>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    (function () {
        window.bvPageAnimated = true;
        gsap.from('#bvCardCat', { opacity: 0, y: 22, duration: 0.4, ease: 'power2.out' });

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

        attachCounter('nombre',      'nombreCounter', 120);
        attachCounter('descripcion', 'descCounter',   500);
    })();
</script>
@endpush
