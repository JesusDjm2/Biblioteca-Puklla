@extends('errors.layout')

@section('codigo', '405')
@section('icono', 'fa-ban')
@section('titulo', 'Acción no permitida')

@section('mensaje')
    Intentaste hacer algo que esta dirección no admite (por ejemplo, abrir directamente un enlace pensado solo para un botón o formulario). Prueba repitiendo la acción desde el botón correspondiente.
@endsection

@section('acciones')
    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('index') }}" class="bv-error-btn bv-error-btn--ghost">
        <i class="fa fa-arrow-left"></i>Volver atrás
    </a>
    <a href="{{ route('index') }}" class="bv-error-btn bv-error-btn--primary">
        <i class="fa fa-home"></i>Ir al inicio
    </a>
@endsection
