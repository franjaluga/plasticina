<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reportes Contables</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900 flex items-center justify-center min-h-screen p-4">

    <div class="max-w-4xl w-full mx-auto p-8 bg-white shadow-xl rounded-2xl border border-slate-100">
        
        <!-- Cabecera -->
        <div class="flex justify-between items-center border-b border-slate-200 pb-4 mb-6">
            <div>
                <h1 class="text-xl font-extrabold text-slate-800 tracking-tight">Reportes Contables</h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    Periodo tributario año: <span class="font-bold text-indigo-600">{{ session('working_year', date('Y')) }}</span>
                </p>
            </div>
            <div>
                <a href="{{ route('welcome') }}" class="text-xs font-semibold bg-slate-100 text-slate-700 px-3 py-1.5 rounded-lg hover:bg-slate-200 transition">
                    &larr; Volver
                </a>
            </div>
        </div>

        <!-- Grilla de Reportes -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            
            <!-- Botón 1: Libro Diario -->
            <a href="{{ route('vc_documents.journal_book') }}" class="group p-5 bg-white border border-slate-200 rounded-xl hover:border-emerald-500 hover:shadow-md transition text-left flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3 group-hover:bg-emerald-600 group-hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h2 class="font-bold text-slate-800 group-hover:text-emerald-600 transition text-base">Libro Diario</h2>
                    <p class="text-xs text-slate-500 mt-1">Registro histórico de asientos contables del periodo.</p>
                </div>
                <span class="text-xs font-semibold text-emerald-600 mt-4 flex items-center">
                    Consultar &rarr;
                </span>
            </a>

            <!-- Botón 2: Balance Tributario -->
            <a href="{{ route('vc_documents.tax_balance') }}" class="group p-5 bg-white border border-slate-200 rounded-xl hover:border-indigo-500 hover:shadow-md transition text-left flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center mb-3 group-hover:bg-indigo-600 group-hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h2 class="font-bold text-slate-800 group-hover:text-indigo-600 transition text-base">Balance 8 Columnas</h2>
                    <p class="text-xs text-slate-500 mt-1">Resumen contable de sumas, saldos, inventario y resultados.</p>
                </div>
                <span class="text-xs font-semibold text-indigo-600 mt-4 flex items-center">
                    Generar &rarr;
                </span>
            </a>

            <!-- NUEVA TARJETA: Consulta de Compras / Ventas por Mes y Año -->
            <div class="md:col-span-2 p-5 bg-slate-50 border border-slate-200 rounded-xl">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mr-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-slate-800 text-base">Consulta de Compras o Ventas</h2>
                        <p class="text-xs text-slate-500">Filtra los documentos específicos seleccionando tipo, mes y año.</p>
                    </div>
                </div>

                <!-- Formulario de Consulta -->
                <form action="{{ route('vc_documents.query') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4 mt-4">
                    <div>
                        <label for="type_vc" class="block text-xs font-semibold text-slate-600 mb-1">Tipo de Documento</label>
                        <select name="type_vc" id="type_vc" class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 bg-white border" required>
                            <option value="V">Venta</option>
                            <option value="C">Compra</option>
                        </select>
                    </div>
                    <div>
                        <label for="month" class="block text-xs font-semibold text-slate-600 mb-1">Mes</label>
                        <select name="month" id="month" class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 bg-white border" required>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ (request('month', date('n')) == $m) ? 'selected' : '' }}>
                                    {{ $m }} - {{ ucfirst(\Carbon\Carbon::create()->month($m)->translatedFormat('F')) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="year" class="block text-xs font-semibold text-slate-600 mb-1">Año</label>
                        <input type="number" name="year" id="year" value="{{ session('working_year', date('Y')) }}" class="w-full text-xs rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 bg-white border" required>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs py-2.5 px-4 rounded-lg shadow-sm transition flex items-center justify-center">
                            Consultar &rarr;
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>

</body>
</html>