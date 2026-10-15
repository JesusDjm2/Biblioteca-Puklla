@extends('errors.layout')

@section('codigo', $exception->getStatusCode() ?? '4xx')
@section('icono', 'fa-exclamation-circle')
@section('titulo', 'No se pudo completar la solicitud')

@section('mensaje')
    Algo en la solicitud no era válido. Vuelve atrás e inténtalo de nuevo; si el problema continúa, avisa al administrador.
@endsection

@section('acciones')
    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('index') }}" class="bv-error-btn bv-error-btn--ghost">
        <i class="fa fa-arrow-left"></i>Volver atrás
    </a>
    <a href="{{ route('index') }}" class="bv-error-btn bv-error-btn--primary">
        <i class="fa fa-home"></i>Ir al inicio
    </a>
@endsection
