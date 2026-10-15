@extends('layouts.app')
@section('titulo', 'Editar categoría de material didáctico')
@section('contenido')
    @include('partials.biblioteca-fisica-crud-header', ['active' => 'material-categorias'])
    @include('partials.admin-breadcrumb', [
        'items' => [
            ['label' => 'Panel',              'url' => route('home')],
            ['label' => 'Material didáctico', 'url' => route('biblioteca-fisica.materiales.index')],
            ['label' => 'Categorías',         'url' => route('biblioteca-fisica.material-categorias.index')],
            ['label' => 'Editar',             'url' => null],
        ],
    ])

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="bv-crud-page-title mb-0">
            <i class="fa fa-edit text-warning mr-2"></i>Editar categoría de material didáctico
        </h1>
        <a href="{{ route('biblioteca-fisica.materiales.index') }}#categorias" class="btn btn-sm btn-outline-secondary">
            <i class="fa fa-arrow-left mr-1"></i>Volver
        </a>
    </div>

    @include('material-didactico.partials.errores')

    <form action="{{ route('biblioteca-fisica.material-categorias.update', $categoria) }}" method="POST">
        @csrf
        @method('PUT')

        @include('material-didactico.categorias._form')

        <div class="bv-form-actions">
            <button type="submit" class="btn btn-info btn-sm px-4">
                <i class="fa fa-save mr-1"></i>Actualizar categoría
            </button>
            <a href="{{ route('biblioteca-fisica.materiales.index') }}#categorias" class="btn btn-outline-secondary btn-sm">
                Cancelar
            </a>
        </div>
    </form>
@endsection
