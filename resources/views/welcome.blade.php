<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Documentos V/C</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900 flex items-center justify-center min-h-screen">

    <div class="max-w-xl w-full mx-auto p-6 bg-white shadow-md rounded-lg text-center">
        
        <!-- Indicador del Owner Activo (Arriba siempre) -->
        <div class="mb-6 p-4 bg-indigo-50 border border-indigo-200 rounded-md flex items-center justify-between">
            <div class="text-left">
                <span class="text-xs font-semibold uppercase tracking-wider text-indigo-600 block">Owner Activo</span>
                <span class="text-sm font-bold text-gray-800">
                    @if($activeOwner)
                        {{ $activeOwner->name }} <span class="text-gray-500 font-normal">({{ $activeOwner->rut }})</span>
                    @else
                        <span class="text-amber-600 font-medium">Ningún owner seleccionado</span>
                    @endif
                </span>
            </div>
            <div>
                <a href="{{ route('owners.index') }}" class="text-xs bg-indigo-600 text-white px-3 py-1.5 rounded hover:bg-indigo-700 transition">
                    Cambiar
                </a>
            </div>
        </div>

        <!-- Alerta / Aviso de Documentos Pendientes de Contabilizar -->
        @if(isset($pendingCount) && $pendingCount > 0)
            <div class="mb-6 p-3 bg-amber-50 border border-amber-200 rounded-md flex items-center justify-between text-amber-800">
                <div class="text-left text-sm">
                    <span class="font-semibold block">¡Atención!</span>
                    Tienes <span class="font-bold">{{ $pendingCount }}</span> documento(s) sin contabilizar.
                </div>
                <div>
                    <a href="{{ route('vc_documents.pending') }}" class="text-xs bg-amber-600 text-white px-3 py-1.5 rounded hover:bg-amber-700 transition font-medium whitespace-nowrap">
                        Contabilizar
                    </a>
                </div>
            </div>
        @else
            <div class="mb-6 p-3 bg-green-50 border border-green-200 rounded-md text-green-800 text-xs text-left">
                ✓ Todos los documentos se encuentran contabilizados.
            </div>
        @endif

        <h1 class="text-2xl font-bold text-gray-800 mb-2">Sistema de Documentos V/C</h1>
        <p class="text-gray-600 mb-6">Gestión de registros de ventas y compras</p>

        <div class="space-y-4">
            <a href="{{ route('vc_documents.create') }}" class="block w-full bg-indigo-600 text-white font-medium py-3 px-4 rounded-md shadow hover:bg-indigo-700 transition">
                Ingresar Nuevo Documento V/C
            </a>
        </div>
        
        <div class="space-y-4 mt-4">
            <a href="{{ route('vc_documents.upload') }}" class="block w-full bg-indigo-600 text-white font-medium py-3 px-4 rounded-md shadow hover:bg-indigo-700 transition">
                Ingresar vía importador
            </a>
        </div>

        <div class="mt-8 pt-4 border-t border-gray-200 text-xs text-gray-500 flex justify-between items-center">
            <p>Sistema en proceso de desarrollo</p>
            <a href="{{ route('owners.index') }}" class="text-indigo-600 hover:underline">Gestionar Owners</a>
        </div>
        <div class="space-y-4 mt-4">
            <a href="{{ route('vc_documents.journal_book') }}" class="block w-full bg-emerald-600 text-white font-medium py-3 px-4 rounded-md shadow hover:bg-emerald-700 transition">
                Ver Libro Diario
            </a>
        </div>
    </div>

</body>
</html>