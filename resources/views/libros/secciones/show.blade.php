@extends('layouts.app')
@section('titulo', 'Secciones')
@section('contenido')
    <div class="row">
        <div class="col-md-12 border-bottom">
            <h3>Seccion: <span class="text-primary"> {{ $seccion->nombre }}</span>
                <a href="{{ url()->previous() }}" class="btn btn-danger btn-sm float-right">Volver</a>
            </h3>
        </div>
        <div class="col-lg-12 mt-4">
            <h4>Categorias:</h4>
            @if ($categorias->isEmpty())
                <p>No hay categorías asociadas a esta sección.</p>
            @else
                <ul>
                    @foreach ($categorias as $categoria)
                        {{-- <li>{{ $categoria->nombre }}</li> --}}
                        <li>
                            {{ $categoria->nombre }}
                           → <span>{{ $categoria->libros_count }} libros</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
