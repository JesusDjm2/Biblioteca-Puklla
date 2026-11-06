@extends('layouts.app')
@section('titulo', 'Nueva canción')
@section('contenido')
    @php
        $volver = old('volver', request('volver'));
        $urlVolver = $volver === 'categorias' ? route('cancionescat.index') : route('canciones.index');
    @endphp
    @include('partials.canciones-crud-header', ['active' => $volver === 'categorias' ? 'categorias' : 'canciones'])
    @include('partials.admin-breadcrumb', [
        'items' => [
            ['label' => 'Panel', 'url' => route('home')],
            ['label' => 'Canciones', 'url' => $urlVolver],
            ['label' => 'Nueva canción', 'url' => null],
        ],
    ])

    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h1 class="bv-crud-page-title mb-0">
                Nueva canción
                @if ($catElegida = $categorias->firstWhere('id', $cancion->categoria_id))
                    <small class="text-muted" style="font-size:.6em;">en <i class="fa fa-music"></i> {{ $catElegida->nombre }}</small>
                @endif
            </h1>
            <p class="bv-crud-meta">Registra la canción y pega el enlace de YouTube, Spotify o Google Drive donde se puede escuchar.</p>
        </div>
        <a href="{{ $urlVolver }}" class="btn btn-outline-secondary btn-sm mt-2"><i class="fa fa-arrow-left"></i> Volver</a>
    </div>

    @include('material-didactico.partials.errores')

    <form action="{{ route('canciones.store') }}" method="POST">
        @csrf
        <input type="hidden" name="volver" value="{{ $volver }}">
        @include('canciones._form')

        <div class="d-flex justify-content-end mb-4">
            <a href="{{ $urlVolver }}" class="btn btn-secondary mr-2">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fa fa-save mr-1"></i> Guardar canción</button>
        </div>
    </form>
@endsection
