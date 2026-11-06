@extends('layouts.app')
@section('titulo', 'Agregar video')
@section('contenido')
    @php
        $volver = old('volver', request('volver'));
        $urlVolver = $volver === 'categorias' ? route('videoscat.index') : route('videos.index');
    @endphp
    @include('partials.videos-crud-header', ['active' => $volver === 'categorias' ? 'categorias' : 'videos'])
    @include('partials.admin-breadcrumb', [
        'items' => [
            ['label' => 'Panel',   'url' => route('home')],
            ['label' => 'Videos',  'url' => route('videos.index')],
            ['label' => 'Agregar', 'url' => null],
        ],
    ])

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="bv-crud-page-title mb-0">
            <i class="fa fa-video-camera text-info mr-2"></i>Agregar video
            @if ($catElegida = $categorias->firstWhere('id', $video->categoria_id))
                <small class="text-muted" style="font-size:.6em;">en <i class="fa fa-film"></i> {{ $catElegida->nombre }}</small>
            @endif
        </h1>
        <a href="{{ $urlVolver }}" class="btn btn-sm btn-outline-secondary">
            <i class="fa fa-arrow-left mr-1"></i>Volver
        </a>
    </div>

    @include('material-didactico.partials.errores')

    <form action="{{ route('videos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="volver" value="{{ $volver }}">

        @include('videos._form')

        <div class="bv-form-actions">
            <button type="submit" class="btn btn-info btn-sm px-4">
                <i class="fa fa-save mr-1"></i>Guardar video
            </button>
            <a href="{{ $urlVolver }}" class="btn btn-outline-secondary btn-sm">Cancelar</a>
        </div>
    </form>
@endsection
