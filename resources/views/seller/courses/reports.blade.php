<x-app-layout>
<x-slot name="header">
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
{{ __('Reportes de Ventas') }}
</h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

        <!-- Totales Globales -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6 border-l-4 border-indigo-500">
                <p class="text-sm font-medium text-gray-500">Ingresos Totales Generados</p>
                <p class="text-3xl font-bold text-indigo-600 mt-1">${{ number_format($globalTotals['total_revenue'], 2) }}</p>
            </div>
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6 border-l-4 border-green-500">
                <p class="text-sm font-medium text-gray-500">Ventas Totales (Matrículas)</p>
                <p class="text-3xl font-bold text-green-600 mt-1">{{ $globalTotals['total_enrollments'] }}</p>
            </div>
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6 border-l-4 border-yellow-500">
                <p class="text-sm font-medium text-gray-500">Cursos Publicados</p>
                <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $globalTotals['total_courses'] }}</p>
            </div>
        </div>
        
        <!-- Reporte Detallado por Curso -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 lg:p-8">
                <h3 class="text-xl font-semibold text-gray-800 mb-6">Rendimiento por Curso</h3>

                @if ($reports->isEmpty())
                    <p class="text-gray-500">{{ __('Aún no tienes cursos con ventas registradas.') }}</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Curso
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Precio
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Estado
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        # Ventas
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ingresos Generados
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Ver</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($reports as $report)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $report['title'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            ${{ number_format($report['price'], 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @php
                                                $color = $report['status'] === 'Publicado' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                                {{ $report['status'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-bold">
                                            {{ $report['enrollment_count'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600 font-bold">
                                            ${{ number_format($report['total_revenue'], 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('seller.courses.edit', $report['course_id']) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
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