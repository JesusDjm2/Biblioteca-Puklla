@extends('layouts.app')
@section('titulo', 'Editar documento — Repositorio')
@section('contenido')
    @php
        $volver = old('volver', request('volver'));
        $urlVolver = $volver === 'estructura' ? route('admin.repositorio.estructura.index') : route('admin.repositorio.items.index');
    @endphp
    @include('partials.repo-crud-header', ['active' => $volver === 'estructura' ? 'estructura' : 'items'])
    @include('partials.admin-breadcrumb', [
        'items' => [
            ['label' => 'Panel', 'url' => route('home')],
            ['label' => 'Repositorio', 'url' => $urlVolver],
            ['label' => 'Editar documento', 'url' => null],
        ],
    ])

    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div style="max-width: 46rem;">
            <h1 class="bv-crud-page-title mb-0">{{ Str::limit($item->titulo, 140) }}</h1>
            <p class="bv-crud-meta">
                {{ $item->coleccion?->ruta }}
                · <span class="badge badge-{{ $item->estado_info['clase'] }}">{{ $item->estado_info['nombre'] }}</span>
            </p>
        </div>
        <div class="mt-2">
            @if ($item->estado === 'publicado')
                <a href="{{ route('repositorio.show', $item->slug) }}" target="_blank" class="btn btn-outline-info btn-sm mr-1"><i class="fa fa-external-link"></i> Ver público</a>
            @endif
            <a href="{{ $urlVolver }}" class="btn btn-outline-secondary btn-sm"><i class="fa fa-arrow-left"></i> Volver</a>
        </div>
    </div>

    <form action="{{ route('admin.repositorio.items.update', $item) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="volver" value="{{ $volver }}">
        @method('PUT')
        @include('admin.repositorio.items._form')
        <div class="d-flex justify-content-end mb-4">
            <a href="{{ $urlVolver }}" class="btn btn-secondary mr-2">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fa fa-save mr-1"></i> Guardar cambios</button>
        </div>
    </form>
@endsection
