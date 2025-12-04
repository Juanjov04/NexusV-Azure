<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">{{ __('Mis Cursos Inscritos') }}</h3>
                    
                    @forelse ($enrollments as $enrollment)
                        <div class="border p-4 mb-4 rounded-lg flex justify-between items-center bg-indigo-50/50 hover:shadow-lg transition duration-150 ease-in-out">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-800">{{ $enrollment->course->title }}</h4>
                                <p class="text-sm text-gray-600">
                                    {{ __('Instructor:') }} {{ $enrollment->course->user->name }}
                                </p>
                            </div>
                            <a href="{{ route('courses.show', $enrollment->course) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-medium">
                                {{ __('Ver Curso') }}
                            </a>
                        </div>
                    @empty
                        <p class="text-gray-600">
                            {{ __('Aún no estás inscrito en ningún curso. ¡Explora el catálogo y regístrate!') }}
                        </p>
                        <a href="{{ route('courses.index') }}" class="text-indigo-600 hover:text-indigo-900 mt-2 block font-medium">
                            {{ __('Ir a Explorar Cursos') }}
                        </a>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>