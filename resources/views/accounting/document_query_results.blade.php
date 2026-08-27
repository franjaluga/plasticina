<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro de Documentos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-black font-sans p-6 max-w-[95%] mx-auto">

    <!-- Cabecera del Libro -->
    <div class="flex justify-between items-baseline pb-3 mb-4 border-b border-gray-300">
        <div>
            <h1 class="text-base font-bold uppercase tracking-wider">
                Registro de {{ $typeVc === 'V' ? 'Ventas' : 'Compras' }}
            </h1>
            <p class="text-xs text-gray-600 font-mono mt-0.5">
                Periodo: {{ str_pad($month, 2, '0', STR_PAD_LEFT) }} / {{ $year }}
            </p>
        </div>
        <div>
            <a href="javascript:history.back()" class="text-xs font-medium text-black border border-gray-300 px-2.5 py-1 rounded">
                &larr; Volver
            </a>
        </div>
    </div>

    <!-- Tabla Normal -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="border-b-2 border-black text-[11px] font-bold uppercase text-gray-700">
                    <th class="py-2.5 px-3">Folio</th>
                    <th class="py-2.5 px-3">Fecha</th>
                    <th class="py-2.5 px-3">RUT / Entidad</th>
                    <th class="py-2.5 px-3">Documento</th>
                    <th class="py-2.5 px-3 text-right">Neto</th>
                    <th class="py-2.5 px-3 text-right">Exento</th>
                    <th class="py-2.5 px-3 text-right">IVA</th>
                    <th class="py-2.5 px-3 text-right">Imp. (+)</th>
                    <th class="py-2.5 px-3 text-right">Imp. (-)</th>
                    <th class="py-2.5 px-3 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="font-mono text-[11px] divide-y divide-gray-200">
                @forelse($documents as $doc)
                    <tr>
                        <td class="py-2 px-3 font-bold">
                            <a href="{{ route('accounting.documents.detail', $doc->id) }}" class="underline">
                                {{ $doc->folio }}
                            </a>
                        </td>
                        <td class="py-2 px-3 text-gray-600 font-sans whitespace-nowrap">{{ $doc->date }}</td>
                        <td class="py-2 px-3 text-gray-800">
                            <span class="font-bold">{{ $doc->entity->rut ?? 'N/A' }}</span>
                            <span class="block text-[10px] text-gray-500 font-sans truncate max-w-[160px]">{{ $doc->entity->name ?? 'Sin nombre' }}</span>
                        </td>
                        <td class="py-2 px-3 text-gray-800 font-sans">
                            <span class="font-bold">{{ $doc->document_type_id }}</span> 
                            <span class="text-gray-500 text-[10px] truncate block max-w-[130px]">{{ $doc->documentType->name ?? $doc->documentType->description ?? '' }}</span>
                        </td>
                        <td class="py-2 px-3 text-right">{{ number_format($doc->net, 0, ',', '.') }}</td>
                        <td class="py-2 px-3 text-right text-gray-500">{{ number_format($doc->exempt, 0, ',', '.') }}</td>
                        <td class="py-2 px-3 text-right">{{ number_format($doc->vat_rec, 0, ',', '.') }}</td>
                        <td class="py-2 px-3 text-right text-gray-500">{{ number_format($doc->plus_oth_tax, 0, ',', '.') }}</td>
                        <td class="py-2 px-3 text-right text-gray-500">{{ number_format($doc->minus_oth_tax, 0, ',', '.') }}</td>
                        <td class="py-2 px-3 text-right font-bold">{{ number_format($doc->total, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-8 text-gray-400 font-sans">
                            No se encontraron registros para este periodo.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            
            @if($documents->isNotEmpty())
                <!-- Fila de Totales -->
                <tfoot>
                    <tr class="border-t-2 border-black font-bold font-mono text-[11px] text-black">
                        <td colspan="4" class="py-3 px-3 text-right uppercase font-sans">TOTALES:</td>
                        <td class="py-3 px-3 text-right">{{ number_format($documents->sum('net'), 0, ',', '.') }}</td>
                        <td class="py-3 px-3 text-right text-gray-600">{{ number_format($documents->sum('exempt'), 0, ',', '.') }}</td>
                        <td class="py-3 px-3 text-right">{{ number_format($documents->sum('vat_rec'), 0, ',', '.') }}</td>
                        <td class="py-3 px-3 text-right text-gray-600">{{ number_format($documents->sum('plus_oth_tax'), 0, ',', '.') }}</td>
                        <td class="py-3 px-3 text-right text-gray-600">{{ number_format($documents->sum('minus_oth_tax'), 0, ',', '.') }}</td>
                        <td class="py-3 px-3 text-right">{{ number_format($documents->sum('total'), 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

</body>
</html>