<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Configuración General - Sistema Contable</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900 flex items-center justify-center min-h-screen p-4">
    <div class="max-w-2xl w-full mx-auto p-8 bg-white shadow-xl rounded-2xl border border-slate-100">
        <div class="mb-6">
            <a href="{{ route('welcome') }}" class="inline-flex items-center text-xs font-semibold bg-white text-slate-600 border border-slate-200 px-3 py-2 rounded-lg hover:bg-slate-50 transition shadow-sm">&larr; Volver al Inicio</a>
        </div>
        
        <div class="text-center mb-8">
            <h1 class="text-2xl font-extrabold text-slate-800">Configuración General del Sistema</h1>
            <p class="text-sm text-slate-500 mt-1">Administra los parámetros maestros y estructuras globales</p>
        </div>

        <div class="grid grid-cols-1 gap-4">
            <!-- Botón hacia Gestión de Planes de Cuentas Maestros -->
            <a href="{{ route('masters.account_templates.index') }}" class="flex items-center justify-between p-5 bg-slate-50 hover:bg-amber-50 border border-slate-200 hover:border-amber-300 rounded-xl transition group shadow-sm">
                <div>
                    <h2 class="text-sm font-bold text-slate-800 group-hover:text-amber-800">Planes de Cuentas Maestros</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Gestiona y edita las plantillas base utilizadas para clonar cuentas al crear nuevas empresas.</p>
                </div>
                <span class="text-amber-600 font-bold text-lg group-hover:translate-x-1 transition-transform">&rarr;</span>
            </a>
        </div>
    </div>
</body>
</html>