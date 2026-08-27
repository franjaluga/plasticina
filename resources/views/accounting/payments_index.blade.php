<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Cobros y Pagos - Sistema Contable</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900 p-6">

    <div class="max-w-7xl mx-auto p-8 bg-white shadow-xl rounded-2xl border border-slate-100">
        
        <!-- Cabecera -->
        <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-200">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Gestión de Cobros y Pagos (Pendientes)</h1>
                <p class="text-sm text-slate-500 mt-1">Owner: <span class="font-bold text-slate-700">{{ $activeOwner?->name ?? 'N/A' }}</span></p>
            </div>
            <div>
                <a href="{{ route('reports.analytics') }}" class="text-xs font-semibold bg-slate-100 text-slate-700 px-3 py-1.5 rounded-lg hover:bg-slate-200 transition">
                    &larr; Volver
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm">{{ session('error') }}</div>
        @endif

        <!-- Tabla de Documentos con Saldo Pendiente -->
        <div class="overflow-x-auto border border-slate-200 rounded-xl">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-100 text-slate-700 uppercase text-xs tracking-wider border-b border-slate-200">
                        <th class="p-3">Tipo (C/V)</th>
                        <th class="p-3">Folio</th>
                        <th class="p-3">Fecha</th>
                        <th class="p-3 text-right">Total Documento</th>
                        <th class="p-3 text-right">Pagado / Cobrado</th>
                        <th class="p-3 text-right">Saldo Pendiente</th>
                        <th class="p-3 text-center">Acción de Pago / Cobro</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    @forelse($pendingDocuments as $doc)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-3 font-bold">
                                <span class="px-2 py-1 rounded text-xs {{ $doc->type_vc == 'V' ? 'bg-indigo-100 text-indigo-700' : 'bg-purple-100 text-purple-700' }}">
                                    {{ $doc->type_vc == 'V' ? 'Venta' : 'Compra' }}
                                </span>
                            </td>
                            <td class="p-3 font-semibold">#{{ $doc->folio }}</td>
                            <td class="p-3">{{ $doc->date }}</td>
                            <td class="p-3 text-right font-mono">$ {{ number_format($doc->total, 2, ',', '.') }}</td>
                            <td class="p-3 text-right font-mono text-emerald-600">$ {{ number_format($doc->calculated_paid, 2, ',', '.') }}</td>
                            <td class="p-3 text-right font-mono font-bold text-rose-600">$ {{ number_format($doc->calculated_balance, 2, ',', '.') }}</td>
                            <td class="p-3 text-center">
                                <form action="{{ route('accounting.payments.store') }}" method="POST" class="flex items-center justify-center space-x-2">
                                    @csrf
                                    <input type="hidden" name="document_id" value="{{ $doc->id }}">
                                    <input type="date" name="date" value="{{ date('Y-m-d') }}" class="text-xs border border-slate-300 rounded px-2 py-1">
                                    <input type="number" name="amount" step="0.01" max="{{ $doc->calculated_balance }}" value="{{ $doc->calculated_balance }}" placeholder="Monto" class="w-24 text-xs border border-slate-300 rounded px-2 py-1 font-mono">
                                    <select name="bank_account_code" class="text-xs border border-slate-300 rounded px-2 py-1 bg-white">
                                        @foreach($accounts as $acc)
                                            <option value="{{ $acc->code }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="px-3 py-1 bg-emerald-600 text-white rounded text-xs font-semibold hover:bg-emerald-700 transition shadow-sm whitespace-nowrap">
                                        {{ $doc->type_vc == 'V' ? 'Cobrar' : 'Pagar' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-slate-400">
                                No hay documentos con saldos pendientes de pago o cobro en este periodo.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>