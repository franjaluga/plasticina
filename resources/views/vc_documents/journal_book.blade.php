<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Libro Diario Contable</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased min-h-screen py-10">

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Cabecera -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-gray-200 pb-4 mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Libro Diario Contable</h1>
                <p class="text-xs text-gray-500 mt-1">
                    Periodo año: <span class="font-bold text-indigo-600">{{ $workingYear ?? session('working_year', date('Y')) }}</span>
                    @if(isset($dateFrom) && isset($dateTo))
                        <span class="ml-2 bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded text-[11px] font-semibold">
                            Desde: {{ $dateFrom }} hasta {{ $dateTo }}
                        </span>
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('vc_documents.journal_book.generate', ['date_from' => $dateFrom ?? '', 'date_to' => $dateTo ?? '', 'export' => 'csv']) }}" class="inline-flex items-center text-xs font-semibold bg-green-600 text-white px-3.5 py-2 rounded-lg shadow-sm hover:bg-green-700 transition">
                    Exportar a CSV
                </a>
                <a href="{{ route('vc_documents.pending') }}" class="inline-flex items-center text-xs font-semibold bg-white border border-gray-300 text-gray-700 px-3.5 py-2 rounded-lg shadow-sm hover:bg-gray-50 transition">
                    Pendientes
                </a>
                <a href="{{ route('vc_documents.journal_book.form') }}" class="inline-flex items-center text-xs font-semibold bg-white border border-gray-300 text-gray-700 px-3.5 py-2 rounded-lg shadow-sm hover:bg-gray-50 transition">
                    &larr; Cambiar Rango
                </a>
            </div>
        </div>

        <!-- Tabla Maestra / Minimalista -->
        <div class="bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs table-auto">
                    <thead>
                        <tr class="bg-gray-800 text-white uppercase tracking-wider text-[11px]">
                            <th class="py-3 px-4 w-32">Código</th>
                            <th class="py-3 px-4">Nombre de Cuenta</th>
                            <th class="py-3 px-4 text-right w-28">Debe</th>
                            <th class="py-3 px-4 text-right w-28">Haber</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($journals as $journal)
                            <!-- Separador o contexto del Asiento con el entry_number -->
                            <tr class="bg-gray-100 font-semibold text-gray-800">
                                <td colspan="4" class="py-2.5 px-4 text-xs">
                                    Asiento N° <span class="font-bold text-indigo-700">{{ $journal->entry_number }}</span> 
                                    <span class="mx-1 text-gray-400">|</span> Fecha: <span class="font-normal">{{ $journal->date }}</span>
                                    
                                    @if($journal->document)
                                        <span class="text-gray-500 font-normal ml-2">(Doc Folio: 
                                            <a href="{{ route('accounting.documents.detail', $journal->document->id) }}" class="text-indigo-600 hover:underline font-bold">
                                                {{ $journal->document->folio }}
                                            </a>)
                                        </span>
                                    @endif

                                    @if($journal->description)
                                        <span class="text-gray-600 font-normal ml-2">— {{ $journal->description }}</span>
                                    @endif

                                    <span class="float-right font-bold {{ $journal->is_balanced ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $journal->is_balanced ? '● Cuadrado' : '● Descuadrado' }}
                                    </span>
                                </td>
                            </tr>

                            @foreach($journal->entries as $entry)
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="py-2 px-4 font-mono text-gray-600 font-medium">{{ $entry->account_code }}</td>
                                    <td class="py-2 px-4 text-gray-900 font-sans">{{ $entry->account->name ?? 'Sin nombre' }}</td>
                                    <td class="py-2 px-4 text-right font-mono text-gray-800">{{ $entry->debit ? number_format($entry->debit, 0, ',', '.') : '-' }}</td>
                                    <td class="py-2 px-4 text-right font-mono text-gray-800">{{ $entry->credit ? number_format($entry->credit, 0, ',', '.') : '-' }}</td>
                                </tr>
                            @endforeach

                            <!-- Totales por asiento acomodados debajo -->
                            <tr class="bg-gray-50/50 border-b border-gray-200 text-gray-600 font-mono text-[11px]">
                                <td class="py-2 px-4"></td>
                                <td class="py-2 px-4 italic font-sans font-medium text-gray-500">Total Asiento N° {{ $journal->entry_number }}:</td>
                                <td class="py-2 px-4 text-right font-bold text-indigo-600">{{ number_format($journal->total_debit, 0, ',', '.') }}</td>
                                <td class="py-2 px-4 text-right font-bold text-indigo-600">{{ number_format($journal->total_credit, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-12 text-gray-400 text-sm">
                                    No hay registros en el libro diario para el periodo o rango seleccionado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>
</html>