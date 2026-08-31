<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema Contable</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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

            <!-- Selector de Año de Trabajo (Ocupa 1 columna con bloqueo/edición) -->
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex flex-col justify-between">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Año de Trabajo</span>
                    <!-- Botón para habilitar/desbloquear la edición -->
                    <button type="button" id="editYearBtn" onclick="toggleYearEdition()" class="text-[11px] font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-2 py-0.5 rounded transition">
                        Cambiar ejercicio contable
                    </button>
                </div>

                <form action="{{ route('period.update') }}" method="POST" class="flex items-center space-x-2">
                    @csrf
                    <!-- Select bloqueado por defecto -->
                    <select name="working_year" id="workingYearSelect" disabled class="w-full text-sm font-bold text-slate-800 bg-slate-100 border border-slate-300 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-not-allowed shadow-sm transition">
                        @php
                            $currentYearSelected = session('working_year', date('Y'));
                        @endphp
                        @for($y = date('Y'); $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ $currentYearSelected == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>

                    <!-- Botón Guardar oculto por defecto -->
                    <button type="submit" id="saveYearBtn" class="hidden text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg shadow-sm transition whitespace-nowrap">
                        Guardar
                    </button>
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
            
            <!-- 1. Ingreso Manual -->
            <div class="group p-6 bg-white border border-slate-200 rounded-2xl hover:border-indigo-500 hover:shadow-lg transition text-left flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4 group-hover:bg-indigo-600 group-hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <h2 class="font-bold text-slate-800 group-hover:text-indigo-600 transition text-base mb-1">1. Ingreso Manual</h2>
                    <p class="text-xs text-slate-500 leading-relaxed mb-4">Registro individual de documentos V/C y creación de asientos contables.</p>
                </div>
                
                <div class="space-y-2">
                    <a href="{{ route('ingress.manual') }}" class="w-full text-xs font-semibold bg-indigo-50 text-indigo-700 p-2.5 rounded-lg hover:bg-indigo-600 hover:text-white transition flex items-center justify-between">
                        <span>Menú Ingreso Manual</span> &rarr;
                    </a>
                    <a href="{{ route('accounting.manual_journals.create') }}" class="w-full text-xs font-semibold bg-slate-900 text-white p-2.5 rounded-lg hover:bg-slate-800 transition flex items-center justify-between">
                        <span>+ Nuevo Asiento Directo</span> &rarr;
                    </a>
                </div>
            </div>

            <!-- 2. Importadores -->
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

            <!-- 3. Reportes y Análisis -->
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

        <!-- Footer y Configuración General -->
        <div class="mt-8 pt-4 border-t border-slate-200 flex flex-col sm:flex-row justify-between items-center gap-3 text-xs text-slate-400">
            <p>Sistema Contable v1.0</p>
            
            <div class="flex items-center space-x-4">
                <a href="{{ route('system.config') }}" class="inline-flex items-center space-x-1 font-semibold text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-lg transition">
                    <span>⚙️ Configuración General</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Script para alternar el estado de edición del Año de Trabajo -->
    <script>
        function toggleYearEdition() {
            const selectElement = document.getElementById('workingYearSelect');
            const editBtn = document.getElementById('editYearBtn');
            const saveBtn = document.getElementById('saveYearBtn');

            if (selectElement.hasAttribute('disabled')) {
                // Desbloquear campo
                selectElement.removeAttribute('disabled');
                selectElement.classList.remove('bg-slate-100', 'cursor-not-allowed');
                selectElement.classList.add('bg-white', 'cursor-pointer', 'border-indigo-500');
                
                // Cambiar botón Editar a Cancelar
                editBtn.textContent = 'Cancelar';
                editBtn.classList.remove('text-indigo-600', 'bg-indigo-50');
                editBtn.classList.add('text-rose-600', 'bg-rose-50');

                // Mostrar botón Guardar
                saveBtn.classList.remove('hidden');
            } else {
                // Volver a bloquear campo (Cancelar acción)
                selectElement.setAttribute('disabled', 'disabled');
                selectElement.classList.add('bg-slate-100', 'cursor-not-allowed');
                selectElement.classList.remove('bg-white', 'cursor-pointer', 'border-indigo-500');
                
                // Restaurar botón Editar
                editBtn.textContent = 'Editar';
                editBtn.classList.add('text-indigo-600', 'bg-indigo-50');
                editBtn.classList.remove('text-rose-600', 'bg-rose-50');

                // Ocultar botón Guardar
                saveBtn.classList.add('hidden');
            }
        }
    </script>
</body>
</html>