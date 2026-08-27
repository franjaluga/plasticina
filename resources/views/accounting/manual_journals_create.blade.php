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

    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-300 text-red-800 text-xs rounded">
            <ul class="list-disc pl-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
                <input type="text" name="glosa" value="{{ old('glosa') }}" placeholder="Ej: Registro de provisión de sueldos mes de agosto" class="w-full text-xs p-2 border border-gray-300 bg-white" required>
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

        // Recuperar datos anteriores si existen con old('entries')
        const oldEntries = @json(old('entries'));

        function initEntries() {
            if (oldEntries && Array.isArray(oldEntries) && oldEntries.length > 0) {
                oldEntries.forEach(entry => {
                    addRow(entry.account_code || '', entry.debit || 0, entry.credit || 0);
                });
            } else {
                // Por defecto al menos 2 filas vacías
                addRow('', 0, 0);
                addRow('', 0, 0);
            }
            calculateTotals();
        }

        function addRow(account = '', debit = 0, credit = 0) {
            let tbody = document.getElementById('entries-body');
            let row = document.createElement('tr');
            row.innerHTML = `
                <td class="py-2 px-2">
                    <input type="text" name="entries[${rowIndex}][account_code]" value="${account}" list="accounts_list" placeholder="Seleccione o escriba cuenta" class="w-full text-xs p-1.5 border border-gray-300 font-sans" required autocomplete="off">
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

        // Inicializar al cargar la página
        window.onload = function() {
            initEntries();
        };
    </script>
</body>
</html>