<x-app-layout>
<x-slot name="header">
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
{{ __('Historial de Compras') }}
</h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        <!-- Pestañas de Navegación -->
        <div class="bg-white shadow sm:rounded-lg overflow-hidden">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-4 sm:px-6 lg:px-8" aria-label="Tabs">
                    <a href="{{ route('profile.edit') }}" class="py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        {{ __('Información General') }}
                    </a>
                    <!-- Esta pestaña se ve activa porque la ruta actual es profile.payments -->
                    <a href="{{ route('profile.payments') }}" class="py-4 px-1 border-b-2 font-medium text-sm border-indigo-500 text-indigo-600">
                        {{ __('Historial de Compras') }}
                    </a>
                </nav>
            </div>
        </div>

        <!-- Contenido del Historial -->
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="w-full">
                <h3 class="text-xl font-semibold text-gray-900 mb-6">Mis Transacciones</h3>

                @if ($payments->isEmpty())
                    <div class="text-center p-10 bg-gray-50 rounded-lg">
                        <p class="text-gray-500">{{ __('Aún no has realizado ninguna compra de cursos.') }}</p>
                        <a href="{{ route('courses.index') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Explorar Cursos
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Curso
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Fecha
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Monto
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Estado
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Método
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID Transacción
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($payments as $payment)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <a href="{{ $payment->course ? route('courses.show', $payment->course) : '#' }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ $payment->course->title ?? 'Curso Eliminado' }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $payment->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-semibold">
                                            ${{ number_format($payment->amount, 2) }} {{ $payment->currency }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @php
                                                $color = match($payment->status) {
                                                    'succeeded' => 'bg-green-100 text-green-800',
                                                    'failed' => 'bg-red-100 text-red-800',
                                                    default => 'bg-yellow-100 text-yellow-800',
                                                };
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $payment->payment_method }} 
                                            @if (isset($payment->payment_details['last_four']))
                                                (****{{ $payment->payment_details['last_four'] }})
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $payment->transaction_id }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
</x-app-layout>