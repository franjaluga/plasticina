<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Empresa - {{ $owner->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased min-h-screen py-10">

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Cabecera y Botón Volver -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <span class="text-xs font-semibold text-indigo-600 uppercase tracking-wider block">Ficha de Empresa</span>
                <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $owner->name }}</h1>
            </div>
            <a href="{{ route('owners.index') }}" class="inline-flex items-center text-xs font-semibold bg-white border border-slate-300 text-slate-700 px-3.5 py-2 rounded-lg shadow-sm hover:bg-slate-50 transition">
                &larr; Volver al Listado
            </a>
        </div>

        <!-- Alertas de éxito -->
        @if(session('success'))
            <div class="mb-4 bg-emerald-50 border-l-4 border-emerald-500 p-4 text-xs text-emerald-800 rounded-r-lg shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Tarjeta de Datos Principales -->
        <div class="bg-white shadow-sm border border-slate-200 rounded-xl p-6 mb-6">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4 pb-2 border-b border-slate-100">Información General</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
                <div>
                    <span class="block font-semibold text-slate-500">RUT de la Empresa</span>
                    <span class="text-sm font-bold text-slate-800 mt-0.5 block">{{ $owner->rut }}</span>
                </div>
                <div>
                    <span class="block font-semibold text-slate-500">Estado Actual</span>
                    <span class="mt-0.5 inline-block">
                        @if($owner->is_active)
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-md text-[11px] font-semibold border border-emerald-200">Activo (Seleccionado)</span>
                        @else
                            <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-md text-[11px] font-semibold border border-slate-200">Inactivo</span>
                        @endif
                    </span>
                </div>
                <div>
                    <span class="block font-semibold text-slate-500">Plan de Cuentas Configurado</span>
                    <span class="text-sm font-bold text-slate-800 mt-0.5 block uppercase tracking-wide">
                        {{ $owner->account_plan_type }}
                    </span>
                </div>
                <div>
                    <span class="block font-semibold text-slate-500">Cuentas Contables Registradas</span>
                    <span class="text-sm font-bold text-slate-800 mt-0.5 block">
                        {{ $owner->accounts_count }} cuentas asociadas
                    </span>
                </div>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="bg-indigo-900 text-white rounded-xl p-6 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <h3 class="text-sm font-bold">Gestión de Plan de Cuentas</h3>
                <p class="text-xs text-indigo-200 mt-0.5">Puedes revisar, modificar o agregar cuentas específicas para esta empresa.</p>
            </div>
            <div>
                <a href="{{ route('owners.accounts.index', $owner->id) }}" class="px-4 py-2.5 bg-white text-indigo-900 font-bold rounded-lg text-xs hover:bg-indigo-50 transition shadow-sm whitespace-nowrap">
                    Administrar Cuentas &rarr;
                </a>
            </div>
        </div>

    </div>

</body>
</html>