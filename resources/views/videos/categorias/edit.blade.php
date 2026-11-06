@extends('layouts.app')
@section('titulo', 'Editar categoría de videos')
@section('contenido')
    @include('partials.categoria-simple-form', [
        'header' => 'partials.videos-crud-header',
        'modulo' => 'Videos',
        'indexRoute' => route('videoscat.index'),
        'action' => route('videoscat.update', $videoCategoria->id),
        'categoria' => $videoCategoria,
        'titulo' => 'Editar categoría: '.$videoCategoria->nombre,
        'ayuda' => 'Cambia el nombre de la categoría de videos.',
    ])
@endsection
