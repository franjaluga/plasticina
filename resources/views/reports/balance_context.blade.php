<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contexto Balance - Sistema Contable</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900 flex items-center justify-center min-h-screen p-4">
    
    <div class="max-w-4xl w-full mx-auto p-8 bg-white shadow-xl rounded-2xl border border-slate-100">
        
        <!-- Enlace para volver -->
        <div class="mb-6">
            <a href="{{ route('reports.analytics') }}" class="inline-flex items-center text-xs font-semibold bg-white text-slate-600 border border-slate-200 px-3 py-2 rounded-lg hover:bg-slate-50 transition shadow-sm">
                &larr; Volver
            </a>
        </div>

        <!-- Encabezado de la vista -->
        <div class="text-center mb-8">
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Contexto: Balance</h1>
            <p class="text-sm text-slate-500 mt-1">Estados financieros y balance tributario</p>
        </div>

        <!-- Grilla de Opciones -->
        <div class="grid grid-cols-1 md:grid-cols-1 gap-6 max-w-xl mx-auto">
            
            <!-- Balance Tributario (8 Columnas) -->
            <a href="{{ route('vc_documents.tax_balance') }}" class="group p-6 bg-white border border-slate-200 rounded-2xl hover:border-indigo-500 hover:shadow-lg transition text-left flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4 group-hover:bg-indigo-600 group-hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h2 class="font-bold text-slate-800 group-hover:text-indigo-600 transition text-base mb-1">Balance Tributario (8 Columnas)</h2>
                    <p class="text-xs text-slate-500 leading-relaxed">Genera el resumen de sumas, saldos, inventario y resultados.</p>
                </div>
                <span class="text-xs font-semibold text-indigo-600 mt-6 flex items-center">
                    Generar &rarr;
                </span>
            </a>

        </div>

        <!-- Footer -->
        <div class="mt-8 pt-4 border-t border-slate-200 text-xs text-slate-400 flex justify-between items-center">
            <p>Sistema Contable v1.0</p>
        </div>
    </div>

</body>
</html>