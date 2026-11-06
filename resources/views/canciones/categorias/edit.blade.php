@extends('layouts.app')
@section('titulo', 'Editar categoría de canciones')
@section('contenido')
    @include('partials.categoria-simple-form', [
        'header' => 'partials.canciones-crud-header',
        'modulo' => 'Canciones',
        'indexRoute' => route('cancionescat.index'),
        'action' => route('cancionescat.update', $categoria->id),
        'categoria' => $categoria,
        'titulo' => 'Editar categoría: '.$categoria->nombre,
        'ayuda' => 'Cambia el nombre de la categoría de canciones.',
    ])
@endsection
