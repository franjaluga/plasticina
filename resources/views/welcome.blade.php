<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema Contable</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900 flex items-center justify-center min-h-screen p-4">

    <div class="max-w-6xl w-full mx-auto p-8 bg-white shadow-xl rounded-2xl border border-slate-100">
        
        <!-- Contenedor superior: Owner Activo y Selector de Año -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            
            <!-- Indicador del Owner Activo (Ocupa 2 columnas) -->
            <div class="sm:col-span-2 p-4 bg-indigo-50/60 border border-indigo-100 rounded-xl flex items-center justify-between">
                <div class="text-left flex items-center space-x-3">
                    <div class="p-2 bg-indigo-600 text-white rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-indigo-600 block">Owner Activo</span>
                        <span class="text-sm font-bold text-slate-800">
                            @if($activeOwner)
                                {{ $activeOwner->name }} <span class="text-slate-500 font-normal">({{ $activeOwner->rut }})</span>
                            @else
                                <span class="text-amber-600 font-medium">Ningún owner seleccionado</span>
                            @endif
                        </span>
                    </div>
                </div>
                <div>
                    <a href="{{ route('owners.index') }}" class="text-xs font-medium bg-white text-indigo-600 border border-indigo-200 px-3 py-2 rounded-lg hover:bg-indigo-50 transition shadow-sm">
                        Cambiar
                    </a>
                </div>
            </div>

            <!-- Selector de Año de Trabajo (Ocupa 1 columna) -->
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex flex-col justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 block mb-1">Año de Trabajo</span>
                <form action="{{ route('period.update') }}" method="POST" class="flex items-center space-x-2">
                    @csrf
                    <select name="working_year" onchange="this.form.submit()" class="w-full text-sm font-bold text-slate-800 bg-white border border-slate-300 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer shadow-sm">
                        @php
                            $currentYearSelected = session('working_year', date('Y'));
                        @endphp
                        @for($y = date('Y'); $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ $currentYearSelected == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </form>
            </div>

        </div>

        <!-- Alerta de Documentos Pendientes -->
        @if(isset($pendingCount) && $pendingCount > 0)
            <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-center justify-between text-amber-900">
                <div class="text-left text-sm flex items-center space-x-3">
                    <div class="p-2 bg-amber-500 text-white rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <span class="font-bold block">¡Atención!</span>
                        Tienes <span class="font-extrabold">{{ $pendingCount }}</span> documento(s) sin contabilizar en este periodo.
                    </div>
                </div>
                <div>
                    <a href="{{ route('vc_documents.pending') }}" class="text-xs font-semibold bg-amber-600 text-white px-3.5 py-2 rounded-lg hover:bg-amber-700 transition shadow-sm whitespace-nowrap">
                        Contabilizar
                    </a>
                </div>
            </div>
        @endif

        <!-- Encabezado de la vista -->
        <div class="text-center mb-8">
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Sistema Contable</h1>
            <p class="text-sm text-slate-500 mt-1">Selecciona una opción para gestionar tu contabilidad</p>
        </div>

        <!-- 3 Botones Principales -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- 1. Ingreso Manual (Agrupa Registros V/C y Asiento Manual) -->
            <a href="{{ route('ingress.manual') }}" class="group p-6 bg-white border border-slate-200 rounded-2xl hover:border-indigo-500 hover:shadow-lg transition text-left flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4 group-hover:bg-indigo-600 group-hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <h2 class="font-bold text-slate-800 group-hover:text-indigo-600 transition text-base mb-1">1. Ingreso Manual</h2>
                    <p class="text-xs text-slate-500 leading-relaxed">Registro individual de documentos V/C y creación de asientos contables.</p>
                </div>
                <span class="text-xs font-semibold text-indigo-600 mt-6 flex items-center">
                    Acceder &rarr;
                </span>
            </a>

            <!-- 2. Importadores (Apunta a ingress.import) -->
            <a href="{{ route('ingress.import') }}" class="group p-6 bg-white border border-slate-200 rounded-2xl hover:border-green-500 hover:shadow-lg transition text-left flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center mb-4 group-hover:bg-green-600 group-hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    </div>
                    <h2 class="font-bold text-slate-800 group-hover:text-green-600 transition text-base mb-1">2. Importador</h2>
                    <p class="text-xs text-slate-500 leading-relaxed">Carga masiva de documentos mediante archivos CSV.</p>
                </div>
                <span class="text-xs font-semibold text-green-600 mt-6 flex items-center">
                    Cargar &rarr;
                </span>
            </a>

            <!-- 3. Reportes y Análisis (Agrupa Reportes, Auditoría, Cobros/Pagos y Asientos del Sistema) -->
            <a href="{{ route('reports.analytics') }}" class="group p-6 bg-white border border-slate-200 rounded-2xl hover:border-purple-500 hover:shadow-lg transition text-left flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mb-4 group-hover:bg-purple-600 group-hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h2 class="font-bold text-slate-800 group-hover:text-purple-600 transition text-base mb-1">3. Reportes y Análisis</h2>
                    <p class="text-xs text-slate-500 leading-relaxed">Balances, auditoría V/C, cobros y pagos y asientos del sistema.</p>
                </div>
                <span class="text-xs font-semibold text-purple-600 mt-6 flex items-center">
                    Ver módulos &rarr;
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