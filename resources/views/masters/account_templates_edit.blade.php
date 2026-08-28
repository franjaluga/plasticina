<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Plan: {{ $accountTemplate->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased min-h-screen py-10">

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Editar Plan: <span class="text-indigo-600">{{ $accountTemplate->name }}</span></h1>
            <a href="{{ route('masters.account_templates.index') }}" class="inline-flex items-center text-xs font-semibold bg-white border border-slate-300 text-slate-700 px-3.5 py-2 rounded-lg shadow-sm hover:bg-slate-50 transition">&larr; Volver</a>
        </div>

        <form action="{{ route('masters.account_templates.update', $accountTemplate->id) }}" method="POST" class="bg-white shadow-sm border border-slate-200 rounded-xl p-6">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 pb-4 border-b border-slate-100">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nombre del Plan</label>
                    <input type="text" name="name" value="{{ $accountTemplate->name }}" class="w-full text-xs border-slate-300 rounded-lg shadow-sm" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Descripción</label>
                    <input type="text" name="description" value="{{ $accountTemplate->description }}" class="w-full text-xs border-slate-300 rounded-lg shadow-sm">
                </div>
            </div>

            <div class="flex justify-between items-center mb-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Elementos / Cuentas del Plan</h3>
                <button type="button" id="addRow" class="px-3 py-1.5 bg-slate-800 text-white rounded-lg text-xs font-semibold hover:bg-slate-700 transition">+ Añadir Cuenta</button>
            </div>

            <div class="border border-slate-200 rounded-lg overflow-hidden mb-6 max-h-[500px] overflow-y-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead class="bg-slate-800 text-white uppercase tracking-wider text-[11px] sticky top-0">
                        <tr>
                            <th class="py-2.5 px-4 w-36">Código</th>
                            <th class="py-2.5 px-4">Nombre de la Cuenta</th>
                            <th class="py-2.5 px-4 w-40">Categoría</th>
                            <th class="py-2.5 px-4 w-12 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="accountsTableBody" class="divide-y divide-slate-100">
                        @foreach($accountTemplate->items as $item)
                        <tr class="bg-white">
                            <td class="py-2 px-4"><input type="text" name="items[{{ $item->id }}][code]" value="{{ $item->code }}" class="w-full text-xs border-slate-300 rounded-md" required></td>
                            <td class="py-2 px-4"><input type="text" name="items[{{ $item->id }}][name]" value="{{ $item->name }}" class="w-full text-xs border-slate-300 rounded-md" required></td>
                            <td class="py-2 px-4">
                                <select name="items[{{ $item->id }}][category]" class="w-full text-xs border-slate-300 rounded-md" required>
                                    <option value="activo" {{ $item->category == 'activo' ? 'selected' : '' }}>Activo</option>
                                    <option value="pasivo" {{ $item->category == 'pasivo' ? 'selected' : '' }}>Pasivo</option>
                                    <option value="patrimonio" {{ $item->category == 'patrimonio' ? 'selected' : '' }}>Patrimonio</option>
                                    <option value="ganancia" {{ $item->category == 'ganancia' ? 'selected' : '' }}>Ganancia</option>
                                    <option value="perdida" {{ $item->category == 'perdida' ? 'selected' : '' }}>Pérdida</option>
                                </select>
                            </td>
                            <td class="py-2 px-4 text-center"><button type="button" onclick="this.closest('tr').remove()" class="text-rose-600 font-bold text-base">&times;</button></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-xs font-semibold shadow-sm hover:bg-indigo-700 transition">Guardar Cambios del Plan</button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('addRow').addEventListener('click', function() {
            const tbody = document.getElementById('accountsTableBody');
            const newId = 'new_' + Date.now();
            const tr = document.createElement('tr');
            tr.className = 'bg-white';
            tr.innerHTML = `
                <td class="py-2 px-4"><input type="text" name="items[\${newId}][code]" class="w-full text-xs border-slate-300 rounded-md" required placeholder="Ej: 1010199"></td>
                <td class="py-2 px-4"><input type="text" name="items[\${newId}][name]" class="w-full text-xs border-slate-300 rounded-md" required placeholder="Nombre de cuenta"></td>
                <td class="py-2 px-4">
                    <select name="items[\${newId}][category]" class="w-full text-xs border-slate-300 rounded-md" required>
                        <option value="activo">Activo</option>
                        <option value="pasivo">Pasivo</option>
                        <option value="patrimonio">Patrimonio</option>
                        <option value="ganancia">Ganancia</option>
                        <option value="perdida">Pérdida</option>
                    </select>
                </td>
                <td class="py-2 px-4 text-center"><button type="button" onclick="this.closest('tr').remove()" class="text-rose-600 font-bold text-base">&times;</button></td>
            `;
            tbody.appendChild(tr);
        });
    </script>
</body>
</html>