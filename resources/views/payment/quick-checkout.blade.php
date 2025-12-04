<x-app-layout>
<x-slot name="header">
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
{{ __('Confirmación de Compra Rápida') }}
</h2>
</x-slot>

<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 lg:p-8">
            
            <h3 class="text-2xl font-bold text-gray-800 mb-6">{{ $course->title }}</h3>
            
            <!-- Detalles del Curso -->
            <div class="mb-8 p-4 bg-indigo-50 border-l-4 border-indigo-400">
                <p class="text-lg font-medium text-gray-700">Precio Total:</p>
                <p class="text-4xl font-extrabold text-indigo-600">${{ number_format($course->price, 2) }} USD</p>
            </div>
            
            <!-- Método de Pago Predeterminado -->
            <div class="mb-8 border-b pb-6">
                <p class="text-lg font-medium text-gray-700 mb-3">Pagar con Método Predeterminado:</p>
                <div class="flex items-center p-4 border border-indigo-400 rounded-lg bg-white shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <div class="ml-3">
                        <p class="font-semibold text-gray-800">
                            {{ $defaultPaymentMethod->brand }} terminada en {{ $defaultPaymentMethod->last_four }}
                            <span class="ml-2 text-xs font-medium text-white bg-indigo-600 px-2 py-0.5 rounded-full">Predeterminada</span>
                        </p>
                        <p class="text-sm text-gray-500">
                            Expira: {{ $defaultPaymentMethod->expiration_date }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Botón de Confirmación -->
            <form method="POST" action="{{ route('payment.process', $course) }}" class="mt-6">
                @csrf
                <!-- Campo oculto para indicar qué método de pago usar -->
                <input type="hidden" name="payment_method_id" value="{{ $defaultPaymentMethod->id }}">
                
                <x-primary-button class="w-full justify-center py-3 text-lg bg-green-600 hover:bg-green-700">
                    {{ __('Confirmar Compra por $') }}{{ number_format($course->price, 2) }}
                </x-primary-button>
            </form>
            
            <!-- Enlace para usar otra tarjeta -->
            <div class="mt-4 text-center">
                <a href="{{ route('payment.checkout', $course) }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                    {{ __('Usar otra tarjeta o registrar una nueva') }}
                </a>
            </div>
            
        </div>
    </div>
</div>


</x-app-layout>