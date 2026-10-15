@extends('layouts.app')
@section('titulo', 'Nuevo material didáctico')
@section('contenido')
    @include('partials.biblioteca-fisica-crud-header', ['active' => 'materiales'])
    @include('partials.admin-breadcrumb', [
        'items' => [
            ['label' => 'Panel',              'url' => route('home')],
            ['label' => 'Material didáctico', 'url' => route('biblioteca-fisica.materiales.index')],
            ['label' => 'Nuevo material',     'url' => null],
        ],
    ])

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="bv-crud-page-title mb-0">
            <i class="fa fa-cube text-info mr-2"></i>Nuevo material didáctico
        </h1>
        <a href="{{ route('biblioteca-fisica.materiales.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fa fa-arrow-left mr-1"></i>Volver
        </a>
    </div>

    @include('material-didactico.partials.errores')

    <form action="{{ route('biblioteca-fisica.materiales.store') }}" method="POST">
        @csrf

        @include('material-didactico.materiales._form')

        <div class="bv-form-actions">
            <button type="submit" class="btn btn-info btn-sm px-4">
                <i class="fa fa-save mr-1"></i>Guardar material
            </button>
            <a href="{{ route('biblioteca-fisica.materiales.index') }}" class="btn btn-outline-secondary btn-sm">
                Cancelar
            </a>
        </div>
    </form>
@endsection
