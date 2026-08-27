<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contexto Libro Mayor - Sistema Contable</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900 flex items-center justify-center min-h-screen p-4">
    <div class="max-w-4xl w-full mx-auto p-8 bg-white shadow-xl rounded-2xl border border-slate-100">
        <div class="mb-6">
            <a href="{{ route('reports.analytics') }}" class="inline-flex items-center text-xs font-semibold bg-white text-slate-600 border border-slate-200 px-3 py-2 rounded-lg hover:bg-slate-50 transition shadow-sm">&larr; Volver</a>
        </div>
        <div class="text-center mb-8">
            <h1 class="text-2xl font-extrabold text-slate-800">Contexto: Libro Mayor</h1>
            <p class="text-sm text-slate-500 mt-1">Consulta de movimientos por cuenta contable</p>
        </div>

        <!-- Formulario de Libro Mayor por Cuenta -->
        <div class="p-6 bg-slate-50 border border-slate-200 rounded-2xl">
            <form action="{{ route('accounting.ledger') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label for="account_code" class="block text-xs font-semibold text-slate-600 mb-1">N° de Cuenta</label>
                    <input type="text" name="account_code" id="account_code" value="{{ request('account_code') }}" list="accounts_list" placeholder="Código" autocomplete="off" class="w-full text-xs rounded-lg border-slate-300 shadow-sm p-2 bg-white border" required>
                    <datalist id="accounts_list">
                        @foreach($accounts ?? [] as $acc)
                            <option value="{{ $acc->code }}">{{ $acc->name }}</option>
                        @endforeach
                    </datalist>
                </div>
                <div>
                    <label for="ledger_month" class="block text-xs font-semibold text-slate-600 mb-1">Mes</label>
                    <select name="month" id="ledger_month" class="w-full text-xs rounded-lg border-slate-300 shadow-sm p-2 bg-white border" required>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ (request('month', date('n')) == $m) ? 'selected' : '' }}>{{ $m }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label for="ledger_year" class="block text-xs font-semibold text-slate-600 mb-1">Año</label>
                    <input type="number" name="year" id="ledger_year" value="{{ session('working_year', date('Y')) }}" class="w-full text-xs rounded-lg border-slate-300 shadow-sm p-2 bg-white border" required>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs py-2.5 px-4 rounded-lg shadow-sm transition">
                        Generar Mayor &rarr;
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>