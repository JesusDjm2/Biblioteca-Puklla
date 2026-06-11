@extends('layouts.app')
@section('titulo', 'Inicio')
@section('contenido')
    <div class="fixed-image-container">
        <img src="{{ asset('img/biblioteca-muestra.jpg') }}" alt="Imagen Fija">
        <div class="overlay"></div>
        <div class="centered-content">
            <h2>EESP Pukllasunchis</h2>
            <h1>Sistema Bibliotecario</h1>
        </div>
    </div>
    <div class="container mt-4 contenedorContenido">
        <div class="row cardIndex">
        </div>
    </div>
@endsection
