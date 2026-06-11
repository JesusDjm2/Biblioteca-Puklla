@extends('layouts.app')
@section('titulo', 'Inicio')
@section('contenido')
    @php
        $soloBiblioteca =
            auth()->user()->hasRole('biblioteca') && !auth()->user()->hasRole('admin');
    @endphp

    @if ($soloBiblioteca)
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <h4 class="text-center mb-4 mt-2">Panel administración — Biblioteca</h4>
                <p class="text-center text-muted mb-4">Gestiona la biblioteca virtual y el catálogo físico.</p>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-5 p-3">
                <a href="{{ route('libros.index') }}" class="text-decoration-none text-dark d-block h-100">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body p-0 text-center">
                            <img src="{{ asset('img/biblioteca-muestra.webp') }}" class="w-100 rounded-top"
                                alt="" style="max-height: 220px; object-fit: cover;" loading="lazy">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title mb-1"><i class="fa fa-book text-primary"></i> Biblioteca virtual</h5>
                            <p class="card-text small text-muted mb-0">Libros, categorías y secciones en línea.</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-5 p-3">
                <a href="{{ route('biblioteca-fisica.categorias.index') }}" class="text-decoration-none text-dark d-block h-100">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body p-0 text-center">
                            <img src="{{ asset('img/Libros.webp') }}" class="w-100 rounded-top" alt=""
                                style="max-height: 220px; object-fit: cover;" loading="lazy">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title mb-1"><i class="fa fa-archive text-primary"></i> Biblioteca física</h5>
                            <p class="card-text small text-muted mb-0">Categorías y ejemplares del acervo físico.</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-lg-12">
                <h4 class="text-center mb-4 mt-3">Administrador general Puklla Virtual</h4>
            </div>
            <div class="col-lg-3 p-3">
                <a href="{{ route('libros.index') }}">
                    <img src="{{ asset('img/Libros.webp') }}" width="100%" loading="lazy" alt="Biblioteca">
                </a>
            </div>
            <div class="col-lg-3 p-3">
                <a href="{{ route('libros.index') }}">
                    <img src="{{ asset('img/Sisichakuna.webp') }}" width="100%" loading="lazy" alt="Sisichakuna">
                </a>
            </div>
            <div class="col-lg-3 p-3">
                <a href="{{ route('libros.index') }}">
                    <img src="{{ asset('img/Videos.webp') }}" width="100%" loading="lazy" alt="Videos">
                </a>
            </div>
            <div class="col-lg-3 p-3">
                <a href="">
                    <img src="{{ asset('img/Canciones.webp') }}" width="100%" loading="lazy" alt="Canciones">
                </a>
            </div>
        </div>
    @endif
@endsection
