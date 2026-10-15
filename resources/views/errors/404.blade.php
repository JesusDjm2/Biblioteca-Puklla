@extends('errors.layout')

@section('codigo', '404')
@section('icono', 'fa-map-signs')
@section('titulo', 'Página no encontrada')

@section('mensaje')
    La dirección a la que intentaste entrar no existe o fue movida. Revisa el enlace o vuelve al inicio.
@endsection

@section('acciones')
    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('index') }}" class="bv-error-btn bv-error-btn--ghost">
        <i class="fa fa-arrow-left"></i>Volver atrás
    </a>
    <a href="{{ route('index') }}" class="bv-error-btn bv-error-btn--primary">
        <i class="fa fa-home"></i>Ir al inicio
    </a>
@endsection
