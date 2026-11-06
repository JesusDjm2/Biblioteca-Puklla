@extends('layouts.app')
@section('titulo', 'Editar video')
@section('contenido')
    @php
        $volver = old('volver', request('volver'));
        // Volver/Cancelar llevan a la lista de la categoría (o a las tarjetas si se partió de ahí sin categoría).
        $urlVolver = $video->categoria_id
            ? route('videos.index', ['categoria_id' => $video->categoria_id])
            : ($volver === 'categorias' ? route('videoscat.index') : route('videos.index'));
    @endphp
    @include('partials.videos-crud-header', ['active' => $volver === 'categorias' ? 'categorias' : 'videos'])
    @include('partials.admin-breadcrumb', [
        'items' => [
            ['label' => 'Panel',  'url' => route('home')],
            ['label' => 'Videos', 'url' => route('videos.index')],
            ['label' => 'Editar', 'url' => null],
        ],
    ])

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="bv-crud-page-title mb-0">
            <i class="fa fa-edit text-warning mr-2"></i>Editar video
        </h1>
        <a href="{{ $urlVolver }}" class="btn btn-sm btn-outline-secondary">
            <i class="fa fa-arrow-left mr-1"></i>Volver
        </a>
    </div>

    @include('material-didactico.partials.errores')

    <form action="{{ route('videos.update', $video->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="volver" value="{{ $volver }}">

        @include('videos._form')

        <div class="bv-form-actions">
            <button type="submit" class="btn btn-info btn-sm px-4">
                <i class="fa fa-save mr-1"></i>Actualizar video
            </button>
            <a href="{{ $urlVolver }}" class="btn btn-outline-secondary btn-sm">Cancelar</a>
        </div>
    </form>
@endsection
