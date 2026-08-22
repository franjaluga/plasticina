<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Documentos V/C</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900 flex items-center justify-center min-h-screen p-4">

    <div class="max-w-2xl w-full mx-auto p-8 bg-white shadow-xl rounded-2xl border border-slate-100">
        
        <!-- Indicador del Owner Activo -->
        <div class="mb-6 p-4 bg-indigo-50/60 border border-indigo-100 rounded-xl flex items-center justify-between">
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

        <!-- Alerta de Documentos Pendientes (Solo aparece si hay pendientes) -->
        @if(isset($pendingCount) && $pendingCount > 0)
            <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-center justify-between text-amber-900">
                <div class="text-left text-sm flex items-center space-x-3">
                    <div class="p-2 bg-amber-500 text-white rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <span class="font-bold block">¡Atención!</span>
                        Tienes <span class="font-extrabold">{{ $pendingCount }}</span> documento(s) sin contabilizar.
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
        <div class="text-center mb-6">
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Sistema de Documentos V/C</h1>
            <p class="text-sm text-slate-500 mt-1">Selecciona una opción para gestionar tus registros de ventas y compras</p>
        </div>

        <!-- Grid de Cards de Acciones -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            
            <!-- Card 1: Ingresar Nuevo Documento -->
            <a href="{{ route('vc_documents.create') }}" class="group p-5 bg-white border border-slate-200 rounded-xl hover:border-indigo-500 hover:shadow-md transition text-left flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center mb-3 group-hover:bg-indigo-600 group-hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <h2 class="font-bold text-slate-800 group-hover:text-indigo-600 transition text-base">Nuevo Documento</h2>
                    <p class="text-xs text-slate-500 mt-1">Registra manualmente una venta o compra individual.</p>
                </div>
                <span class="text-xs font-semibold text-indigo-600 mt-4 flex items-center">
                    Ingresar &rarr;
                </span>
            </a>

            <!-- Card 2: Ingresar vía importador -->
            <a href="{{ route('vc_documents.upload') }}" class="group p-5 bg-white border border-slate-200 rounded-xl hover:border-indigo-500 hover:shadow-md transition text-left flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center mb-3 group-hover:bg-indigo-600 group-hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    </div>
                    <h2 class="font-bold text-slate-800 group-hover:text-indigo-600 transition text-base">Importar Masivo</h2>
                    <p class="text-xs text-slate-500 mt-1">Carga múltiples documentos a través de archivos.</p>
                </div>
                <span class="text-xs font-semibold text-indigo-600 mt-4 flex items-center">
                    Cargar archivo &rarr;
                </span>
            </a>

            <!-- Card 3: Ver Libro Diario -->
            <a href="{{ route('vc_documents.journal_book') }}" class="group p-5 bg-white border border-slate-200 rounded-xl hover:border-emerald-500 hover:shadow-md transition text-left flex flex-col justify-between sm:col-span-2">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3 group-hover:bg-emerald-600 group-hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <h2 class="font-bold text-slate-800 group-hover:text-emerald-600 transition text-base">Ver Libro Diario</h2>
                        <p class="text-xs text-slate-500 mt-1">Consulta los movimientos contables registrados y asientos generados.</p>
                    </div>
                    <span class="text-xs font-semibold text-emerald-600 flex items-center">
                        Consultar &rarr;
                    </span>
                </div>
            </a>

        </div>

        <!-- Footer -->
        <div class="mt-8 pt-4 border-t border-slate-200 text-xs text-slate-400 flex justify-between items-center">
            <p>Sistema en proceso de desarrollo</p>
        </div>
    </div>

</body>
</html>