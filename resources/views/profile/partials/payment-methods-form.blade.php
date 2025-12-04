<section>
<header>
<h2 class="text-lg font-medium text-gray-900">
{{ __('Métodos de Pago Guardados') }}
</h2>

    <p class="mt-1 text-sm text-gray-600">
        {{ __('Administra tus tarjetas guardadas para compras rápidas.') }}
    </p>
</header>

@if ($paymentMethods->count())
    <div class="mt-6 space-y-4">
        @foreach ($paymentMethods as $method)
            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg {{ $method->is_default ? 'bg-indigo-50 border-indigo-400 shadow-sm' : 'bg-white' }}">
                
                <!-- Detalles de la Tarjeta -->
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 {{ $method->is_default ? 'text-indigo-500' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <div class="ml-3">
                        <p class="font-semibold text-gray-800">
                            {{ $method->brand }} terminada en {{ $method->last_four }}
                            @if ($method->is_default)
                                <span class="ml-2 text-xs font-medium text-white bg-indigo-600 px-2 py-0.5 rounded-full">{{ __('Predeterminada') }}</span>
                            @endif
                        </p>
                        <p class="text-sm text-gray-500">
                            Expira: {{ $method->expiration_date }} | Titular: {{ $method->card_holder_name }}
                        </p>
                    </div>
                </div>
                
                <!-- Acciones -->
                <div class="flex items-center space-x-3">
                    @unless ($method->is_default)
                        <form method="POST" action="{{ route('payment-methods.set-default', $method) }}">
                            @csrf
                            @method('PATCH')
                            <x-primary-button class="text-xs">
                                {{ __('Establecer como Predeterminada') }}
                            </x-primary-button>
                        </form>
                    @endunless
                    
                    <form method="POST" action="{{ route('payment-methods.destroy', $method) }}" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este método de pago?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 hover:text-red-900">
                            {{ __('Eliminar') }}
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@else
    <p class="mt-4 text-sm text-gray-500">{{ __('No tienes métodos de pago guardados.') }}</p>
@endif

<div class="mt-8 border-t pt-6">
    <h3 class="text-md font-semibold text-gray-900 mb-4">{{ __('Agregar Nuevo Método de Pago') }}</h3>
    
    <!-- Formulario para agregar una nueva tarjeta -->
    <form method="post" action="{{ route('payment-methods.store') }}" class="mt-4 space-y-6">
        @csrf
        
        <!-- Nombre del Titular -->
        <div>
            <x-input-label for="card_holder_name" :value="__('Nombre del Titular')" />
            <x-text-input id="card_holder_name" name="card_holder_name" type="text" class="mt-1 block w-full" required autocomplete="cc-name" />
            <x-input-error class="mt-2" :messages="$errors->get('card_holder_name')" />
        </div>

        <!-- Número de Tarjeta -->
        <div>
            <x-input-label for="card_number" :value="__('Número de Tarjeta')" />
            <x-text-input id="card_number" name="card_number" type="text" class="mt-1 block w-full" pattern="\d{16}" maxlength="16" required placeholder="XXXX XXXX XXXX XXXX" inputmode="numeric" />
            <x-input-error class="mt-2" :messages="$errors->get('card_number')" />
        </div>

        <!-- Expiración y CVC -->
        <div class="grid grid-cols-3 gap-4">
            <div>
                <x-input-label for="expiry_month" :value="__('Mes Exp.')" />
                <x-text-input id="expiry_month" name="expiry_month" type="number" class="mt-1 block w-full" required min="1" max="12" placeholder="MM" />
                <x-input-error class="mt-2" :messages="$errors->get('expiry_month')" />
            </div>
            <div>
                <x-input-label for="expiry_year" :value="__('Año Exp.')" />
                <x-text-input id="expiry_year" name="expiry_year" type="number" class="mt-1 block w-full" required min="{{ date('Y') }}" max="{{ date('Y') + 10 }}" placeholder="YYYY" />
                <x-input-error class="mt-2" :messages="$errors->get('expiry_year')" />
            </div>
            <div>
                <x-input-label for="cvc" :value="__('CVC')" />
                <x-text-input id="cvc" name="cvc" type="text" class="mt-1 block w-full" pattern="\d{3}" maxlength="3" required placeholder="CVC" inputmode="numeric" />
                <x-input-error class="mt-2" :messages="$errors->get('cvc')" />
            </div>
        </div>
        
        <!-- Checkbox Default -->
        <div class="flex items-center">
            <input id="is_default" name="is_default" type="checkbox" value="1" checked class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
            <x-input-label for="is_default" class="ml-2" :value="__('Establecer como tarjeta predeterminada')" />
        </div>
        

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Guardar Tarjeta') }}</x-primary-button>

            @if (session('status') === 'Método de pago agregado exitosamente.')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-gray-600">{{ __('Guardado.') }}</p>
            @endif
        </div>
    </form>
</div>


</section>