<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Explorar Cursos Disponibles') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg mb-8">
                
                {{-- Formulario de Búsqueda --}}
                <form method="GET" action="{{ route('courses.index') }}" class="flex space-x-4">
                    <x-text-input type="search" name="search" placeholder="Buscar por título o encabezado..." class="w-full" value="{{ request('search') }}" />
                    <x-primary-button type="submit">
                        {{ __('Buscar') }}
                    </x-primary-button>
                    @if(request('search'))
                        <a href="{{ route('courses.index') }}" class="py-2 px-4 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 self-center">{{ __('Limpiar') }}</a>
                    @endif
                </form>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($courses as $course)
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg hover:shadow-xl transition duration-300">
                        <div class="p-6">
                            
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $course->title }}</h3>
                            <p class="text-sm text-indigo-600 mb-4">{{ $course->header }}</p>
                            
                            <p class="text-gray-700 text-sm mb-4 line-clamp-3">{{ $course->description }}</p>
                            
                            <div class="flex justify-between items-center text-xs text-gray-500 mb-4">
                                <span>🗓️ Clase: {{ $course->scheduled_date->format('d M Y H:i') }}</span>
                                <span>👨‍🏫 Por: {{ $course->user->name }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-100">
                                <span class="text-lg font-extrabold text-green-600">${{ number_format($course->price, 2) }}</span>
                                
                                {{-- Enlace a la vista de detalle/inscripción --}}
                                <a href="{{ route('courses.show', $course) }}" class="bg-indigo-500 text-white py-2 px-4 rounded-md text-sm hover:bg-indigo-600 transition duration-150">
                                    {{ __('Ver Detalles / Inscribirse') }}
                                </a>
                            </div>

                        </div>
                    </div>
                @empty
                    <p class="col-span-3 text-center text-gray-500">No se encontraron cursos publicados o que coincidan con la búsqueda.</p>
                @endforelse
            </div>

            {{-- Paginación --}}
            <div class="mt-8">
                {{ $courses->links() }}
            </div>

        </div>
    </div>
</x-app-layout>