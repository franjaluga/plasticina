<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resultado de Consulta de Documentos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-gray-900 font-sans p-8 max-w-5xl mx-auto">

    <!-- Cabecera -->
    <div class="flex justify-between items-baseline border-b border-gray-300 pb-4 mb-6">
        <div>
            <h1 class="text-xl font-semibold tracking-tight">
                Consulta de {{ $typeVc === 'V' ? 'Ventas' : 'Compras' }}
            </h1>
            <p class="text-xs text-gray-500">
                Periodo: <span class="font-bold text-indigo-600">{{ $month }} / {{ $year }}</span>
            </p>
        </div>
        <div>
            <a href="javascript:history.back()" class="text-xs font-semibold bg-slate-100 text-slate-700 px-3 py-1.5 rounded-lg hover:bg-slate-200 transition">
                &larr; Volver
            </a>
        </div>
    </div>

    <!-- Tabla de Resultados -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="border-b border-gray-900 text-xs text-gray-500 uppercase">
                    <th class="py-2 pr-4">Folio</th>
                    <th class="py-2 px-4">Fecha</th>
                    <th class="py-2 px-4">RUT / Entidad</th>
                    <th class="py-2 px-4">Documento</th>
                    <th class="py-2 px-4 text-right">Neto</th>
                    <th class="py-2 px-4 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-xs font-mono">
                @forelse($documents as $doc)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 pr-4 font-bold text-indigo-600">
                            <a href="{{ route('accounting.documents.detail', $doc->id) }}" class="hover:underline">
                                {{ $doc->folio }}
                            </a>
                        </td>
                        <td class="py-3 px-4 text-gray-600 font-sans">{{ $doc->date }}</td>
                        <td class="py-3 px-4 text-gray-800">
                            <!-- Accedemos a la relación entity -->
                            <span class="font-bold">{{ $doc->entity->rut ?? 'N/A' }}</span><br>
                            <span class="text-gray-500 text-[11px] font-sans">{{ $doc->entity->name ?? 'Sin nombre registrado' }}</span>
                        </td>
                        <td class="py-3 px-4 text-gray-800 font-sans">
                            <!-- Accedemos a la relación documentType y mostramos su código y nombre/descripción -->
                            <span class="font-bold">{{ $doc->document_type_id }}</span> - 
                            <span>{{ $doc->documentType->name ?? $doc->documentType->description ?? 'Documento' }}</span>
                        </td>
                        <td class="py-3 px-4 text-right">{{ number_format($doc->net, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-right font-bold">{{ number_format($doc->total, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-10 text-gray-400 font-sans">
                            No se encontraron registros para este periodo y tipo de documento.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</body>
</html>