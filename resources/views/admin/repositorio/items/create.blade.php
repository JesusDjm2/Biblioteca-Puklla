@extends('layouts.app')
@section('titulo', 'Nuevo documento — Repositorio')
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
            ['label' => 'Nuevo documento', 'url' => null],
        ],
    ])

    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h1 class="bv-crud-page-title mb-0">
                Catalogar documento
                @if ($item->coleccion)
                    <small class="text-muted" style="font-size:.6em;">en <i class="fa fa-folder-open"></i> {{ $item->coleccion->nombre }}</small>
                @endif
            </h1>
            <p class="bv-crud-meta">Registra un trabajo de investigación, tesis u otro documento. El enlace corto y el QR se generan al guardar.</p>
        </div>
        <a href="{{ $urlVolver }}" class="btn btn-outline-secondary btn-sm mt-2"><i class="fa fa-arrow-left"></i> Volver</a>
    </div>

    <form action="{{ route('admin.repositorio.items.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="volver" value="{{ $volver }}">
        @include('admin.repositorio.items._form')
        <div class="d-flex justify-content-end mb-4">
            <a href="{{ $urlVolver }}" class="btn btn-secondary mr-2">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fa fa-save mr-1"></i> Guardar documento</button>
        </div>
    </form>
@endsection
