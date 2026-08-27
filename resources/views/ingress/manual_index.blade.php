<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ingreso Manual - Sistema Contable</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900 flex items-center justify-center min-h-screen p-4">

    <div class="max-w-4xl w-full mx-auto p-8 bg-white shadow-xl rounded-2xl border border-slate-100">
        
        <!-- Enlace para volver -->
        <div class="mb-6">
            <a href="{{ route('welcome') }}" class="inline-flex items-center text-xs font-semibold bg-white text-slate-600 border border-slate-200 px-3 py-2 rounded-lg hover:bg-slate-50 transition shadow-sm">
                &larr; Volver al inicio
            </a>
        </div>

        <!-- Encabezado de la vista -->
        <div class="text-center mb-8">
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Ingreso Manual</h1>
            <p class="text-sm text-slate-500 mt-1">Selecciona una opción de registro</p>
        </div>

        <!-- Grilla de Opciones -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- 1. Registro Individual de Documentos V/C -->
            <a href="{{ route('vc_documents.create') }}" class="group p-6 bg-white border border-slate-200 rounded-2xl hover:border-indigo-500 hover:shadow-lg transition text-left flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4 group-hover:bg-indigo-600 group-hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <h2 class="font-bold text-slate-800 group-hover:text-indigo-600 transition text-base mb-1">Registro V/C</h2>
                    <p class="text-xs text-slate-500 leading-relaxed">Ingresa facturas de compra o venta una a una con validación de cuentas.</p>
                </div>
                <span class="text-xs font-semibold text-indigo-600 mt-6 flex items-center">
                    Ingresar &rarr;
                </span>
            </a>

            <!-- 2. Asientos Contables Manuales -->
            <a href="{{ route('accounting.manual_journals.create') }}" class="group p-6 bg-white border border-slate-200 rounded-2xl hover:border-amber-500 hover:shadow-lg transition text-left flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-4 group-hover:bg-amber-600 group-hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <h2 class="font-bold text-slate-800 group-hover:text-amber-600 transition text-base mb-1">Asiento Manual</h2>
                    <p class="text-xs text-slate-500 leading-relaxed">Crea asientos de partida doble independientes de documentos comerciales.</p>
                </div>
                <span class="text-xs font-semibold text-amber-600 mt-6 flex items-center">
                    Crear &rarr;
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