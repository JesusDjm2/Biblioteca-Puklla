@extends('layouts.app')
@section('titulo', 'Nueva categoría de canciones')
@section('contenido')
    @include('partials.categoria-simple-form', [
        'header' => 'partials.canciones-crud-header',
        'modulo' => 'Canciones',
        'indexRoute' => route('cancionescat.index'),
        'action' => route('cancionescat.store'),
        'categoria' => null,
        'titulo' => 'Nueva categoría de canciones',
        'ayuda' => 'Agrupa las canciones por tema. Luego podrás registrar canciones directamente desde su tarjeta.',
    ])
@endsection
