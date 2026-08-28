<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Plan de Cuentas - {{ $owner->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased min-h-screen py-10">

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Cabecera -->
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white border border-slate-200 rounded-2xl p-6 shadow-sm gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg">Gestión de Empresa</span>
                <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight mt-1">Plan de Cuentas: <span class="text-indigo-600">{{ $owner->name }}</span></h1>
                <p class="text-xs text-slate-500 mt-0.5">RUT: <span class="font-semibold text-slate-700">{{ $owner->rut }}</span></p>
            </div>
            <div class="flex items-center space-x-3">
                <button onclick="openCreateModal()" class="inline-flex items-center text-xs font-semibold bg-indigo-600 text-white px-4 py-2.5 rounded-xl shadow-sm hover:bg-indigo-700 transition">
                    + Nueva Cuenta
                </button>
                <a href="{{ route('owners.index') }}" class="inline-flex items-center text-xs font-semibold bg-white border border-slate-300 text-slate-700 px-4 py-2.5 rounded-xl shadow-sm hover:bg-slate-50 transition">
                    &larr; Volver
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 text-xs text-emerald-800 rounded-r-xl shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error') || $errors->any())
            <div class="mb-6 bg-rose-50 border-l-4 border-rose-500 p-4 text-xs text-rose-800 rounded-r-xl shadow-sm">
                {{ session('error') ?? $errors->first() }}
            </div>
        @endif

        <!-- Tabla principal de cuentas limpia y despejada -->
        <div class="bg-white shadow-sm border border-slate-200 rounded-2xl overflow-hidden">
            <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Cuentas Registradas ({{ count($accounts) }})</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-100 text-slate-600 uppercase tracking-wider text-[10px] font-bold">
                            <th class="py-3 px-5 w-36">Código</th>
                            <th class="py-3 px-5">Nombre de la Cuenta</th>
                            <th class="py-3 px-5 w-40">Categoría</th>
                            <th class="py-3 px-5 w-32 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($accounts as $account)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-4 px-5 font-mono font-bold text-slate-700">
                                    {{ $account->code }}
                                </td>
                                <td class="py-4 px-5 font-medium text-slate-800">
                                    {{ $account->name }}
                                </td>
                                <td class="py-4 px-5">
                                    @php
                                        $badgeColor = match($account->category) {
                                            'activo' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'pasivo' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'patrimonio' => 'bg-purple-50 text-purple-700 border-purple-200',
                                            'perdida' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            default => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase border {{ $badgeColor }}">
                                        {{ $account->category }}
                                    </span>
                                </td>
                                <td class="py-4 px-5 text-right space-x-1">
                                    <button onclick="openEditModal('{{ $account->id }}', '{{ $account->code }}', '{{ addslashes($account->name) }}', '{{ $account->category }}')" class="px-2.5 py-1.5 bg-slate-100 text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg font-semibold transition">
                                        Editar
                                    </button>

                                    <form action="{{ route('owners.accounts.destroy', [$owner->id, $account->id]) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de eliminar esta cuenta?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1.5 bg-slate-100 text-rose-600 hover:bg-rose-50 rounded-lg font-semibold transition">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-slate-400">
                                    No hay cuentas registradas para esta empresa.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Modal Unificado para Crear / Editar Cuenta -->
    <div id="accountModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 border border-slate-100">
            <div class="flex justify-between items-center mb-4">
                <h3 id="modalTitle" class="text-sm font-bold text-slate-800">Añadir Nueva Cuenta</h3>
                <button onclick="closeAccountModal()" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
            </div>

            <form id="accountForm" method="POST" class="space-y-4">
                @csrf
                <div id="methodField"></div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Código de Cuenta</label>
                    <input type="text" id="code" name="code" placeholder="Ej: 110101" class="w-full text-xs border-slate-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nombre de la Cuenta</label>
                    <input type="text" id="name" name="name" placeholder="Ej: Caja Chica" class="w-full text-xs border-slate-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Categoría</label>
                    <select id="category" name="category" class="w-full text-xs border-slate-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3 bg-white" required>
                        <option value="activo">Activo</option>
                        <option value="pasivo">Pasivo</option>
                        <option value="patrimonio">Patrimonio</option>
                        <option value="perdida">Pérdida</option>
                        <option value="ganancia">Ganancia</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" onclick="closeAccountModal()" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold hover:bg-slate-200 transition">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold shadow-md hover:bg-indigo-700 transition">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts limpios para controlar el modal de creación y edición -->
    <script>
        const modal = document.getElementById('accountModal');
        const form = document.getElementById('accountForm');
        const modalTitle = document.getElementById('modalTitle');
        const methodField = document.getElementById('methodField');

        function openCreateModal() {
            modalTitle.innerText = "Añadir Nueva Cuenta";
            form.action = "{{ route('owners.accounts.store', $owner->id) }}";
            methodField.innerHTML = ''; // POST por defecto
            
            document.getElementById('code').value = '';
            document.getElementById('name').value = '';
            document.getElementById('category').value = 'activo';

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function openEditModal(id, code, name, category) {
            modalTitle.innerText = "Editar Cuenta Contable";
            
            // Generar la URL utilizando la ruta nombrada de Laravel de forma segura
            let updateUrl = "{{ route('owners.accounts.update', ['owner' => $owner->id, 'account' => ':id']) }}";
            form.action = updateUrl.replace(':id', id);

            methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">'; // Simular PUT

            document.getElementById('code').value = code;
            document.getElementById('name').value = name;
            document.getElementById('category').value = category;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        
        function closeAccountModal() {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    </script>
</body>
</html>