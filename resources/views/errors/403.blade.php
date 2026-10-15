@extends('errors.layout')

@section('codigo', '403')
@section('tono', 'is-danger')
@section('icono', 'fa-lock')
@section('titulo', 'Acceso restringido')

@section('mensaje')
    No tienes permisos para entrar a esta sección. Si crees que deberías tener acceso, pide a un administrador que revise tu rol.
@endsection

@section('acciones')
    <a href="{{ route('index') }}" class="bv-error-btn bv-error-btn--primary">
        <i class="fa fa-home"></i>Ir al inicio
    </a>
@endsection
