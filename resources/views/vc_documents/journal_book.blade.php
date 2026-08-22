<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Libro Diario Contable</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-gray-900 font-sans p-8 max-w-5xl mx-auto">

    <!-- Cabecera simple con el año activo y enlaces -->
    <div class="flex justify-between items-baseline border-b border-gray-300 pb-4 mb-6">
        <div>
            <h1 class="text-xl font-semibold tracking-tight">Libro Diario</h1>
            <p class="text-xs text-gray-500">
                Periodo tributario año: <span class="font-bold text-indigo-600">{{ $year ?? session('working_year', date('Y')) }}</span>
            </p>
        </div>
        <div class="space-x-4 text-sm">
            <a href="{{ route('vc_documents.export_csv') }}" class="text-green-600 hover:underline font-medium">Exportar a CSV</a>
            <a href="{{ route('vc_documents.pending') }}" class="text-gray-600 hover:underline">Pendientes</a>
            <a href="{{ route('welcome') }}" class="text-xs font-semibold bg-slate-100 text-slate-700 px-3 py-1.5 rounded-lg hover:bg-slate-200 transition">
                &larr; Volver
            </a>
        </div>
    </div>

    <!-- Tabla Maestra / Minimalista -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead>
                <tr class="border-b border-gray-900 text-xs text-gray-500 uppercase">
                    <th class="py-2 pr-4">Asiento / Fecha</th>
                    <th class="py-2 px-4">Cuenta</th>
                    <th class="py-2 px-4">Componente</th>
                    <th class="py-2 px-4 text-right">Debe</th>
                    <th class="py-2 px-4 text-right">Haber</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-xs">
                @forelse($journals as $journal)
                    <!-- Separador o contexto del Asiento con el nuevo entry_number -->
                    <tr class="bg-gray-50 font-medium text-gray-700">
                        <td colspan="5" class="py-2 px-2">
                            Asiento N° <span class="font-bold text-indigo-700">{{ $journal->entry_number }}</span> — Fecha: {{ $journal->date }} 
                            @if($journal->document)
                                <span class="text-gray-500 font-normal">(Doc Folio: {{ $journal->document->folio }})</span>
                            @endif
                            <span class="float-right font-normal {{ $journal->is_balanced ? 'text-green-600' : 'text-red-600' }}">
                                {{ $journal->is_balanced ? '● Cuadrado' : '● Descuadrado' }}
                            </span>
                        </td>
                    </tr>
                    @foreach($journal->entries as $entry)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 pr-4"></td>
                            <td class="py-2 px-4 font-mono text-gray-600">{{ $entry->account_code }}</td>
                            <td class="py-2 px-4 text-gray-800">{{ $entry->account->name ?? 'Sin nombre' }}</td>
                            <td class="py-2 px-4 text-right font-mono">{{ $entry->debit ? number_format($entry->debit, 2, ',', '.') : '-' }}</td>
                            <td class="py-2 px-4 text-right font-mono">{{ $entry->credit ? number_format($entry->credit, 2, ',', '.') : '-' }}</td>
                        </tr>
                    @endforeach
                    <!-- Totales por asiento sutiles -->
                    <tr class="border-b border-gray-200 text-gray-500">
                        <td colspan="3" class="py-1 px-2 text-right italic">Total Asiento N° {{ $journal->entry_number }}</td>
                        <td class="py-1 px-4 text-right font-mono">{{ number_format($journal->total_debit, 2, ',', '.') }}</td>
                        <td class="py-1 px-4 text-right font-mono">{{ number_format($journal->total_credit, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-10 text-gray-400">No hay registros en el libro diario para este año.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</body>
</html>