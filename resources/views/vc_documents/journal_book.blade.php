<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Libro Diario Contable</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900 min-h-screen p-6">

    <div class="max-w-6xl mx-auto bg-white shadow-md rounded-lg p-6">
        
        <!-- Cabecera -->
        <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Libro Diario</h1>
                <p class="text-sm text-gray-600">Registro histórico de asientos contables de documentos V/C</p>
            </div>
            <div>
                <a href="{{ route('vc_documents.pending') }}" class="bg-gray-600 text-white text-sm px-4 py-2 rounded hover:bg-gray-700 transition mr-2">
                    Pendientes
                </a>
                <a href="{{ route('vc_documents.create') }}" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded hover:bg-indigo-700 transition">
                    Volver al Inicio
                </a>
            </div>
        </div>

        <!-- Listado de Asientos -->
        <div class="space-y-6">
            @forelse($journals as $journal)
                <div class="border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                    <!-- Cabecera del Asiento -->
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center text-sm">
                        <div>
                            <span class="font-bold text-indigo-700">Asiento #{{ $journal->id }}</span> 
                            <span class="text-gray-500 mx-2">|</span> 
                            <span class="font-medium text-gray-700">Fecha: {{ $journal->date }}</span>
                            <span class="text-gray-500 mx-2">|</span>
                            <span class="text-gray-600">Doc V/C ID: {{ $journal->vc_document_id }}</span>
                        </div>
                        <div>
                            @if($journal->is_balanced)
                                <span class="bg-green-100 text-green-800 text-xs px-2.5 py-1 rounded-full font-semibold">Cuadrado</span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs px-2.5 py-1 rounded-full font-semibold">Descuadrado</span>
                            @endif
                        </div>
                    </div>

                    <!-- Detalle del Asiento (Líneas) -->
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600 border-b border-gray-200 text-xs uppercase">
                                <th class="py-2 px-4">Cuenta</th>
                                <th class="py-2 px-4">Componente</th>
                                <th class="py-2 px-4 text-right">Debe</th>
                                <th class="py-2 px-4 text-right">Haber</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($journal->entries as $entry)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 px-4 font-mono font-medium text-gray-800">{{ $entry->account_code }}</td>
                                    <td class="py-2 px-4 text-gray-600 capitalize">{{ str_replace('_', ' ', $entry->component_name) }}</td>
                                    <td class="py-2 px-4 text-right font-mono">{{ $entry->debit > 0 ? number_format($entry->debit, 2, ',', '.') : '-' }}</td>
                                    <td class="py-2 px-4 text-right font-mono">{{ $entry->credit > 0 ? number_format($entry->credit, 2, ',', '.') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50 font-bold border-t border-gray-300">
                                <td colspan="2" class="py-2 px-4 text-right">Totales:</td>
                                <td class="py-2 px-4 text-right font-mono text-gray-800">{{ number_format($journal->total_debit, 2, ',', '.') }}</td>
                                <td class="py-2 px-4 text-right font-mono text-gray-800">{{ number_format($journal->total_credit, 2, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @empty
                <div class="text-center py-12 text-gray-500">
                    No existen asientos contables registrados en el libro diario.
                </div>
            @endforelse
        </div>

    </div>

</body>
</html>