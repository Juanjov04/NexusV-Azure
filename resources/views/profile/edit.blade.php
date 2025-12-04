<x-app-layout>
<x-slot name="header">
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
{{ __('Profile') }}
</h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        <!-- Pestañas de Navegación -->
        <div class="bg-white shadow sm:rounded-lg overflow-hidden">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-4 sm:px-6 lg:px-8" aria-label="Tabs">
                    <!-- Pestaña 1: Información General -->
                    <a href="{{ route('profile.edit') }}" class="py-4 px-1 border-b-2 font-medium text-sm @if(Route::currentRouteName() === 'profile.edit') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif">
                        {{ __('Información General') }}
                    </a>
                    <!-- Pestaña 2: Métodos de Pago -->
                    <a href="#payment-methods" class="py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        {{ __('Métodos de Pago') }}
                    </a>
                    <!-- Pestaña 3: Historial de Compras -->
                    <a href="{{ route('profile.payments') }}" class="py-4 px-1 border-b-2 font-medium text-sm @if(Route::currentRouteName() === 'profile.payments') border-indigo-500 text-indigo-600 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif">
                        {{ __('Historial de Compras') }}
                    </a>
                </nav>
            </div>
        </div>


        <!-- Contenido de las Formas de Perfil -->
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>
        
        <!-- NUEVA SECCIÓN: Métodos de Pago -->
        <div id="payment-methods" class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-4xl">
                @include('profile.partials.payment-methods-form', ['paymentMethods' => $user->paymentMethods])
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>


</x-app-layout>