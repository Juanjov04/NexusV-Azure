<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel Admin | Inscripciones Globales') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Pestañas de Navegación del Administrador --}}
            <div class="mb-6 flex space-x-4 border-b border-gray-200">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border-b-2 border-transparent text-gray-600 hover:border-gray-300">
                    {{ __('Usuarios') }}
                </a>
                <a href="{{ route('admin.courses.index') }}" class="px-4 py-2 border-b-2 border-transparent text-gray-600 hover:border-gray-300">
                    {{ __('Cursos Globales') }}
                </a>
                <a href="{{ route('admin.enrollments.index') }}" class="px-4 py-2 border-b-2 border-indigo-500 text-indigo-600 font-semibold">
                    {{ __('Inscripciones Globales') }}
                </a>
            </div>
            {{-- FIN PESTAÑAS --}}

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-6">{{ __('Todas las Inscripciones del Sistema') }}</h3>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Inscripción</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Curso</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comprador</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($enrollments as $enrollment)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $enrollment->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $enrollment->course->title }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $enrollment->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $enrollment->created_at->format('d M Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        {{-- FORMULARIO DELETE GLOBAL --}}
                                        <form action="{{ route('admin.enrollments.destroy', $enrollment) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar esta inscripción ID {{ $enrollment->id }}? Esta acción es irreversible.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $enrollments->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>