<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function index()
    {
        $admins = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'alumno');
        })->get();
        return view('auth.admins.index', compact('admins'));
    }
    public function alumnos()
    {
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'alumno');
        })->get();
        return view('auth.admins.alumnos', compact('admins'));
    }
    public function bibliotecario()
    {
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'biblioteca');
        })->get();
        return view('admin.bibliotecario', compact('admins'));
    }
    public function videos()
    {
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'videos');
        })->get();
        return view('admin.videos', compact('admins'));
    }
    public function canciones()
    {
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'audios');
        })->get();
        return view('admin.canciones', compact('admins'));
    }
    public function sisicha()
    {
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'sisicha');
        })->get();
        return view('admin.sisicha', compact('admins'));
    }
    public function create()
    {
        $roles = Role::all();
        return view('auth.register', compact('roles'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.index')->with('success', 'Usuario creado exitosamente.');
    }
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('auth.admins.edit', compact('user', 'roles'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        $user->syncRoles($request->role);

        return redirect()->route('admin.index')->with('success', 'Usuario actualizado exitosamente.');
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.index')->with('success', 'Usuario eliminado exitosamente.');
    }
}
