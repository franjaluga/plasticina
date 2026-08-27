<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nuevo Asiento Contable Manual</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-black font-sans p-6 max-w-4xl mx-auto">

    <!-- Cabecera -->
    <div class="flex justify-between items-baseline pb-3 mb-6 border-b border-gray-300">
        <div>
            <h1 class="text-base font-bold uppercase tracking-wider">Nuevo Asiento Contable Manual</h1>
            <p class="text-xs text-gray-600 font-mono mt-0.5">Registro de comprobante directo</p>
        </div>
        <div>
            <a href="{{ route('ingress.manual') }}" class="text-xs font-medium text-black border border-gray-300 px-2.5 py-1 rounded">
                &larr; Volver
            </a>
        </div>
    </div>

    <!-- Indicador del Owner Activo -->
    <div class="mb-6 p-4 bg-indigo-50/60 border border-indigo-100 rounded-xl flex items-center justify-between">
        <div class="text-left flex items-center space-x-3">
            <div class="p-2 bg-indigo-600 text-white rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </div>
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-indigo-600 block">Owner Activo</span>
                <span class="text-sm font-bold text-slate-800">
                    @if(isset($activeOwner) && $activeOwner)
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

    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-300 text-red-800 text-xs rounded">
            <ul class="list-disc pl-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- SECCIÓN DE ASIENTOS PREFABRICADOS / PLANTILLAS -->
    <div class="mb-6 p-4 bg-indigo-50/50 border border-indigo-100 rounded">
        <h2 class="text-xs font-bold uppercase text-indigo-900 mb-2">Asientos Prefabricados (Plantillas Rápidas)</h2>
        <div class="flex flex-wrap gap-2">
            @foreach($templates as $key => $template)
                <button type="button" 
                        onclick="loadTemplate('{{ $key }}')" 
                        class="text-xs bg-white border border-indigo-300 text-indigo-700 px-3 py-1.5 rounded font-semibold hover:bg-indigo-600 hover:text-white transition shadow-sm">
                   + {{ $template['name'] ?? $template['title'] ?? ucfirst(str_replace('_', ' ', $key)) }}
                </button>
            @endforeach
        </div>
    </div>

    <form action="{{ route('accounting.manual_journals.store') }}" method="POST">
        @csrf

        <!-- Datos Generales -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6 p-4 bg-gray-50 border border-gray-200 rounded">
            <div>
                <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Fecha</label>
                <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" class="w-full text-xs p-2 border border-gray-300 bg-white" required>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Mes</label>
                <select name="month" class="w-full text-xs p-2 border border-gray-300 bg-white" required>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ old('month', date('n')) == $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Año</label>
                <input type="number" name="year" value="{{ old('year', $workingYear) }}" class="w-full text-xs p-2 border border-gray-300 bg-white" required>
            </div>
            <div class="sm:col-span-4">
                <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Glosa / Descripción General</label>
                <input type="text" id="glosa-input" name="glosa" value="{{ old('glosa') }}" placeholder="Ej: Registro de provisión de sueldos mes de agosto" class="w-full text-xs p-2 border border-gray-300 bg-white" required>
            </div>
        </div>

        <!-- Líneas de Detalle (Debe / Haber) -->
        <h2 class="text-xs font-bold uppercase tracking-wider mb-2 text-gray-700">Detalle de Cuentas</h2>
        
        <table class="w-full text-left border-collapse text-xs mb-4" id="entries-table">
            <thead>
                <tr class="border-b-2 border-black text-[11px] font-bold uppercase text-gray-700">
                    <th class="py-2 px-2">Cuenta Contable</th>
                    <th class="py-2 px-2 text-right w-36">Debe</th>
                    <th class="py-2 px-2 text-right w-36">Haber</th>
                    <th class="py-2 px-2 text-center w-12">Acción</th>
                </tr>
            </thead>
            <tbody id="entries-body" class="font-mono text-[11px] divide-y divide-gray-200">
                <!-- Se poblará vía PHP/JS según old() o por defecto 2 filas -->
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-black font-bold font-mono text-[11px]">
                    <td class="py-2 px-2 text-right uppercase font-sans">TOTALES:</td>
                    <td class="py-2 px-2 text-right" id="total-debit">0</td>
                    <td class="py-2 px-2 text-right" id="total-credit">0</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <div class="flex justify-between items-center mb-6">
            <button type="button" onclick="addRow()" class="text-xs bg-gray-100 border border-gray-300 px-3 py-1.5 font-semibold text-black hover:bg-gray-200">
                + Agregar línea
            </button>
            <button type="submit" class="text-xs bg-black text-white px-4 py-2 font-bold uppercase tracking-wider">
                Guardar Asiento
            </button>
        </div>

        <!-- Datalist compartido de cuentas -->
        <datalist id="accounts_list">
            @foreach($accounts as $acc)
                <option value="{{ $acc->code }}">{{ $acc->name }}</option>
            @endforeach
        </datalist>
    </form>

    <script>
        let rowIndex = 0;

        // Inyectar plantillas y diccionario de cuentas de forma segura desde PHP
        const templatesData = @json($templates);
        const oldEntries = @json(old('entries'));
        
        // Crear un mapa de código => nombre para lectura rápida
        const accountsMap = {
            @foreach($accounts as $acc)
                "{{ $acc->code }}": "{{ $acc->name }}",
            @endforeach
        };

        function initEntries() {
            if (oldEntries && Array.isArray(oldEntries) && oldEntries.length > 0) {
                oldEntries.forEach(entry => {
                    addRow(entry.account_code || '', entry.debit || 0, entry.credit || 0);
                });
            } else {
                addRow('', 0, 0);
                addRow('', 0, 0);
            }
            calculateTotals();
        }

        function addRow(account = '', debit = 0, credit = 0) {
            let tbody = document.getElementById('entries-body');
            let row = document.createElement('tr');
            
            // Obtener el nombre inicial si viene con valor (ej. desde old())
            let initialName = accountsMap[account] || 'Seleccione o escriba cuenta';

            row.innerHTML = `
                <td class="py-2 px-2">
                    <div class="flex items-center space-x-2">
                        <input type="text" name="entries[${rowIndex}][account_code]" value="${account}" list="accounts_list" placeholder="Código" class="w-28 text-xs p-1.5 border border-gray-300 font-sans account-input" oninput="updateAccountName(this)" required autocomplete="off">
                        <span class="text-gray-600 text-[11px] font-sans truncate account-name-label" style="max-width: 220px;">${initialName}</span>
                    </div>
                </td>
                <td class="py-2 px-2 text-right">
                    <input type="number" step="any" name="entries[${rowIndex}][debit]" value="${debit}" class="w-full text-xs p-1.5 border border-gray-300 text-right debit-input" oninput="calculateTotals()" required>
                </td>
                <td class="py-2 px-2 text-right">
                    <input type="number" step="any" name="entries[${rowIndex}][credit]" value="${credit}" class="w-full text-xs p-1.5 border border-gray-300 text-right credit-input" oninput="calculateTotals()" required>
                </td>
                <td class="py-2 px-2 text-center">
                    <button type="button" onclick="removeRow(this)" class="text-red-600 font-bold px-2 py-1">&times;</button>
                </td>
            `;
            tbody.appendChild(row);
            rowIndex++;
            calculateTotals();
        }

        // Función para actualizar dinámicamente el nombre de la cuenta en tiempo de lectura al escribir o seleccionar
        function updateAccountName(input) {
            let code = input.value.trim();
            let label = input.closest('td').querySelector('.account-name-label');
            
            if (accountsMap[code]) {
                label.innerText = accountsMap[code];
                label.classList.remove('text-gray-400', 'italic');
                label.classList.add('text-gray-800', 'font-medium');
            } else {
                label.innerText = 'Cuenta no encontrada';
                label.classList.remove('text-gray-800', 'font-medium');
                label.classList.add('text-gray-400', 'italic');
            }
        }

        function removeRow(btn) {
            let row = btn.closest('tr');
            if(document.querySelectorAll('#entries-body tr').length > 2) {
                row.remove();
                calculateTotals();
            } else {
                alert('El asiento debe tener al menos dos líneas.');
            }
        }

        function calculateTotals() {
            let debits = document.querySelectorAll('.debit-input');
            let credits = document.querySelectorAll('.credit-input');
            let totalDebit = 0;
            let totalCredit = 0;

            debits.forEach(input => totalDebit += parseFloat(input.value) || 0);
            credits.forEach(input => totalCredit += parseFloat(input.value) || 0);

            document.getElementById('total-debit').innerText = totalDebit.toLocaleString('es-CL');
            document.getElementById('total-credit').innerText = totalCredit.toLocaleString('es-CL');
        }

        function loadTemplate(templateKey) {
            let template = templatesData[templateKey];
            if (!template) return;

            let amountInput = prompt("Ingrese el monto para la transacción (" + template.name + "):");
            if (amountInput === null) return; // Cancelado

            let amount = parseFloat(amountInput);
            if (isNaN(amount) || amount <= 0) {
                alert("Por favor, ingrese un monto válido mayor a 0.");
                return;
            }

            // Asignar glosa sugerida por la plantilla
            document.getElementById('glosa-input').value = template.glosa;

            // Limpiar tabla actual
            let tbody = document.getElementById('entries-body');
            tbody.innerHTML = '';
            rowIndex = 0;

            // Recorrer dinámicamente las líneas configuradas en la plantilla
            template.entries.forEach(entry => {
                let debitValue = entry.debit_type === 'amount' ? amount : 0;
                let creditValue = entry.credit_type === 'amount' ? amount : 0;
                
                addRow(entry.account_code, debitValue, creditValue);
            });

            calculateTotals();
        }

        window.onload = function() {
            initEntries();
        };
    </script>
</body>
</html>