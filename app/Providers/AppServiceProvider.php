<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // La redirección a HTTPS es obligatoria para el deploy en Azure/producción.
        // Se mantiene fuera del if para asegurar que se ejecuta.
        // NOTA: Para ambientes de desarrollo local (como Valet o Homestead),
        // podrías querer envolver esto en un if ($this->app->environment('production')).
        URL::forceScheme('https');

        // =======================================================
        // 🔐 DEFINICIÓN DE GATES (PERMISOS)
        // =======================================================

        // Gate 'is-seller': Permiso para Vendedores (EXISTENTE)
        Gate::define('is-seller', function (User $user) {
            return $user->isSeller(); 
        });

        // 1. Gate 'manage-system': Acceso al Panel de Administración
        Gate::define('manage-system', function (User $user) {
            // Solo los Admins (Maestro o Secundario) pueden acceder.
            return $user->isAdmin();
        });

        // 2. Gate 'manage-user': Permiso para modificar/eliminar Usuarios (Lógica de Jerarquía)
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