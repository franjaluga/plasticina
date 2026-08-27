<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Comprobante de Asiento Contable N° {{ $journal->entry_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased min-h-screen py-10">

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Cabecera principal -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-gray-200 pb-4 mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Comprobante de Asiento Contable</h1>
                <p class="text-xs text-gray-500 mt-1">
                    Asiento N°: <span class="font-bold text-indigo-600">{{ $journal->entry_number }}</span> 
                    <span class="mx-1 text-gray-300">|</span> 
                    Año: <span class="font-bold text-gray-800">{{ $journal->year }}</span>
                </p>

                <!-- GLOSA / DESCRIPCIÓN AMPLIADA (Busca en el asiento o en el documento asociado) -->
                @php
                    $glosaAsiento = $journal->description ?? $journal->glosa ?? null;
                    $glosaDocumento = $journal->document->description ?? $journal->document->glosa ?? $journal->document->obs ?? null;
                    $glosaFinal = $glosaAsiento ?: $glosaDocumento;
                @endphp

                @if(!empty($glosaFinal))
                    <div class="mt-2.5 inline-flex items-center gap-2 bg-indigo-50 border border-indigo-200 text-indigo-900 px-3 py-1.5 rounded-lg text-xs font-medium">
                        <span class="font-bold uppercase tracking-wider text-[10px] text-indigo-700 bg-indigo-100 px-1.5 py-0.5 rounded">Glosa</span>
                        <span>{{ $glosaFinal }}</span>
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-2">
                <!-- Botón para volver al listado -->
                <a href="{{ route('accounting.system_journals') }}" class="inline-flex items-center text-xs font-semibold bg-white border border-gray-300 text-gray-700 px-3.5 py-2 rounded-lg shadow-sm hover:bg-gray-50 transition">
                    &larr; Volver al Listado
                </a>

                <!-- Botón para eliminar el asiento -->
                <form action="{{ route('accounting.audit.destroy', $journal->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este asiento y su documento asociado?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center text-xs font-semibold bg-red-600 text-white px-3.5 py-2 rounded-lg shadow-sm hover:bg-red-700 transition">
                        Eliminar Asiento
                    </button>
                </form>
            </div>
        </div>

        <!-- Mensajes de éxito o error -->
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 text-xs rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 text-xs rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Tarjeta con información general del Asiento -->
        <div class="bg-white shadow-sm border border-gray-200 rounded-xl p-5 mb-6 text-xs space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <span class="font-bold text-gray-500 uppercase tracking-wider block text-[10px]">Fecha del Asiento</span>
                    <span class="text-gray-800 font-medium text-sm">{{ $journal->date }}</span>
                </div>
                <div>
                    <span class="font-bold text-gray-500 uppercase tracking-wider block text-[10px]">Tipo de Origen</span>
                    <span class="text-gray-800 font-medium">
                        {{ $journal->vc_document_id ? 'Documento V/C (ID: ' . $journal->vc_document_id . ')' : 'Asiento Manual / Pago' }}
                    </span>
                </div>
                <div>
                    <span class="font-bold text-gray-500 uppercase tracking-wider block text-[10px]">Estado</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-semibold {{ $journal->is_balanced ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $journal->is_balanced ? '● Cuadrado' : '● Descuadrado' }}
                    </span>
                </div>
            </div>

            @if($journal->document && $journal->document->entity)
                <div class="pt-3 border-t border-gray-100">
                    <span class="font-bold text-gray-500 uppercase tracking-wider block text-[10px]">Entidad Asociada</span>
                    <span class="text-gray-800 font-medium">{{ $journal->document->entity->name }} (RUT: {{ $journal->document->entity->rut }})</span>
                </div>
            @endif
        </div>

        <!-- Tabla de Líneas de Detalle (Debe / Haber) -->
        <div class="bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-800 text-white uppercase tracking-wider text-[11px]">
                            <th class="py-3 px-4 w-36">Código Cuenta</th>
                            <th class="py-3 px-4">Nombre de la Cuenta</th>
                            <th class="py-3 px-4 w-32">Componente</th>
                            <th class="py-3 px-4 text-right w-32">Debe</th>
                            <th class="py-3 px-4 text-right w-32">Haber</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($journal->entries as $entry)
                            <tr class="hover:bg-gray-50/80 transition">
                                <td class="py-2.5 px-4 font-mono font-bold text-gray-700">{{ $entry->account_code }}</td>
                                <td class="py-2.5 px-4 text-gray-900">{{ $entry->account->name ?? 'Cuenta Sin Nombre' }}</td>
                                <td class="py-2.5 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-700">
                                        {{ ucfirst($entry->component_name) }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-4 text-right font-mono text-gray-800">{{ $entry->debit > 0 ? number_format($entry->debit, 0, ',', '.') : '-' }}</td>
                                <td class="py-2.5 px-4 text-right font-mono text-gray-800">{{ $entry->credit > 0 ? number_format($entry->credit, 0, ',', '.') : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 border-t border-gray-200 font-bold font-mono text-xs text-gray-800">
                            <td colspan="3" class="py-3 px-4 text-right uppercase font-sans text-[11px]">Totales:</td>
                            <td class="py-3 px-4 text-right text-indigo-600">{{ number_format($journal->total_debit, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right text-indigo-600">{{ number_format($journal->total_credit, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>

</body>
</html>