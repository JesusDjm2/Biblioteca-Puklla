@extends('layouts.app')
@section('titulo', 'Editar préstamo')
@section('contenido')
    @include('partials.biblioteca-fisica-crud-header', ['active' => 'prestamos'])
    @include('partials.admin-breadcrumb', [
        'items' => [
            ['label' => 'Panel',     'url' => route('home')],
            ['label' => 'Préstamos', 'url' => route('biblioteca-fisica.prestamos.index')],
            ['label' => 'Editar',    'url' => null],
        ],
    ])

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="bv-crud-page-title mb-0">
            <i class="fa fa-edit text-warning mr-2"></i>Editar préstamo
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

    <form action="{{ route('biblioteca-fisica.prestamos.update', $prestamo) }}" method="POST" id="prestamoForm">
        @csrf
        @method('PUT')

        <div class="bv-form-card" id="bvCardLibro">
            <div class="bv-form-section-title">
                <i class="fa {{ $prestamo->es_material ? 'fa-cubes' : 'fa-book' }} mr-1"></i>{{ $prestamo->es_material ? 'Material didáctico' : 'Libro' }}
            </div>
            <p class="mb-0">
                <span class="font-weight-bold">{{ $prestamo->item_nombre }}</span>
                <code class="small text-muted ml-1">{{ $prestamo->item_codigo }}</code>
                <span class="badge {{ $prestamo->estado_badge_class }} ml-2">{{ $prestamo->estado_label }}</span>
            </p>
            <small class="text-muted">
                {{ $prestamo->es_material ? 'El material' : 'El libro' }} de un préstamo no se puede cambiar; elimina este registro y crea uno nuevo si te equivocaste.
            </small>

            @if ($prestamo->es_material)
                <div class="form-group mt-2 mb-0" style="max-width: 220px;">
                    <label class="bv-form-label" for="cantidad">
                        Unidades prestadas <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="cantidad" id="cantidad" min="1" step="1" required
                        @if ($prestamo->fecha_devolucion === null) max="{{ $maxUnidadesMaterial }}" @endif
                        class="form-control form-control-sm @error('cantidad') is-invalid @enderror"
                        value="{{ old('cantidad', $prestamo->cantidad) }}">
                    @if ($prestamo->fecha_devolucion === null)
                        <small class="text-muted">Máximo {{ $maxUnidadesMaterial }} según disponibilidad.</small>
                    @endif
                    @error('cantidad')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @elseif ($prestamo->libroFisico->cantidad_ejemplares > 1)
                <div class="form-group mt-2 mb-0" style="max-width: 220px;">
                    <label class="bv-form-label" for="numero_ejemplar">
                        N.º de ejemplar prestado <span class="text-danger">*</span>
                    </label>
                    <select name="numero_ejemplar" id="numero_ejemplar"
                        class="form-control form-control-sm @error('numero_ejemplar') is-invalid @enderror" required>
                        <option value="">Selecciona…</option>
                        @foreach ($numerosEjemplarDisponibles as $numero)
                            <option value="{{ $numero }}" @selected(old('numero_ejemplar', $prestamo->numero_ejemplar) === $numero)>
                                Ejemplar {{ $numero }}
                            </option>
                        @endforeach
                    </select>
                    @error('numero_ejemplar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @endif
        </div>

        <div class="bv-form-card" id="bvCardPrestatario">
            <div class="bv-form-section-title">
                <i class="fa fa-user mr-1"></i>Datos del prestatario
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label class="bv-form-label" for="nombre_prestatario">
                        Nombre completo <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="nombre_prestatario" id="nombre_prestatario"
                        class="form-control form-control-sm @error('nombre_prestatario') is-invalid @enderror"
                        value="{{ old('nombre_prestatario', $prestamo->nombre_prestatario) }}" required maxlength="255"
                        autofocus>
                    @error('nombre_prestatario')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="bv-form-label" for="dni_prestatario">
                        DNI / código <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="dni_prestatario" id="dni_prestatario"
                        class="form-control form-control-sm @error('dni_prestatario') is-invalid @enderror"
                        value="{{ old('dni_prestatario', $prestamo->dni_prestatario) }}" required maxlength="20">
                    @error('dni_prestatario')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="bv-form-label" for="contacto_prestatario">
                        Contacto <small class="text-muted font-weight-normal">(opcional)</small>
                    </label>
                    <input type="text" name="contacto_prestatario" id="contacto_prestatario"
                        class="form-control form-control-sm @error('contacto_prestatario') is-invalid @enderror"
                        value="{{ old('contacto_prestatario', $prestamo->contacto_prestatario) }}" maxlength="255">
                    @error('contacto_prestatario')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="bv-form-card" id="bvCardFechas">
            <div class="bv-form-section-title">
                <i class="fa fa-calendar mr-1"></i>Fechas
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label class="bv-form-label" for="fecha_prestamo">
                        Fecha de préstamo <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="fecha_prestamo" id="fecha_prestamo"
                        class="form-control form-control-sm @error('fecha_prestamo') is-invalid @enderror"
                        value="{{ old('fecha_prestamo', $prestamo->fecha_prestamo->toDateString()) }}" required>
                    @error('fecha_prestamo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label class="bv-form-label" for="fecha_limite">
                        Fecha límite de devolución <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="fecha_limite" id="fecha_limite"
                        class="form-control form-control-sm @error('fecha_limite') is-invalid @enderror"
                        value="{{ old('fecha_limite', $prestamo->fecha_limite->toDateString()) }}" required>
                    @error('fecha_limite')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            @if ($prestamo->fecha_devolucion)
                <p class="small text-muted mb-2">
                    <i class="fa fa-check-circle mr-1"></i>
                    Devuelto el {{ $prestamo->fecha_devolucion->format('d/m/Y') }}.
                </p>
            @endif

            <div class="form-group mb-0">
                <label class="bv-form-label" for="observaciones">
                    Observaciones <small class="text-muted font-weight-normal">(opcional)</small>
                </label>
                <textarea name="observaciones" id="observaciones" rows="3"
                    class="form-control form-control-sm @error('observaciones') is-invalid @enderror"
                    maxlength="2000">{{ old('observaciones', $prestamo->observaciones) }}</textarea>
                @error('observaciones')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="bv-form-actions">
            <button type="submit" class="btn btn-info btn-sm px-4">
                <i class="fa fa-save mr-1"></i>Actualizar préstamo
            </button>
            <a href="{{ route('biblioteca-fisica.prestamos.index') }}" class="btn btn-outline-secondary btn-sm">
                Cancelar
            </a>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    (function () {
        window.bvPageAnimated = true;
        gsap.from(['#bvCardLibro', '#bvCardPrestatario', '#bvCardFechas'], {
            opacity: 0, y: 22, duration: 0.42, stagger: 0.1, ease: 'power2.out'
        });
    })();
</script>
@endpush
