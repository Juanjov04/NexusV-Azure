<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel de Administración | Gestión de Usuarios') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
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

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold">{{ __('Usuarios del Sistema') }}</h3>
                        <a href="{{ route('admin.users.create') }}">
                            <x-primary-button>{{ __('+ Crear Nuevo Administrador') }}</x-primary-button>
                        </a>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cursos / Inscripciones</th>
                                <th class="px-6 py-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($users as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        V: {{ $user->courses->count() }} | C: {{ $user->enrollments->count() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        {{-- El Gate verifica si el usuario autenticado puede tocar al usuario objetivo --}}
                                        @can('manage-user', $user) 
                                            <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                                            
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar a {{ $user->name }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                            </form>
                                        @else
                                            <span class="text-gray-400">{{ __('No permitido') }}</span>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>