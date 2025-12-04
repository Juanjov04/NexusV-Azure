<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Curso') }}: {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                {{-- Formulario de Edición --}}
                <form method="POST" action="{{ route('seller.courses.update', $course) }}">
                    @csrf
                    @method('PUT') {{-- Directiva necesaria para el método UPDATE --}}

                    <h3 class="text-xl font-bold text-gray-900 mb-4">{{ __('Detalles Principales del Curso') }}</h3>

                    {{-- Título --}}
                    <div>
                        <x-input-label for="title" :value="__('Título del Curso')" />
                        <x-text-input 
                            id="title" 
                            name="title" 
                            type="text" 
                            class="mt-1 block w-full" 
                            :value="old('title', $course->title)" 
                            required 
                            autofocus 
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('title')" />
                    </div>

                    {{-- Encabezado (Header) --}}
                    <div class="mt-4">
                        <x-input-label for="header" :value="__('Encabezado (Resumen Breve)')" />
                        <x-text-input 
                            id="header" 
                            name="header" 
                            type="text" 
                            class="mt-1 block w-full" 
                            :value="old('header', $course->header)" 
                            required 
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('header')" />
                    </div>

                    {{-- Descripción --}}
                    <div class="mt-4">
                        <x-input-label for="description" :value="__('Descripción Completa')" />
                        <textarea 
                            id="description" 
                            name="description" 
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            rows="5"
                            required
                        >{{ old('description', $course->description) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    {{-- Precio --}}
                    <div class="mt-4">
                        <x-input-label for="price" :value="__('Precio ($)')" />
                        <x-text-input 
                            id="price" 
                            name="price" 
                            type="number" 
                            step="0.01" 
                            class="mt-1 block w-full" 
                            :value="old('price', $course->price)" 
                            required 
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('price')" />
                    </div>

                    {{-- Fecha Programada --}}
                    <div class="mt-4">
                        <x-input-label for="scheduled_date" :value="__('Fecha Programada')" />
                        <x-text-input 
                            id="scheduled_date" 
                            name="scheduled_date" 
                            type="datetime-local" 
                            class="mt-1 block w-full" 
                            :value="old('scheduled_date', $course->scheduled_date->format('Y-m-d\TH:i'))" 
                            required 
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('scheduled_date')" />
                    </div>
                    
                    {{-- Publicado (Checkbox) --}}
                    <div class="flex items-center mt-6">
                        <input 
                            id="is_published" 
                            name="is_published" 
                            type="checkbox" 
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                            value="1"
                            @checked(old('is_published', $course->is_published))
                        >
                        <x-input-label for="is_published" :value="__('Publicar este curso ahora')" class="ml-2" />
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('seller.courses.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                            {{ __('Cancelar') }}
                        </a>
                        <x-primary-button>
                            {{ __('Guardar Cambios') }}
                        </x-primary-button>
                    </div>
                </form>

                
                {{-- AÑADIDO: GESTIÓN DE MÓDULOS (Fase 4.1) --}}
                <div class="border-t border-gray-200 mt-8 pt-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">{{ __('Gestión de Módulos (Contenido)') }}</h3>
                    
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('seller.modules.create', $course) }}">
                            <x-primary-button class="bg-green-600 hover:bg-green-700">
                                {{ __('+ Añadir Nuevo Módulo') }}
                            </x-primary-button>
                        </a>
                    </div>

                    @if($course->modules->isEmpty())
                        <p class="text-gray-500">Aún no hay módulos. Añade el contenido del curso.</p>
                    @else
                        <ul class="divide-y divide-gray-200 border rounded-lg">
                            @foreach ($course->modules as $module)
                                <li class="p-3 flex justify-between items-center bg-white hover:bg-gray-50">
                                    <div class="font-medium">
                                        [{{ $module->sequence_order }}] {{ $module->title }}
                                    </div>
                                    <div class="text-sm">
                                        <a href="{{ route('seller.modules.edit', ['course' => $course->id, 'module' => $module->id]) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Editar</a>
                                        <form action="{{ route('seller.modules.destroy', ['course' => $course->id, 'module' => $module->id]) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar módulo?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                {{-- FIN GESTIÓN DE MÓDULOS --}}
                
                
                {{-- Formulario para Eliminar Curso (Permanente) --}}
                <div class="border-t border-gray-200 mt-8 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Eliminar Curso') }}</h3>
                    <p class="text-sm text-gray-600 mb-4">{{ __('Una vez eliminado, no se puede recuperar. Esto también elimina todos los registros de módulos e inscripciones.') }}</p>
                    
                    <form method="POST" action="{{ route('seller.courses.destroy', $course) }}">
                        @csrf
                        @method('DELETE')
                        <x-danger-button>
                            {{ __('Eliminar Curso Permanentemente') }}
                        </x-danger-button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>