<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Libro Mayor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-black font-sans p-6 max-w-[95%] mx-auto">

    <!-- Cabecera del Libro -->
    <div class="flex justify-between items-baseline pb-3 mb-4 border-b border-gray-300">
        <div>
            <h1 class="text-base font-bold uppercase tracking-wider">
                Libro Mayor - Cuenta: <span class="text-indigo-600">{{ $accountCode }}</span>
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

    <!-- Tabla del Libro Mayor -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="border-b-2 border-black text-[11px] font-bold uppercase text-gray-700">
                    <th class="py-2.5 px-3">Fecha</th>
                    <th class="py-2.5 px-3">Asiento ID</th>
                    <th class="py-2.5 px-3">Glosa / Entidad</th>
                    <th class="py-2.5 px-3 text-right">Debe</th>
                    <th class="py-2.5 px-3 text-right">Haber</th>
                    <th class="py-2.5 px-3 text-right">Saldo</th>
                </tr>
            </thead>
            <tbody class="font-mono text-[11px] divide-y divide-gray-200">
                @php 
                    $saldo = 0; 
                    $totalDebe = 0;
                    $totalHaber = 0;
                @endphp
                @forelse($journals as $journal)
                    @foreach($journal->entries->where('account_code', $accountCode) as $entry)
                        @php
                            $debe = $entry->debit ?? 0;
                            $haber = $entry->credit ?? 0;
                            $saldo += ($debe - $haber);
                            $totalDebe += $debe;
                            $totalHaber += $haber;
                        @endphp
                        <tr>
                            <td class="py-2 px-3 text-gray-600 font-sans whitespace-nowrap">{{ $journal->date }}</td>
                            <td class="py-2 px-3 font-bold">
                                {{ $journal->id }}
                            </td>
                            <td class="py-2 px-3 text-gray-800 font-sans">
                                {{ $journal->document->entity->name ?? 'Asiento contable N° ' . $journal->id }}
                            </td>
                            <td class="py-2 px-3 text-right">{{ $debe > 0 ? number_format($debe, 0, ',', '.') : '-' }}</td>
                            <td class="py-2 px-3 text-right">{{ $haber > 0 ? number_format($haber, 0, ',', '.') : '-' }}</td>
                            <td class="py-2 px-3 text-right font-bold">{{ number_format($saldo, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-gray-400 font-sans">
                            No se encontraron movimientos para esta cuenta en el periodo seleccionado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            
            @if($totalDebe > 0 || $totalHaber > 0)
                <tfoot>
                    <tr class="border-t-2 border-black font-bold font-mono text-[11px] text-black">
                        <td colspan="3" class="py-3 px-3 text-right uppercase font-sans">TOTALES:</td>
                        <td class="py-3 px-3 text-right">{{ number_format($totalDebe, 0, ',', '.') }}</td>
                        <td class="py-3 px-3 text-right">{{ number_format($totalHaber, 0, ',', '.') }}</td>
                        <td class="py-3 px-3 text-right">{{ number_format($saldo, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

</body>
</html>