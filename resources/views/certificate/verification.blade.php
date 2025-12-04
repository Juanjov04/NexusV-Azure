<x-guest-layout>
    <div class="max-w-4xl mx-auto p-6 bg-white shadow-lg rounded-lg text-center">
        <h1 class="text-3xl font-bold text-green-600 mb-4">✅ Certificado Verificado</h1>
        <p class="text-gray-700 mb-6">Este certificado es auténtico y fue emitido por **{{ $enrollment->course->user->name }}** a través de NexusV-V2.</p>

        <div class="grid grid-cols-2 gap-4 text-left border-t pt-4">
            <p><strong>Estudiante:</strong> {{ $enrollment->user->name }}</p>
            <p><strong>Curso:</strong> {{ $enrollment->course->title }}</p>
            <p><strong>Fecha de Finalización:</strong> {{ $enrollment->completed_at->format('d/m/Y') }}</p>
            <p><strong>ID de Verificación:</strong> {{ $enrollment->certificate_uuid }}</p>
        </div>

        <a href="{{ route('login') }}" class="mt-6 inline-block bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Volver a NexusV-V2</a>
    </div>
</x-guest-layout>