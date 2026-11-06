@extends('layouts.app')
@section('titulo', 'Nueva categoría de videos')
@section('contenido')
    @include('partials.categoria-simple-form', [
        'header' => 'partials.videos-crud-header',
        'modulo' => 'Videos',
        'indexRoute' => route('videoscat.index'),
        'action' => route('videoscat.store'),
        'categoria' => null,
        'titulo' => 'Nueva categoría de videos',
        'ayuda' => 'Agrupa los videos por tema. Luego podrás agregar videos directamente desde su tarjeta.',
    ])
@endsection
