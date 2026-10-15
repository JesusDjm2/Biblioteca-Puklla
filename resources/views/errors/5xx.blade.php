@extends('errors.layout')

@section('codigo', $exception->getStatusCode() ?? '5xx')
@section('tono', 'is-danger')
@section('icono', 'fa-exclamation-triangle')
@section('titulo', 'Algo salió mal')

@section('mensaje')
    Ocurrió un error inesperado en el servidor. Inténtalo de nuevo en un momento; si el problema sigue, avisa al administrador del sistema.
@endsection

@section('acciones')
    <button type="button" class="bv-error-btn bv-error-btn--ghost" onclick="location.reload()">
        <i class="fa fa-refresh"></i>Reintentar
    </button>
    <a href="{{ route('index') }}" class="bv-error-btn bv-error-btn--primary">
        <i class="fa fa-home"></i>Ir al inicio
    </a>
@endsection
