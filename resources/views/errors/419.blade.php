@extends('errors.layout')

@section('codigo', '419')
@section('tono', 'is-warning')
@section('icono', 'fa-clock-o')
@section('titulo', 'La página caducó')

@section('mensaje')
    Tu sesión o el formulario que estabas llenando llevaba abierto demasiado tiempo. No se perdió nada grave: vuelve a la página anterior e inténtalo de nuevo.
@endsection

@section('acciones')
    <a href="{{ url()->previous() }}" class="bv-error-btn bv-error-btn--primary">
        <i class="fa fa-refresh"></i>Volver e intentar de nuevo
    </a>
    <a href="{{ route('index') }}" class="bv-error-btn bv-error-btn--ghost">
        <i class="fa fa-home"></i>Ir al inicio
    </a>
@endsection
