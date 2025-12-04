<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Finalizar Compra') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row gap-6">
                
                {{-- Resumen del Pedido --}}
                <div class="w-full md:w-1/3 order-2 md:order-1">
                    <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Resumen</h3>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Curso:</span>
                            <span class="font-medium text-right">{{ $course->title }}</span>
                        </div>
                        <div class="flex justify-between mb-4 border-b pb-4">
                            <span class="text-gray-600">Instructor:</span>
                            <span class="text-right">{{ $course->user->name }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xl font-bold text-gray-900">
                            <span>Total:</span>
                            <span>${{ number_format($course->price, 2) }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-4 text-center">
                        <p class="text-xs text-gray-500">🔒 Pago seguro SSL simulado</p>
                    </div>
                </div>

                {{-- Formulario de Tarjeta Simulado --}}
                <div class="w-full md:w-2/3 order-1 md:order-2">
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <h3 class="text-lg font-bold text-gray-900 mb-6">Detalles del Pago (Tarjeta de Crédito)</h3>

                        <form method="POST" action="{{ route('payment.process', $course) }}">
                            @csrf

                            {{-- Titular --}}
                            <div class="mb-4">
                                <x-input-label for="card_holder" :value="__('Nombre del Titular')" />
                                <x-text-input id="card_holder" class="block mt-1 w-full" type="text" name="card_holder" placeholder="Como aparece en la tarjeta" required />
                                <x-input-error :messages="$errors->get('card_holder')" class="mt-2" />
                            </div>

                            {{-- Número de Tarjeta --}}
                            <div class="mb-4">
                                <x-input-label for="card_number" :value="__('Número de Tarjeta')" />
                                <div class="relative">
                                    <x-text-input id="card_number" class="block mt-1 w-full pl-10" type="text" name="card_number" placeholder="0000 0000 0000 0000" maxlength="16" required />
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                    </div>
                                </div>
                                <x-input-error :messages="$errors->get('card_number')" class="mt-2" />
                            </div>

                            <div class="flex gap-4 mb-6">
                                {{-- Fecha Expiración --}}
                                <div class="w-1/2">
                                    <x-input-label for="expiry_date" :value="__('Fecha Exp (MM/YY)')" />
                                    <x-text-input id="expiry_date" class="block mt-1 w-full" type="text" name="expiry_date" placeholder="MM/YY" required />
                                    <x-input-error :messages="$errors->get('expiry_date')" class="mt-2" />
                                </div>
                                
                                {{-- CVC --}}
                                <div class="w-1/2">
                                    <x-input-label for="cvc" :value="__('CVC')" />
                                    <x-text-input id="cvc" class="block mt-1 w-full" type="text" name="cvc" placeholder="123" maxlength="3" required />
                                    <x-input-error :messages="$errors->get('cvc')" class="mt-2" />
                                </div>
                            </div>

                            <x-primary-button class="w-full justify-center py-3 text-lg">
                                {{ __('Pagar Now') }} ${{ number_format($course->price, 2) }}
                            </x-primary-button>
                            
                            <div class="mt-4 text-center">
                                <a href="{{ route('courses.show', $course) }}" class="text-sm text-gray-600 hover:text-gray-900 underline">Cancelar y volver</a>
                            </div>

                        </form>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</x-app-layout>