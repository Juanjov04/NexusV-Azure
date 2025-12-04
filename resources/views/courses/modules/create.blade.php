// resources/views/courses/modules/create.blade.php

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Agendar Nuevo Módulo para: ') . $course->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg p-6">
                
                {{-- CAMBIO CLAVE: Añadir enctype para subir archivos --}}
                <form method="POST" action="{{ route('seller.modules.store', $course) }}" enctype="multipart/form-data"> 
                    @csrf

                    {{-- 1. TÍTULO --}}
                    <div>
                        <x-input-label for="title" :value="__('Título de la Lección/Módulo')" />
                        <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>
                    
                    {{-- 2. TIPO DE CONTENIDO (NUEVO CAMPO) --}}
                    <div class="mt-4">
                        <x-input-label for="content_type" :value="__('Tipo de Contenido')" />
                        <select id="content_type" name="content_type" onchange="toggleContentFields(this.value)" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="link" @selected(old('content_type') == 'link')>Enlace Externo (URL)</option>
                            <option value="video" @selected(old('content_type') == 'video')>Video (Subir archivo)</option>
                            <option value="document" @selected(old('content_type') == 'document')>Documento (Subir archivo)</option>
                        </select>
                        <x-input-error :messages="$errors->get('content_type')" class="mt-2" />
                    </div>

                    {{-- 3. CAMPO CONDICIONAL: URL (Por defecto visible) --}}
                    <div id="url_field" class="mt-4">
                        <x-input-label for="content_url" :value="__('URL del Contenido (Ej: YouTube, Vimeo)')" />
                        <x-text-input id="content_url" class="block mt-1 w-full" type="url" name="content_url" :value="old('content_url')" placeholder="https://youtube.com/link-a-video" />
                        <x-input-error :messages="$errors->get('content_url')" class="mt-2" />
                    </div>
                    
                    {{-- 4. CAMPO CONDICIONAL: SUBIDA DE ARCHIVO (Por defecto oculto) --}}
                    <div id="file_field" class="mt-4" style="display: {{ old('content_type') && old('content_type') !== 'link' ? 'block' : 'none' }};">
                        <x-input-label for="media_file" :value="__('Subir Archivo de Contenido (Video/Documento)')" />
                        <input id="media_file" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" type="file" name="media_file" />
                        <p class="text-sm text-gray-500 mt-1">Formatos permitidos: MP4, PDF, DOC, DOCX. Máx: 50MB.</p>
                        <x-input-error :messages="$errors->get('media_file')" class="mt-2" />
                    </div>
                    
                    {{-- 5. ORDEN DE SECUENCIA --}}
                    <div class="mt-4">
                        <x-input-label for="sequence_order" :value="__('Orden de Secuencia')" />
                        <x-text-input id="sequence_order" class="block mt-1 w-full" type="number" name="sequence_order" :value="old('sequence_order', $course->modules->max('sequence_order') + 1)" required min="1" />
                        <x-input-error :messages="$errors->get('sequence_order')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('seller.courses.edit', $course) }}" class="text-gray-600 hover:text-gray-900 mr-4">
                            {{ __('Cancelar') }}
                        </a>
                        <x-primary-button>
                            {{ __('Guardar Módulo') }}
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- Script JavaScript para alternar campos --}}
    <script>
        function toggleContentFields(type) {
            const urlField = document.getElementById('url_field');
            const fileField = document.getElementById('file_field');
            
            if (type === 'link') {
                urlField.style.display = 'block';
                fileField.style.display = 'none';
                // Hacer que la URL sea requerida si es link, y el archivo no
                document.getElementById('content_url').setAttribute('required', 'required');
                document.getElementById('media_file').removeAttribute('required');
            } else {
                urlField.style.display = 'none';
                fileField.style.display = 'block';
                // Hacer que el archivo sea requerido si es video/doc, y la URL no
                document.getElementById('media_file').setAttribute('required', 'required');
                document.getElementById('content_url').removeAttribute('required');
            }
        }
        // Inicializar al cargar la página si hay datos antiguos (old('content_type'))
        document.addEventListener('DOMContentLoaded', () => {
            const initialType = document.getElementById('content_type').value;
            toggleContentFields(initialType);
        });
    </script>
</x-app-layout>