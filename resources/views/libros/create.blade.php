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
    <div class="row">
        <div class="col-md-12">
            <h3 class="text-info">Crear Nuevo Libro
                <a href="{{ route('libros.index') }}" class="btn btn-danger btn-sm float-right">Volver</a>
            </h3>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        <div style="border-bottom: 1px dashed rgb(74, 113, 138); width: 100%;" class="mt-2"></div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <form action="{{ route('libros.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-12 mt-3">
                        <label for="nombre">Nombre:</label>
                        <input type="text" name="nombre" id="nombre"
                            class="form-control form-control-sm rounded-pill" value="{{ old('nombre') }}" required>
                    </div>
                    <div class="col-lg-6 mt-3">
                        <label for="autor">Autor:</label>
                        <input type="text" name="autor" id="autor"
                            class="form-control form-control-sm rounded-pill" value="{{ old('autor') }}" required>
                    </div>
                    <div class="col-lg-6 mt-3 mb-2">
                        <label for="identificador">Identificador:</label>
                        <input type="text" name="identificador" id="identificador"
                            class="form-control form-control-sm rounded-pill" value="{{ old('identificador') }}" required>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-lg-12">
                        <h5>Secciones <small class="text-primary">>></small> Categorías:</h5>
                    </div>
                    @foreach ($secciones as $seccion)
                        <div class="col-lg-4 mb-2">
                                <strong>{{ $seccion->nombre }}</strong>
                                @foreach ($seccion->categorias as $categoria)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="categorias[]"
                                            id="categoria_{{ $categoria->id }}" value="{{ $categoria->id }}"
                                            {{ (is_array(old('categorias')) && in_array($categoria->id, old('categorias'))) || (isset($categoriaSeleccionada) && in_array($categoria->id, $categoriaSeleccionada)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="categoria_{{ $categoria->id }}">
                                            {{ $categoria->nombre }}
                                        </label>
                                    </div>
                                @endforeach
                        </div>
                    @endforeach
                </div>
                <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
            </form>
        </div>
    </div>
@endsection
