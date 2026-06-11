@extends('layouts.app')
@section('contenido')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h3>Registrar Nuevo Usuario
                <a href="{{ route('admin.index') }}" class="btn btn-danger btn-sm float-right">Volver</a>
            </h3>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('admin.store') }}">
                @csrf        
                <div class="form-group mt-3">
                    <label for="name">Nombre:</label>
                    <input id="name" type="text" class="form-control form-control-sm rounded-pill @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
        
                <div class="form-group">
                    <label for="email">Correo Electrónico:</label>
                    <input id="email" type="email" class="form-control form-control-sm rounded-pill @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
        
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input id="password" type="password" class="form-control form-control-sm rounded-pill @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
        
                <div class="form-group">
                    <label for="password-confirm">Confirmar Contraseña:</label>
                    <input id="password-confirm" type="password" class="form-control form-control-sm rounded-pill" name="password_confirmation" required autocomplete="new-password">
                </div>
        
                <div class="form-group">
                    <label for="role">Rol:</label>
                    <select id="role" class="form-control form-control-sm rounded-pill @error('role') is-invalid @enderror" name="role" required>
                        <option value="" disabled selected>{{ __('Select a role') }}</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                    @error('role')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
        
                <button type="submit" class="btn btn-sm btn-primary">Registrar</button>
            </form>
        </div>
    </div>
</div>
@endsection
