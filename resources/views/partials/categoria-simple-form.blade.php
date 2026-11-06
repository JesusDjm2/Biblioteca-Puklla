{{--
    Formulario de categoría simple (nombre + URL amigable) para Videos y Canciones.
    Parámetros: $header (vista de cabecera), $modulo (texto, ej. "Videos"), $indexRoute, $action,
    $categoria (modelo o null), $titulo, $ayuda.
--}}
@php $editando = $categoria && $categoria->exists; @endphp
@include($header, ['active' => 'categorias'])
@include('partials.admin-breadcrumb', [
    'items' => [
        ['label' => 'Panel', 'url' => route('home')],
        ['label' => $modulo, 'url' => $indexRoute],
        ['label' => $editando ? 'Editar categoría' : 'Nueva categoría', 'url' => null],
    ],
])

<div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
    <div>
        <h1 class="bv-crud-page-title mb-0">{{ $titulo }}</h1>
        <p class="bv-crud-meta">{{ $ayuda }}</p>
    </div>
    <a href="{{ $indexRoute }}" class="btn btn-outline-secondary btn-sm mt-2"><i class="fa fa-arrow-left"></i> Volver</a>
</div>

@include('material-didactico.partials.errores')

<form action="{{ $action }}" method="POST">
    @csrf
    @if ($editando) @method('PUT') @endif

    <div class="bv-form-card mb-4" style="max-width: 46rem;">
        <div class="form-group">
            <label for="nombre" class="bv-form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" name="nombre" id="nombre" maxlength="255" required autofocus
                class="form-control @error('nombre') is-invalid @enderror"
                value="{{ old('nombre', $categoria?->nombre) }}" placeholder="Ej: Fiestas y celebraciones">
            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="form-group mb-0">
            <label for="url" class="bv-form-label">URL amigable</label>
            <input type="text" name="url" id="url" maxlength="255" required readonly
                class="form-control @error('url') is-invalid @enderror"
                value="{{ old('url', $categoria?->url) }}">
            <small class="form-text text-muted">
                Se genera a partir del nombre.@if ($editando) Cambiarla cambia el enlace público de la categoría.@endif
            </small>
            @error('url')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="d-flex justify-content-end mb-4" style="max-width: 46rem;">
        <a href="{{ $indexRoute }}" class="btn btn-secondary mr-2">Cancelar</a>
        <button type="submit" class="btn btn-primary"><i class="fa fa-save mr-1"></i> {{ $editando ? 'Guardar cambios' : 'Crear categoría' }}</button>
    </div>
</form>

<script>
    (function () {
        var nombre = document.getElementById('nombre');
        var url = document.getElementById('url');
        nombre.addEventListener('input', function () {
            url.value = nombre.value.normalize('NFD').replace(/[̀-ͯ]/g, '')
                .toLowerCase().replace(/&/g, 'y').replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
        });
    })();
</script>
