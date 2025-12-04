<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Administrador Secundario') }}
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
                    
                    <form method="POST" action="{{ route('admin.users.store-admin') }}">
                        @csrf

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="mt-4">
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Crear Admin Secundario') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>