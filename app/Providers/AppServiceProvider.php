<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    // ... (Método register())

    public function boot(): void
    {
        // Definición del Gate 'is-seller' (EXISTENTE)
        Gate::define('is-seller', function (User $user) {
            return $user->isSeller(); 
        });
        
        // =======================================================
        // NUEVOS GATES DE ADMINISTRACIÓN
        // =======================================================

        // 1. Gate para acceso general al Panel de Administración
        Gate::define('manage-system', function (User $user) {
            // Solo los Admins (Maestro o Secundario) pueden acceder al sistema de gestión.
            return $user->isAdmin();
        });

        // 2. Gate para modificar/eliminar Usuarios (Lógica de Jerarquía)
        Gate::define('manage-user', function (User $auth_user, User $target_user = null) {
            // El usuario autenticado debe ser Admin
            if (! $auth_user->isAdmin()) {
                return false;
            }

            // Si no hay un usuario objetivo (ej: creando un usuario), se permite.
            if (! $target_user) {
                return true;
            }

            // REGLA CRÍTICA: Un Administrador Secundario NO puede tocar al Administrador Maestro.
            if ($auth_user->role === 'admin-secondary' && $target_user->isMasterAdmin()) {
                return false;
            }

            // Si es Master Admin, o si es Secundario tocando a otro Secundario/Vendedor/Comprador, se permite.
            return true;
        });
    }
}