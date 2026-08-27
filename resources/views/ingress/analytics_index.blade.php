<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reportes y Análisis - Sistema Contable</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900 flex items-center justify-center min-h-screen p-4">

    <div class="max-w-6xl w-full mx-auto p-8 bg-white shadow-xl rounded-2xl border border-slate-100">
        
        <!-- Enlace para volver -->
        <div class="mb-6">
            <a href="{{ route('welcome') }}" class="inline-flex items-center text-xs font-semibold bg-white text-slate-600 border border-slate-200 px-3 py-2 rounded-lg hover:bg-slate-50 transition shadow-sm">
                &larr; Volver al inicio
            </a>
        </div>

        <!-- Encabezado de la vista -->
        <div class="text-center mb-8">
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Reportes y Análisis</h1>
            <p class="text-sm text-slate-500 mt-1">Selecciona un módulo o contexto de gestión</p>
        </div>

        <!-- ================= SECCIÓN 1: CONTABILIDAD ================= -->
        <div class="mb-8">
            <div class="flex items-center space-x-3 mb-4 border-b border-slate-200 pb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 bg-slate-100 px-2.5 py-1 rounded-md">Contexto: Contabilidad</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <!-- 1. LIBRO DIARIO -->
                <a href="{{ route('reports.journal_context') }}" class="group p-6 bg-white border border-slate-200 rounded-2xl hover:border-emerald-500 hover:shadow-lg transition text-left flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4 group-hover:bg-emerald-600 group-hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <h2 class="font-bold text-slate-800 group-hover:text-emerald-600 transition text-base mb-1">Libro Diario</h2>
                        <p class="text-xs text-slate-500 leading-relaxed">Gestión de asientos del sistema, libro diario V/C y registros históricos.</p>
                    </div>
                    <span class="text-xs font-semibold text-emerald-600 mt-6 flex items-center">
                        Acceder &rarr;
                    </span>
                </a>

                <!-- 2. BALANCE -->
                <a href="{{ route('reports.balance_context') }}" class="group p-6 bg-white border border-slate-200 rounded-2xl hover:border-indigo-500 hover:shadow-lg transition text-left flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4 group-hover:bg-indigo-600 group-hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <h2 class="font-bold text-slate-800 group-hover:text-indigo-600 transition text-base mb-1">Balance</h2>
                        <p class="text-xs text-slate-500 leading-relaxed">Balance tributario de 8 columnas.</p>
                    </div>
                    <span class="text-xs font-semibold text-indigo-600 mt-6 flex items-center">
                        Acceder &rarr;
                    </span>
                </a>

                <!-- 3. LIBRO MAYOR -->
                <a href="{{ route('reports.ledger_context') }}" class="group p-6 bg-white border border-slate-200 rounded-2xl hover:border-amber-500 hover:shadow-lg transition text-left flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-4 group-hover:bg-amber-600 group-hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        </div>
                        <h2 class="font-bold text-slate-800 group-hover:text-amber-600 transition text-base mb-1">Libro Mayor</h2>
                        <p class="text-xs text-slate-500 leading-relaxed">Consulta detallada de movimientos, cargos y abonos por cuenta contable.</p>
                    </div>
                    <span class="text-xs font-semibold text-amber-600 mt-6 flex items-center">
                        Acceder &rarr;
                    </span>
                </a>

                <!-- 4. ANALÍTICOS DE COMPRAS O VENTAS -->
                <a href="{{ route('accounting.analytics') }}" class="group p-6 bg-white border border-slate-200 rounded-2xl hover:border-blue-500 hover:shadow-lg transition text-left flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4 group-hover:bg-blue-600 group-hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2m-6 9l2 2 4-4"></path></svg>
                        </div>
                        <h2 class="font-bold text-slate-800 group-hover:text-blue-600 transition text-base mb-1">Analíticos (V/C)</h2>
                        <p class="text-xs text-slate-500 leading-relaxed">Consulta específica de compras o ventas por tipo, mes y año.</p>
                    </div>
                    <span class="text-xs font-semibold text-blue-600 mt-6 flex items-center">
                        Consultar &rarr;
                    </span>
                </a>

                <!-- 5. AUDITORÍA V/C -->
                <a href="{{ route('accounting.reports.audit') }}" class="group p-6 bg-white border border-slate-200 rounded-2xl hover:border-sky-500 hover:shadow-lg transition text-left flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center mb-4 group-hover:bg-sky-600 group-hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2m-6 9l2 2 4-4"></path></svg>
                        </div>
                        <h2 class="font-bold text-slate-800 group-hover:text-sky-600 transition text-base mb-1">Auditoría V/C</h2>
                        <p class="text-xs text-slate-500 leading-relaxed">Auditoría de registros y gestión de eliminación de documentos o asientos.</p>
                    </div>
                    <span class="text-xs font-semibold text-sky-600 mt-6 flex items-center">
                        Auditar &rarr;
                    </span>
                </a>

            </div>
        </div>

        <!-- ================= SECCIÓN 2: FINANZAS ================= -->
        <div>
            <div class="flex items-center space-x-3 mb-4 border-b border-slate-200 pb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 bg-slate-100 px-2.5 py-1 rounded-md">Contexto: Finanzas</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <!-- 6. COBROS Y PAGOS -->
                <a href="{{ route('accounting.payments.index') }}" class="group p-6 bg-white border border-slate-200 rounded-2xl hover:border-purple-500 hover:shadow-lg transition text-left flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mb-4 group-hover:bg-purple-600 group-hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <h2 class="font-bold text-slate-800 group-hover:text-purple-600 transition text-base mb-1">Cobros y Pagos</h2>
                        <p class="text-xs text-slate-500 leading-relaxed">Gestión de saldos pendientes y pagos a proveedores o cobros a clientes.</p>
                    </div>
                    <span class="text-xs font-semibold text-purple-600 mt-6 flex items-center">
                        Gestionar &rarr;
                    </span>
                </a>

            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 pt-4 border-t border-slate-200 text-xs text-slate-400 flex justify-between items-center">
            <p>Sistema Contable v1.0</p>
        </div>
    </div>

</body>
</html>