@extends('layouts.app')
@section('contenido')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h3>Editar Usuarioss
                    <button onclick="window.history.back();" class="btn btn-danger btn-sm float-right">Volver</button>
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
                <form method="POST" action="{{ route('admin.update', $user->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group mt-3">
                        <label for="name">Nombre:</label>
                        <input id="name" type="text"
                            class="form-control form-control-sm rounded-pill @error('name') is-invalid @enderror"
                            name="name" value="{{ old('name', $user->name) }}" required autocomplete="name" autofocus>
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Correo Electrónico:</label>
                        <input id="email" type="email"
                            class="form-control form-control-sm rounded-pill @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email', $user->email) }}" required autocomplete="email">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña (dejar en blanco si no se desea cambiar):</label>
                        <input id="password" type="password"
                            class="form-control form-control-sm rounded-pill @error('password') is-invalid @enderror"
                            name="password" autocomplete="new-password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password-confirm">Confirmar Contraseña:</label>
                        <input id="password-confirm" type="password" class="form-control form-control-sm rounded-pill"
                            name="password_confirmation" autocomplete="new-password">
                    </div>

                    @if ($user->roles->isNotEmpty())
                        <div class="form-group">
                            <label for="role">Rol:</label>
                            <select id="role"
                                class="form-control form-control-sm rounded-pill @error('role') is-invalid @enderror"
                                name="role" required>
                                <option value="" disabled>{{ __('Select a role') }}</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}"
                                        {{ $user->roles->first()->name === $role->name ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    @else
                        <div class="form-group">
                            <label for="role">Rol:</label>
                            <select id="role"
                                class="form-control form-control-sm rounded-pill @error('role') is-invalid @enderror"
                                name="role" required>
                                <option value="" disabled selected>{{ __('Select a role') }}</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                            @error('role')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    @endif


                    <button type="submit" class="btn btn-sm btn-primary">Actualizar</button>
                </form>
            </div>
        </div>
    </div>
@endsection
