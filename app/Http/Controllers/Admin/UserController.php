<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Muestra la lista de todos los usuarios (Compradores, Vendedores, Administradores).
     */
    public function index(): View
    {
        // 1. Autorización: Verifica que el usuario sea Admin (Master o Secondary) para acceder al panel.
        Gate::authorize('manage-system'); 

        // 2. Lógica: Listar todos los usuarios, ordenados por rol para ver la jerarquía.
        $users = User::all()->sortByDesc('role'); 

        // Nota: Asumimos que la vista para el Admin está en 'admin.users.index'
        return view('admin.users.index', compact('users'));
    }

    /**
     * Muestra el formulario para crear un nuevo usuario (incluyendo Admins Secundarios).
     */
    public function create(): View
    {
        Gate::authorize('manage-user'); // Permiso para crear (cualquier Admin)
        return view('admin.users.create');
    }

    /**
     * Almacena un NUEVO ADMINISTRADOR SECUNDARIO en la base de datos.
     */
    // Este método es usado por la ruta 'admin.users.store-admin' (creada en web.php)
    public function storeAdmin(Request $request): RedirectResponse
    {
        Gate::authorize('manage-user');

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => 'required|min:8|confirmed',
        ]);
        
        // El rol siempre será 'admin-secondary'
        User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => 'admin-secondary',
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Administrador Secundario creado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un usuario existente.
     */
    public function edit(User $user): View
    {
        // Autorización: Protege al Administrador Maestro (si el usuario autenticado es Secundario)
        Gate::authorize('manage-user', $user); 

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Actualiza la información de un usuario.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        // Autorización: Protege al Administrador Maestro
        Gate::authorize('manage-user', $user); 

        // Validación
        $request->validate([
            'name' => 'required|string|max:255',
            // El rol del usuario objetivo puede ser editado (solo si es un rol válido)
            'role' => ['required', 'in:admin-master,admin-secondary,seller,buyer'], 
            'email' => ['required', 'email', Rule::unique('users')->ignore($user)],
        ]);

        $user->update($request->only('name', 'email', 'role'));

        return redirect()->route('admin.users.index')->with('success', "Usuario {$user->name} actualizado.");
    }

    /**
     * Elimina a un usuario.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Autorización: Protege al Administrador Maestro de ser eliminado por un Admin Secundario
        Gate::authorize('manage-user', $user); 

        // No permitir que un usuario se elimine a sí mismo
        if (Auth::id() === $user->id) {
             return redirect()->route('admin.users.index')->with('error', "No puedes eliminar tu propia cuenta desde aquí.");
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', "Usuario {$user->name} eliminado.");
    }
}