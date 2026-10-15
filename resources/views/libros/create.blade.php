@extends('layouts.app')
@section('titulo', 'Crear nuevo Libro')
@section('contenido')
    @include('partials.biblioteca-virtual-crud-header', ['active' => 'libros'])
    @include('partials.admin-breadcrumb', [
        'items' => [
            ['label' => 'Panel', 'url' => route('home')],
            ['label' => 'Libros', 'url' => route('libros.index')],
            ['label' => 'Nuevo libro', 'url' => null],
        ],
    ])

    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h1 class="bv-crud-page-title mb-0">Nuevo libro</h1>
            <p class="bv-crud-meta">Registra un texto y vincúlalo a sus categorías.</p>
        </div>
        <a href="{{ route('libros.index') }}" class="btn btn-outline-secondary btn-sm mt-2"><i class="fa fa-arrow-left"></i>
            Volver</a>
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

    <form action="{{ route('libros.store') }}" method="POST">
        @csrf

        <div class="bv-form-card">
            <p class="bv-form-section-title">Datos del libro</p>
            <div class="form-row">
                <div class="form-group col-lg-12">
                    <label for="nombre" class="bv-form-label">Nombre</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre') }}"
                        required>
                </div>
                <div class="form-group col-lg-6">
                    <label for="autor" class="bv-form-label">Autor</label>
                    <input type="text" name="autor" id="autor" class="form-control" value="{{ old('autor') }}"
                        required>
                </div>
                <div class="form-group col-lg-6">
                    <label for="identificador" class="bv-form-label">Identificador (ID de Drive)</label>
                    <div class="input-group">
                        <input type="text" name="identificador" id="identificador" class="form-control"
                            value="{{ old('identificador') }}" required>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary"
                                onclick="abrirVistaPreviaLibro(document.getElementById('identificador').value, document.getElementById('nombre').value)">
                                <i class="fa fa-eye"></i> Vista previa
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bv-form-card">
            <p class="bv-form-section-title">Secciones y categorías</p>
            @foreach ($secciones as $seccion)
                <p class="bv-form-label mb-2">{{ $seccion->nombre }}</p>
                <div class="bv-cat-pills">
                    @foreach ($seccion->categorias as $categoria)
                        <div class="bv-cat-pill">
                            <input type="checkbox" name="categorias[]" id="categoria_{{ $categoria->id }}"
                                value="{{ $categoria->id }}"
                                {{ (is_array(old('categorias')) && in_array($categoria->id, old('categorias'))) || (isset($categoriaSeleccionada) && in_array($categoria->id, $categoriaSeleccionada)) ? 'checked' : '' }}>
                            <label for="categoria_{{ $categoria->id }}">{{ $categoria->nombre }}</label>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <div class="bv-form-actions">
            <button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Guardar</button>
            <a href="{{ route('libros.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
@endsection
