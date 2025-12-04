<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Módulo: ') . $module->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl sm:rounded-lg p-6">
                
                {{-- CAMBIO CLAVE: Añadir enctype para subir archivos --}}
                <form method="POST" action="{{ route('seller.modules.update', ['course' => $course->id, 'module' => $module->id]) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT') {{-- Usamos PUT para la actualización --}}

                    {{-- 1. TÍTULO --}}
                    <div>
                        <x-input-label for="title" :value="__('Título de la Lección/Módulo')" />
                        <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $module->title)" required autofocus />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    {{-- 2. TIPO DE CONTENIDO --}}
                    <div class="mt-4">
                        <x-input-label for="content_type" :value="__('Tipo de Contenido')" />
                        <select id="content_type" name="content_type" onchange="toggleContentFields(this.value)" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            {{-- Usamos $module->content_type para seleccionar el valor actual --}}
                            <option value="link" @selected(old('content_type', $module->content_type) == 'link')>Enlace Externo (URL)</option>
                            <option value="video" @selected(old('content_type', $module->content_type) == 'video')>Video (Subir archivo)</option>
                            <option value="document" @selected(old('content_type', $module->content_type) == 'document')>Documento (Subir archivo)</option>
                        </select>
                        <x-input-error :messages="$errors->get('content_type')" class="mt-2" />
                    </div>

                    {{-- 3. CAMPO CONDICIONAL: URL --}}
                    <div id="url_field" class="mt-4">
                        <x-input-label for="content_url" :value="__('URL del Contenido (Ej: YouTube, Vimeo)')" />
                        <x-text-input id="content_url" class="block mt-1 w-full" type="url" name="content_url" :value="old('content_url', $module->content_url)" placeholder="https://youtube.com/link-a-video" />
                        <x-input-error :messages="$errors->get('content_url')" class="mt-2" />
                    </div>
                    
                    {{-- 4. CAMPO CONDICIONAL: SUBIDA DE ARCHIVO --}}
                    {{-- Usamos una lógica más robusta para ocultar/mostrar y manejar archivos existentes --}}
                    <div id="file_field" class="mt-4">
                        <x-input-label for="media_file" :value="__('Subir Archivo de Contenido (Deja vacío para mantener el actual)')" />
                        <input id="media_file" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" type="file" name="media_file" />
                        
                        {{-- Indicador de archivo actual --}}
                        @if ($module->content_path)
                            <p class="text-sm text-gray-500 mt-1">Archivo actual: **{{ basename($module->content_path) }}**. Sube uno nuevo para reemplazarlo.</p>
                        @else
                            <p class="text-sm text-gray-500 mt-1">Formatos permitidos: MP4, PDF, DOC, DOCX. Máx: 50MB.</p>
                        @endif
                        
                        <x-input-error :messages="$errors->get('media_file')" class="mt-2" />
                    </div>
                    
                    {{-- 5. ORDEN DE SECUENCIA --}}
                    <div class="mt-4">
                        <x-input-label for="sequence_order" :value="__('Orden de Secuencia')" />
                        <x-text-input id="sequence_order" class="block mt-1 w-full" type="number" name="sequence_order" :value="old('sequence_order', $module->sequence_order)" required min="1" />
                        <x-input-error :messages="$errors->get('sequence_order')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('seller.courses.edit', $course) }}" class="text-gray-600 hover:text-gray-900 mr-4">
                            {{ __('Cancelar') }}
                        </a>
                        <x-primary-button>
                            {{ __('Guardar Cambios') }}
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
            const mediaFileElement = document.getElementById('media_file');
            
            if (type === 'link') {
                urlField.style.display = 'block';
                fileField.style.display = 'none';
                
                // Si es link, la URL es requerida; el archivo no es requerido.
                document.getElementById('content_url').setAttribute('required', 'required');
                mediaFileElement.removeAttribute('required');
                
            } else {
                urlField.style.display = 'none';
                fileField.style.display = 'block';
                
                // Si es video/doc, el archivo es requerido SOLO si no hay ya un content_path.
                // Ya que estamos editando, si ya hay un content_path, puede ser opcional (el usuario podría querer mantener el archivo actual).
                
                // Simplificamos: si ya hay un path, no es requerido, si no hay, se requiere.
                const currentContentPath = '{{ $module->content_path ?? '' }}';

                if (currentContentPath === '') {
                    mediaFileElement.setAttribute('required', 'required');
                } else {
                    mediaFileElement.removeAttribute('required');
                }
                
                document.getElementById('content_url').removeAttribute('required');
            }
        }
        // Inicializar al cargar la página
        document.addEventListener('DOMContentLoaded', () => {
            const initialType = document.getElementById('content_type').value;
            toggleContentFields(initialType);
        });
    </script>
</x-app-layout>