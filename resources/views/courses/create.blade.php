<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Agendar Nuevo Curso') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form method="POST" action="{{ route('seller.courses.store') }}">
                        @csrf

                        <div>
                            <x-input-label for="title" :value="__('Título del Curso')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="header" :value="__('Encabezado/Subtítulo')" />
                            <x-text-input id="header" class="block mt-1 w-full" type="text" name="header" :value="old('header')" required />
                            <x-input-error :messages="$errors->get('header')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="scheduled_date" :value="__('Fecha y Hora Tentativa de la Clase')" />
                            <x-text-input id="scheduled_date" class="block mt-1 w-full" type="datetime-local" name="scheduled_date" :value="old('scheduled_date')" required />
                            <x-input-error :messages="$errors->get('scheduled_date')" class="mt-2" />
                        </div>
                        
                        <div class="mt-4">
                            <x-input-label for="price" :value="__('Precio (USD)')" />
                            <x-text-input id="price" class="block mt-1 w-full" type="number" step="0.01" name="price" :value="old('price', 0.00)" required />
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Descripción del Curso')" />
                            <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>


                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Agendar Curso') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>