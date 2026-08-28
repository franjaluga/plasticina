<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Owners</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased min-h-screen py-10">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Cabecera -->
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Gestión de Owners</h1>
            <a href="{{ route('reports.analytics') }}" class="inline-flex items-center text-xs font-semibold bg-white border border-slate-300 text-slate-700 px-3.5 py-2 rounded-lg shadow-sm hover:bg-slate-50 transition">
                &larr; Volver
            </a>
        </div>

        <!-- Alertas de éxito y error -->
        @if(session('success'))
            <div class="mb-4 bg-emerald-50 border-l-4 border-emerald-500 p-4 text-xs text-emerald-800 rounded-r-lg shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-rose-50 border-l-4 border-rose-500 p-4 text-xs text-rose-800 rounded-r-lg shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        <!-- Indicador del Service -->
        <div class="mb-6 bg-sky-50 border border-sky-200 text-sky-800 p-4 rounded-xl text-xs font-medium shadow-sm flex items-center justify-between">
            <div>
                <span class="font-bold uppercase tracking-wider text-sky-900">Owner Activo Actual:</span> 
                <span class="font-semibold text-sky-950 ml-1">{{ $activeOwner ? $activeOwner->name . ' (' . $activeOwner->rut . ')' : 'Ninguno activo' }}</span>
            </div>
        </div>

        <!-- Formulario de Creación con Selección de Plan de Cuentas -->
        <div class="bg-white shadow-sm border border-slate-200 rounded-xl p-6 mb-6">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-4">Agregar Nuevo Owner y Plan de Cuentas Inicial</h2>
            <form action="{{ route('owners.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-slate-700 mb-1">RUT</label>
                        <input type="text" name="rut" value="{{ old('rut') }}" class="w-full text-xs border-slate-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required maxlength="10" placeholder="Ej: 12345678-9">
                    </div>
                    <div class="md:col-span-4">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nombre / Empresa</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="w-full text-xs border-slate-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required maxlength="100">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Plan de Cuentas Inicial</label>
                        <select name="account_plan_type" class="w-full text-xs border-slate-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="standard_pyme" {{ old('account_plan_type') == 'standard_pyme' ? 'selected' : '' }}>Plan Estándar PyME (Base)</option>
                            @foreach($templates as $tpl)
                                <option value="{{ $tpl->slug }}" {{ old('account_plan_type') == $tpl->slug ? 'selected' : '' }}>
                                    {{ $tpl->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <button type="submit" class="w-full py-2 bg-indigo-600 text-white rounded-lg text-xs font-semibold shadow-sm hover:bg-indigo-700 transition">
                            Guardar
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Listado -->
        <div class="bg-white shadow-sm border border-slate-200 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-600">Listado de Owners</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-800 text-white uppercase tracking-wider text-[11px]">
                            <th class="py-2.5 px-4">ID</th>
                            <th class="py-2.5 px-4">RUT</th>
                            <th class="py-2.5 px-4">Nombre</th>
                            <th class="py-2.5 px-4">Plan Asignado</th>
                            <th class="py-2.5 px-4 text-center">Estado</th>
                            <th class="py-2.5 px-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($owners as $owner)
                        @php
                            // Resolver nombre amigable del plan asignado
                            if ($owner->account_plan_type == 'standard_pyme') {
                                $planName = 'Plan Estándar PyME';
                            } else {
                                $matchedTpl = $templates->where('slug', $owner->account_plan_type)->first();
                                $planName = $matchedTpl ? $matchedTpl->name : $owner->account_plan_type;
                            }
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-bold text-slate-700">{{ $owner->id }}</td>
                            <td class="py-3 px-4">{{ $owner->rut }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-800">{{ $owner->name }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-md text-[11px] font-semibold border border-indigo-200">
                                    {{ $planName }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($owner->is_active)
                                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-md text-[11px] font-semibold border border-emerald-200">Activo</span>
                                @else
                                    <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-md text-[11px] font-semibold border border-slate-200">Inactivo</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center space-x-1 whitespace-nowrap">
                                <!-- Botón Activar -->
                                @unless($owner->is_active)
                                    <form action="{{ route('owners.activate', $owner) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2.5 py-1 bg-emerald-600 text-white rounded-md text-xs font-semibold hover:bg-emerald-700 transition shadow-sm">Activar</button>
                                    </form>
                                @endunless

                                <!-- Botón Abrir Modal de Edición -->
                                <button onclick="toggleModal('editModal{{ $owner->id }}')" class="px-2.5 py-1 bg-amber-500 text-white rounded-md text-xs font-semibold hover:bg-amber-600 transition shadow-sm">Editar</button>
                            </td>
                        </tr>

                        <!-- Modal de Edición Tailwind puro -->
                        <div id="editModal{{ $owner->id }}" class="fixed inset-0 z-50 hidden bg-slate-900/50 flex items-center justify-center p-4">
                            <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 border border-slate-200">
                                <div class="flex justify-between items-center mb-4 pb-2 border-b border-slate-100">
                                    <h3 class="text-sm font-bold text-slate-800">Editar Owner</h3>
                                    <button onclick="toggleModal('editModal{{ $owner->id }}')" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                                </div>
                                <form action="{{ route('owners.update', $owner) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="space-y-4 mb-5">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">RUT</label>
                                            <input type="text" name="rut" value="{{ $owner->rut }}" class="w-full text-xs border-slate-300 rounded-lg shadow-sm" required maxlength="10">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Nombre</label>
                                            <input type="text" name="name" value="{{ $owner->name }}" class="w-full text-xs border-slate-300 rounded-lg shadow-sm" required maxlength="100">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 mb-1">Plan de Cuentas (Inalterable)</label>
                                            <input type="text" value="{{ $planName }}" class="w-full text-xs border-slate-200 bg-slate-100 text-slate-500 rounded-lg shadow-sm cursor-not-allowed" disabled>
                                            <p class="text-[11px] text-slate-400 mt-1">El plan de cuentas no se puede modificar una vez creado el owner para proteger la compatibilidad contable.</p>
                                        </div>
                                    </div>
                                    <div class="flex justify-end gap-2">
                                        <button type="button" onclick="toggleModal('editModal{{ $owner->id }}')" class="px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">Cancelar</button>
                                        <button type="submit" class="px-4 py-1.5 bg-indigo-600 text-white rounded-lg text-xs font-semibold hover:bg-indigo-700 transition shadow-sm">Actualizar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-400">No hay empresas registradas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Script sencillo para abrir/cerrar modales con Tailwind -->
    <script>
        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
            } else {
                modal.classList.add('hidden');
            }
        }
    </script>
</body>
</html>