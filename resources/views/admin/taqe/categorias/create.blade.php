@extends('layouts.app')
@section('titulo', "Crear Categoría — Taq'e")
@section('contenido')
    @include('partials.taqe-crud-header', ['active' => 'categorias'])
    @include('partials.admin-breadcrumb', [
        'items' => [
            ['label' => 'Panel', 'url' => route('home')],
            ['label' => "Taq'e Categorías", 'url' => route('admin.taqe.categorias.index')],
            ['label' => 'Nueva categoría', 'url' => null],
        ],
    ])

    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h1 class="bv-crud-page-title mb-0">Nueva categoría de investigación</h1>
            <p class="bv-crud-meta">Crea una categoría para clasificar los trabajos de los alumnos.</p>
        </div>
        <a href="{{ route('admin.taqe.categorias.index') }}" class="btn btn-outline-secondary btn-sm mt-2">
            <i class="fa fa-arrow-left"></i> Volver
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger bv-alert">
            <ul class="mb-0 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.taqe.categorias.store') }}" method="POST">
        @csrf

        <div class="bv-form-card mb-4">
            <p class="bv-form-section-title"><i class="fa fa-folder-open"></i> Datos de la categoría</p>
            <div class="form-row">
                <div class="form-group col-lg-8">
                    <label for="nombre" class="bv-form-label">Nombre de la categoría <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre') }}" required placeholder="Ej: Educación Intercultural Bilingüe (EIB)">
                </div>
                <div class="form-group col-lg-4">
                    <label for="orden" class="bv-form-label">Orden de aparición</label>
                    <input type="number" name="orden" id="orden" class="form-control" value="{{ old('orden', $categoria->orden) }}">
                </div>
                <div class="form-group col-lg-6">
                    <label for="color" class="bv-form-label">Color distintivo</label>
                    <div class="input-group">
                        <input type="color" name="color" id="color" class="form-control form-control-color" style="max-width: 60px; height: 38px; padding: 2px;" value="{{ old('color', '#c78e41') }}">
                        <input type="text" class="form-control" id="color_hex" value="{{ old('color', '#c78e41') }}" readonly>
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <label for="icono" class="bv-form-label">Ícono FontAwesome</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i id="icon-preview" class="fa fa-folder"></i></span>
                        </div>
                        <input type="text" name="icono" id="icono" class="form-control" value="{{ old('icono', 'fa-folder') }}" placeholder="fa-book, fa-comments, fa-music…">
                    </div>
                    <small class="form-text text-muted">Nombre de la clase FontAwesome (ej. fa-graduation-cap, fa-paint-brush).</small>
                </div>
                <div class="form-group col-12">
                    <label for="descripcion" class="bv-form-label">Descripción</label>
                    <textarea name="descripcion" id="descripcion" rows="3" class="form-control" placeholder="Breve resumen del propósito de esta categoría temática…">{{ old('descripcion') }}</textarea>
                </div>
                <div class="form-group col-12">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="activo" name="activo" value="1" @checked(old('activo', true))>
                        <label class="custom-control-label" for="activo">Categoría activa y visible en los filtros del catálogo</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('admin.taqe.categorias.index') }}" class="btn btn-secondary mr-2">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fa fa-save mr-1"></i> Guardar categoría</button>
        </div>
    </form>

    <script>
        document.getElementById('color')?.addEventListener('input', function(e) {
            document.getElementById('color_hex').value = e.target.value;
        });
        document.getElementById('icono')?.addEventListener('input', function(e) {
            var icon = e.target.value.trim();
            if (!icon.startsWith('fa-') && !icon.startsWith('fa ')) {
                icon = 'fa-' + icon;
            }
            document.getElementById('icon-preview').className = 'fa ' + icon;
        });
    </script>
@endsection
