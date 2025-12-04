<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Contenido del Curso: ') . $course->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg p-6">
                
                {{-- Mensajes de feedback --}}
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
                @endif

                {{-- CERTIFICADO --}}
                @if ($progressPercent == 100)
                    <div class="bg-indigo-100 border-l-4 border-indigo-500 text-indigo-700 p-4 mb-8 flex flex-col md:flex-row justify-between items-center" role="alert">
                        <div class="mb-4 md:mb-0">
                            <p class="font-bold text-lg">{{ __('¡Felicidades! Curso Completado.') }}</p>
                            <p>{{ __('Has completado todas las lecciones. Obtén tu certificado ahora mismo.') }}</p>
                        </div>
                        
                        <a href="{{ route('courses.certify', $course) }}">
                            <x-primary-button class="bg-indigo-600 hover:bg-indigo-700">
                                {{ __('Generar Certificado') }}
                            </x-primary-button>
                        </a>
                    </div>
                @endif

                {{-- Encabezado de Progreso --}}
                <h3 class="text-2xl font-bold mb-4">Progreso General</h3>
                <div class="mb-8">
                    <div class="w-full bg-gray-200 rounded-full h-4 mb-2">
                        <div class="bg-indigo-600 h-4 rounded-full transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
                    </div>
                    <p class="text-3xl font-extrabold text-indigo-600 mb-1">{{ $progressPercent }}% Completado</p>
                    <p class="text-sm text-gray-500">{{ $completedModules }} de {{ $totalModules }} lecciones vistas.</p>
                </div>

                <h3 class="text-2xl font-bold mb-6 border-b pb-2">Temario del Curso</h3>
                
                {{-- LISTA DE MÓDULOS --}}
                <div class="space-y-6">
                    @forelse ($modules as $module)
                        @php
                            $isCompleted = $module->progress->isNotEmpty() && $module->progress->first()->is_completed;
                        @endphp
                        
                        {{-- Contenedor del Módulo --}}
                        <div class="border rounded-lg shadow-sm overflow-hidden {{ $isCompleted ? 'bg-green-50 border-green-200' : 'bg-white border-gray-200' }}">
                            
                            {{-- Cabecera del Módulo (Título y Estado) --}}
                            <div class="p-4 flex items-center justify-between bg-opacity-50 border-b border-gray-100">
                                <div class="flex items-center space-x-3">
                                    <span class="bg-gray-800 text-white text-xs font-bold px-2 py-1 rounded-full">{{ $module->sequence_order }}</span>
                                    <h4 class="font-bold text-lg {{ $isCompleted ? 'text-green-800' : 'text-gray-800' }}">
                                        {{ $module->title }}
                                    </h4>
                                    @if ($isCompleted)
                                        <span class="text-xs font-semibold text-green-600 bg-green-100 px-2 py-0.5 rounded-full border border-green-200">✅ Visto</span>
                                    @endif
                                </div>

                                {{-- Botón Marcar como Completado --}}
                                @if (!$isCompleted)
                                    <form method="POST" action="{{ route('progress.store', $module) }}">
                                        @csrf
                                        <x-primary-button class="bg-indigo-600 hover:bg-indigo-700 text-xs">
                                            {{ __('Marcar como Visto') }}
                                        </x-primary-button>
                                    </form>
                                @endif
                            </div>

                            {{-- CUERPO DEL CONTENIDO (Aquí es donde ocurre la magia) --}}
                            <div class="p-5 bg-white">
                                
                                {{-- CASO 1: VIDEO --}}
                                @if($module->content_type === 'video')
                                    <div class="w-full max-w-4xl mx-auto">
                                        <div class="relative pb-[56.25%] h-0 overflow-hidden rounded-lg shadow-lg bg-black">
                                            {{-- Usamos asset('storage/...') para acceder al archivo --}}
                                            <video class="absolute top-0 left-0 w-full h-full" controls controlsList="nodownload">
                                                <source src="{{ asset('storage/' . $module->content_path) }}" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-2 text-center">Reproduce el video de la lección.</p>
                                    </div>

                                {{-- CASO 2: DOCUMENTO --}}
                                @elseif($module->content_type === 'document')
                                    <div class="flex items-center justify-center p-6 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50">
                                        <div class="text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                            <p class="mt-1 text-sm text-gray-600">Material de lectura disponible</p>
                                            <a href="{{ asset('storage/' . $module->content_path) }}" target="_blank" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
                                                📄 Descargar / Ver Documento
                                            </a>
                                        </div>
                                    </div>

                                {{-- CASO 3: LINK --}}
                                @elseif($module->content_type === 'link')
                                    <div class="flex items-center p-4 bg-blue-50 border-l-4 border-blue-400 rounded-r-lg">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                                                <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-blue-700">
                                                Esta lección es un recurso externo:
                                                <a href="{{ $module->content_url }}" target="_blank" class="font-bold underline hover:text-blue-600">
                                                    {{ $module->content_url }}
                                                </a>
                                            </p>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 bg-gray-50 rounded-lg border-2 border-dashed">
                            <p class="text-gray-500">Este curso aún no tiene lecciones cargadas.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>