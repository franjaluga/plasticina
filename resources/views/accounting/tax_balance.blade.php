<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Balance Tributario de 8 Columnas</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-slate-900 font-sans p-8 max-w-6xl mx-auto">

    <!-- Cabecera Minimalista con Datos de la Empresa -->
    <div class="flex justify-between items-start border-b border-slate-300 pb-4 mb-6">
        <div>
            <h1 class="text-xl font-semibold tracking-tight">Balance Tributario de 8 Columnas</h1>
            <div class="text-xs text-slate-600 mt-1 space-y-0.5">
                @php
                    $activeOwner = app(\App\Services\OwnerService::class)->getActiveOwner();
                @endphp
                <p><span class="font-bold text-slate-900">Empresa:</span> {{ $activeOwner->name ?? 'Sin Empresa' }}</p>
                <p><span class="font-bold text-slate-900">RUT:</span> {{ $activeOwner->rut ?? 'N/A' }}</p>
                <p><span class="font-bold text-slate-900">Periodo Comercial:</span> {{ $year }}</p>
            </div>
        </div>
        <div class="text-sm">
            <a href="{{ route('welcome') }}" class="text-xs font-semibold bg-slate-100 text-slate-700 px-3 py-1.5 rounded-lg hover:bg-slate-200 transition">
                &larr; Volver
            </a>
        </div>
    </div>

    @php
        $sum_sd = 0; $sum_sc = 0;
        $sum_bd = 0; $sum_bc = 0;
        $sum_act = 0; $sum_pas = 0;
        $sum_per = 0; $sum_gan = 0;

        foreach($balanceRows as $row) {
            $sum_sd += $row['sum_debit']; 
            $sum_sc += $row['sum_credit'];
            $sum_bd += $row['balance_debit']; 
            $sum_bc += $row['balance_credit'];
            $sum_act += $row['activo']; 
            $sum_pas += $row['pasivo'];
            $sum_per += $row['perdida']; 
            $sum_gan += $row['ganancia'];
        }

        // Cálculo del Resultado del Ejercicio
        $diffResult = abs($sum_per - $sum_gan);
        $resPerdida = 0;
        $resGanancia = 0;
        $resActivo = 0;
        $resPasivo = 0;

        if ($sum_gan > $sum_per) {
            $resPerdida = $diffResult; 
            $resPasivo = $diffResult;  
        } elseif ($sum_per > $sum_gan) {
            $resGanancia = $diffResult; 
            $resActivo = $diffResult;   
        }

        $totalInvActivo = $sum_act + $resActivo;
        $totalInvPasivo = $sum_pas + $resPasivo;
        $totalResPerdida = $sum_per + $resPerdida;
        $totalResGanancia = $sum_gan + $resGanancia;
    @endphp

    <!-- Tabla Minimalista con Grilla -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs border border-slate-200">
            <thead>
                <tr class="border-b border-slate-300 text-[10px] text-slate-600 uppercase text-center bg-slate-50">
                    <th rowspan="2" class="py-2 px-2 text-left border-r border-slate-200">Código</th>
                    <th rowspan="2" class="py-2 px-4 text-left border-r border-slate-200">Cuenta Contable</th>
                    <th colspan="2" class="py-1 px-2 border-b border-r border-slate-200">Sumas</th>
                    <th colspan="2" class="py-1 px-2 border-b border-r border-slate-200">Saldos</th>
                    <th colspan="2" class="py-1 px-2 border-b border-r border-slate-200">Inventario</th>
                    <th colspan="2" class="py-1 px-2 border-b border-slate-200">Resultados</th>
                </tr>
                <tr class="border-b border-slate-300 text-[10px] text-slate-500 uppercase text-right bg-slate-50">
                    <th class="py-1 px-2 border-r border-slate-200">Débito</th>
                    <th class="py-1 px-2 border-r border-slate-200">Crédito</th>
                    <th class="py-1 px-2 border-r border-slate-200">Deudor</th>
                    <th class="py-1 px-2 border-r border-slate-200">Acreedor</th>
                    <th class="py-1 px-2 border-r border-slate-200">Activo</th>
                    <th class="py-1 px-2 border-r border-slate-200">Pasivo</th>
                    <th class="py-1 px-2 border-r border-slate-200">Pérdida</th>
                    <th class="py-1 px-2">Ganancia</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-xs">
                @forelse($balanceRows as $row)
                    <tr class="font-mono">
                        <td class="py-1.5 px-2 font-bold text-slate-700 font-sans border-r border-slate-200">{{ $row['code'] }}</td>
                        <td class="py-1.5 px-4 font-sans text-slate-800 border-r border-slate-200">{{ $row['name'] }}</td>
                        
                        <td class="py-1.5 px-2 text-right border-r border-slate-200">{{ $row['sum_debit'] ? number_format($row['sum_debit'], 0, ',', '.') : '-' }}</td>
                        <td class="py-1.5 px-2 text-right border-r border-slate-200">{{ $row['sum_credit'] ? number_format($row['sum_credit'], 0, ',', '.') : '-' }}</td>
                        
                        <td class="py-1.5 px-2 text-right border-r border-slate-200">{{ $row['balance_debit'] ? number_format($row['balance_debit'], 0, ',', '.') : '-' }}</td>
                        <td class="py-1.5 px-2 text-right border-r border-slate-200">{{ $row['balance_credit'] ? number_format($row['balance_credit'], 0, ',', '.') : '-' }}</td>
                        
                        <td class="py-1.5 px-2 text-right border-r border-slate-200">{{ $row['activo'] ? number_format($row['activo'], 0, ',', '.') : '-' }}</td>
                        <td class="py-1.5 px-2 text-right border-r border-slate-200">{{ $row['pasivo'] ? number_format($row['pasivo'], 0, ',', '.') : '-' }}</td>
                        
                        <td class="py-1.5 px-2 text-right border-r border-slate-200">{{ $row['perdida'] ? number_format($row['perdida'], 0, ',', '.') : '-' }}</td>
                        <td class="py-1.5 px-2 text-right">{{ $row['ganancia'] ? number_format($row['ganancia'], 0, ',', '.') : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-10 text-slate-400 font-sans">No hay registros contables en los asientos para generar el balance de este año.</td>
                    </tr>
                @endforelse
            </tbody>

            @if($balanceRows->isNotEmpty())
                <tfoot class="border-t-2 border-slate-300 font-bold font-mono text-slate-900 bg-white text-xs">
                    <!-- Fila de Sumas -->
                    <tr class="border-b border-slate-200">
                        <td colspan="2" class="py-2 px-4 text-left font-sans uppercase text-[10px] border-r border-slate-200">Sumas</td>
                        <td class="py-2 px-2 text-right border-r border-slate-200">{{ number_format($sum_sd, 0, ',', '.') }}</td>
                        <td class="py-2 px-2 text-right border-r border-slate-200">{{ number_format($sum_sc, 0, ',', '.') }}</td>
                        <td class="py-2 px-2 text-right border-r border-slate-200">{{ number_format($sum_bd, 0, ',', '.') }}</td>
                        <td class="py-2 px-2 text-right border-r border-slate-200">{{ number_format($sum_bc, 0, ',', '.') }}</td>
                        <td class="py-2 px-2 text-right border-r border-slate-200">{{ number_format($sum_act, 0, ',', '.') }}</td>
                        <td class="py-2 px-2 text-right border-r border-slate-200">{{ number_format($sum_pas, 0, ',', '.') }}</td>
                        <td class="py-2 px-2 text-right border-r border-slate-200">{{ number_format($sum_per, 0, ',', '.') }}</td>
                        <td class="py-2 px-2 text-right">{{ number_format($sum_gan, 0, ',', '.') }}</td>
                    </tr>

                    <!-- Fila de Resultado del Ejercicio -->
                    <tr class="border-b border-slate-200">
                        <td colspan="2" class="py-2 px-4 text-left font-sans uppercase text-[10px] border-r border-slate-200">
                            Resultado del Ejercicio ({{ $sum_gan > $sum_per ? 'Utilidad' : 'Pérdida' }})
                        </td>
                        <td colspan="4" class="border-r border-slate-200"></td>
                        <td class="py-2 px-2 text-right border-r border-slate-200">{{ $resActivo ? number_format($resActivo, 0, ',', '.') : '-' }}</td>
                        <td class="py-2 px-2 text-right border-r border-slate-200">{{ $resPasivo ? number_format($resPasivo, 0, ',', '.') : '-' }}</td>
                        <td class="py-2 px-2 text-right border-r border-slate-200">{{ $resPerdida ? number_format($resPerdida, 0, ',', '.') : '-' }}</td>
                        <td class="py-2 px-2 text-right">{{ $resGanancia ? number_format($resGanancia, 0, ',', '.') : '-' }}</td>
                    </tr>

                    <!-- Fila de Sumas Iguales -->
                    <tr class="border-t border-slate-300">
                        <td colspan="2" class="py-2.5 px-4 text-left font-sans uppercase text-[10px] border-r border-slate-300">Sumas Iguales</td>
                        <td class="py-2.5 px-2 text-right border-r border-slate-300">{{ number_format($sum_sd, 0, ',', '.') }}</td>
                        <td class="py-2.5 px-2 text-right border-r border-slate-300">{{ number_format($sum_sc, 0, ',', '.') }}</td>
                        <td class="py-2.5 px-2 text-right border-r border-slate-300">{{ number_format($sum_bd, 0, ',', '.') }}</td>
                        <td class="py-2.5 px-2 text-right border-r border-slate-300">{{ number_format($sum_bc, 0, ',', '.') }}</td>
                        <td class="py-2.5 px-2 text-right border-r border-slate-300 font-extrabold">{{ number_format($totalInvActivo, 0, ',', '.') }}</td>
                        <td class="py-2.5 px-2 text-right border-r border-slate-300 font-extrabold">{{ number_format($totalInvPasivo, 0, ',', '.') }}</td>
                        <td class="py-2.5 px-2 text-right border-r border-slate-300 font-extrabold">{{ number_format($totalResPerdida, 0, ',', '.') }}</td>
                        <td class="py-2.5 px-2 text-right font-extrabold">{{ number_format($totalResGanancia, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>

</body>
</html>