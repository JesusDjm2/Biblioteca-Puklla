@extends('errors.layout')

@section('codigo', '503')
@section('tono', 'is-warning')
@section('icono', 'fa-wrench')
@section('titulo', 'En mantenimiento')

@section('mensaje')
    El sistema está en mantenimiento breve. Vuelve a intentarlo en unos minutos.
@endsection

@section('acciones')
    <button type="button" class="bv-error-btn bv-error-btn--primary" onclick="location.reload()">
        <i class="fa fa-refresh"></i>Reintentar
    </button>
@endsection
