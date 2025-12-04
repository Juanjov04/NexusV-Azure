<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel de Administración | Editar Usuario: ') . $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- AÑADIDO: Pestañas de Navegación del Administrador --}}
            <div class="mb-6 flex space-x-4 border-b border-gray-200">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border-b-2 border-indigo-500 text-indigo-600 font-semibold">
                    {{ __('Usuarios') }}
                </a>
                <a href="{{ route('admin.courses.index') }}" class="px-4 py-2 border-b-2 border-transparent text-gray-600 hover:border-gray-300">
                    {{ __('Cursos Globales') }}
                </a>
                <a href="{{ route('admin.enrollments.index') }}" class="px-4 py-2 border-b-2 border-transparent text-gray-600 hover:border-gray-300">
                    {{ __('Inscripciones Globales') }}
                </a>
            </div>
            {{-- FIN PESTAÑAS --}}

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PATCH')

                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        
                        <div class="mt-4">
                            <x-input-label for="role" :value="__('Rol del Usuario')" />
                            <select id="role" name="role" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                {{-- 
                                    IMPORTANTE: 
                                    El Administrador Maestro (Master) NO debe poder ser cambiado de rol por nadie (protegido por Gate).
                                --}}
                                @if ($user->role === 'admin-master')
                                    <option value="admin-master" selected>{{ __('Administrador Maestro (Fijo)') }}</option>
                                @else
                                    <option value="buyer" @selected(old('role', $user->role) == 'buyer')>{{ __('Comprador') }}</option>
                                    <option value="seller" @selected(old('role', $user->role) == 'seller')>{{ __('Vendedor') }}</option>
                                    {{-- Solo el Admin Master puede ascender a Admin Secundario --}}
                                    @can('is-admin-master') 
                                        <option value="admin-secondary" @selected(old('role', $user->role) == 'admin-secondary')>{{ __('Administrador Secundario') }}</option>
                                    @endcan
                                    
                                @endif
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>
                        
                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Actualizar Usuario') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>