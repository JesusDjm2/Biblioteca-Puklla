@extends('errors.layout')

@section('codigo', '429')
@section('tono', 'is-warning')
@section('icono', 'fa-hourglass-half')
@section('titulo', 'Demasiadas solicitudes')

@section('mensaje')
    Hiciste varias acciones muy seguidas y el sistema puso una pausa de seguridad. Espera unos segundos y vuelve a intentarlo.
@endsection

@section('acciones')
    <a href="{{ url()->previous() }}" class="bv-error-btn bv-error-btn--primary">
        <i class="fa fa-refresh"></i>Intentar de nuevo
    </a>
@endsection
