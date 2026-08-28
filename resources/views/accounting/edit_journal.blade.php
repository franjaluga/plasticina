<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Asiento Contable N° {{ $journal->entry_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased min-h-screen py-10">

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Cabecera enriquecida con Folio, RUT y Nombre -->
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white border border-gray-200 rounded-xl p-5 shadow-sm gap-4">
            <div>
                <h1 class="text-xl font-bold text-gray-800 tracking-tight">Editar Asiento Contable N° <span class="text-indigo-600">{{ $journal->entry_number }}</span></h1>
                <div class="text-xs text-gray-600 mt-1.5 space-y-1">
                    <p>
                        Periodo año: <span class="font-bold text-gray-800">{{ $journal->year }}</span> | 
                        Tipo Doc: <span class="font-semibold text-gray-800">{{ $journal->document->documentType->name ?? 'Doc' }}</span> | 
                        Folio: 
                        @if($journal->document)
                            <a href="{{ route('accounting.documents.detail', $journal->document->id) }}" target="_blank" class="font-bold text-indigo-600 hover:underline inline-flex items-center" title="Ver detalle del documento en nueva pestaña">
                                {{ $journal->document->folio }} &nearr;
                            </a>
                        @else
                            <span class="font-bold text-gray-800">N/A</span>
                        @endif
                    </p>
                    <p>
                        Entidad: <span class="font-bold font-mono text-gray-800">{{ $journal->document->entity->rut ?? 'Sin RUT' }}</span> — <span class="font-semibold text-gray-800">{{ $journal->document->entity->name ?? 'Sin Nombre' }}</span>
                    </p>
                </div>
            </div>
            <div>
                <a href="{{ route('accounting.system_journals') }}" class="inline-flex items-center text-xs font-semibold bg-white border border-gray-300 text-gray-700 px-3.5 py-2 rounded-lg shadow-sm hover:bg-gray-50 transition">
                    &larr; Volver
                </a>
            </div>
        </div>

        @if(session('error'))
            <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 text-xs text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('accounting.journals.update', $journal->id) }}" method="POST" class="bg-white shadow-sm border border-gray-200 rounded-xl p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Fecha del Asiento</label>
                    <input type="date" name="date" value="{{ old('date', $journal->date) }}" class="w-full text-xs border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Glosa / Descripción</label>
                    <input type="text" name="description" value="{{ old('description', $journal->description) }}" class="w-full text-xs border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
            </div>

            <div class="flex justify-between items-center mb-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500">Líneas del Asiento Contable</h3>
                <button type="button" id="addRowBtn" class="px-3 py-1.5 bg-gray-800 text-white rounded-lg text-xs font-semibold hover:bg-gray-700 transition shadow-sm">
                    + Agregar Cuenta
                </button>
            </div>
            
            <div class="border border-gray-200 rounded-lg overflow-hidden mb-6">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-800 text-white uppercase tracking-wider text-[11px]">
                            <th class="py-2.5 px-4">Cuenta Contable</th>
                            <th class="py-2.5 px-4 w-32 text-right">Debe</th>
                            <th class="py-2.5 px-4 w-32 text-right">Haber</th>
                            <th class="py-2.5 px-4 w-12 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="entriesTableBody" class="divide-y divide-gray-100">
                        @php
                            $editableEntries = $journal->entries->filter(fn($e) => $e->component_name !== 'total');
                            $devengoEntry = $journal->entries->first(fn($e) => $e->component_name === 'total');
                        @endphp

                        <!-- Filas editables (Impuestos, Gastos, etc.) -->
                        @foreach($editableEntries as $index => $entry)
                            <tr class="entry-row bg-white">
                                <td class="py-3 px-4">
                                    <input type="hidden" name="entries[{{ $index }}][component_name]" value="{{ $entry->component_name ?? 'custom' }}">
                                    <select name="entries[{{ $index }}][account_code]" class="w-full text-xs border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                        @foreach($accounts as $acc)
                                            <option value="{{ $acc->code }}" {{ $entry->account_code == $acc->code ? 'selected' : '' }}>
                                                {{ $acc->code }} - {{ $acc->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <input type="number" step="any" name="entries[{{ $index }}][debit]" value="{{ $entry->debit }}" class="debit-input w-full text-right text-xs border-gray-300 rounded-md shadow-sm" required>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <input type="number" step="any" name="entries[{{ $index }}][credit]" value="{{ $entry->credit }}" class="credit-input w-full text-right text-xs border-gray-300 rounded-md shadow-sm" required>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <button type="button" class="remove-row-btn text-red-500 hover:text-red-700 font-bold text-sm px-2" title="Eliminar fila">&times;</button>
                                </td>
                            </tr>
                        @endforeach

                        <!-- Fila fija del devengo (Siempre al final y no editable) -->
                        @if($devengoEntry)
                            <tr class="entry-row bg-gray-100/90 font-semibold" data-fixed="true">
                                <td class="py-3 px-4">
                                    <input type="hidden" name="entries[devengo][component_name]" value="total">
                                    <input type="hidden" name="entries[devengo][account_code]" value="{{ $devengoEntry->account_code }}">
                                    <div class="font-mono text-gray-700">{{ $devengoEntry->account_code }} - {{ $devengoEntry->account->name ?? '' }}</div>
                                    <span class="text-[10px] text-amber-600 font-semibold">(Devengo - Fijo y no modificable)</span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <input type="number" step="any" name="entries[devengo][debit]" value="{{ $devengoEntry->debit }}" class="debit-input w-full text-right text-xs bg-gray-100 text-gray-500 border-gray-300 rounded-md shadow-sm cursor-not-allowed" readonly required>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <input type="number" step="any" name="entries[devengo][credit]" value="{{ $devengoEntry->credit }}" class="credit-input w-full text-right text-xs bg-gray-100 text-gray-500 border-gray-300 rounded-md shadow-sm cursor-not-allowed" readonly required>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="text-gray-400 text-xs">—</span>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                    <!-- Totales Provisorios UX -->
                    <tfoot>
                        <tr class="bg-gray-50 font-mono text-xs border-t border-gray-200">
                            <td class="py-3 px-4 text-right font-bold text-gray-700 font-sans">
                                <div class="flex items-center justify-end space-x-2">
                                    <span id="balanceIndicatorDot" class="h-3 w-3 rounded-full bg-red-500 inline-block"></span>
                                    <span id="balanceIndicatorText" class="text-[11px] font-bold text-red-600 uppercase">Descuadrado</span>
                                    <span class="ml-2">Totales Provisorios:</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-right font-bold text-indigo-600" id="sumDebit">0</td>
                            <td class="py-3 px-4 text-right font-bold text-indigo-600" id="sumCredit">0</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('accounting.system_journals') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-xs font-semibold shadow-sm hover:bg-indigo-700 transition">
                    Actualizar Asiento
                </button>
            </div>
        </form>

    </div>

    <!-- Select maestro oculto nativo que contiene todas las opciones renderizadas por Blade -->
    <select id="masterAccountSelect" class="hidden">
        <option value="">-- Seleccione una cuenta --</option>
        @foreach($accounts as $acc)
            <option value="{{ $acc->code }}">{{ $acc->code }} - {{ $acc->name }}</option>
        @endforeach
    </select>

    <!-- Script de UX para clonar las opciones nativas de forma garantizada -->
    <script>
        document.getElementById('addRowBtn').addEventListener('click', function() {
            const tbody = document.getElementById('entriesTableBody');
            const fixedRow = tbody.querySelector('tr[data-fixed="true"]');
            const masterSelect = document.getElementById('masterAccountSelect');
            
            const newIndex = 'custom_' + Date.now();

            const newTr = document.createElement('tr');
            newTr.className = 'entry-row bg-white';
            newTr.innerHTML = `
                <td class="py-3 px-4">
                    <input type="hidden" name="entries[\${newIndex}][component_name]" value="custom">
                    <select name="entries[\${newIndex}][account_code]" class="w-full text-xs border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 new-account-select" required>
                    </select>
                </td>
                <td class="py-3 px-4 text-right">
                    <input type="number" step="any" name="entries[\${newIndex}][debit]" value="0" class="debit-input w-full text-right text-xs border-gray-300 rounded-md shadow-sm" required>
                </td>
                <td class="py-3 px-4 text-right">
                    <input type="number" step="any" name="entries[\${newIndex}][credit]" value="0" class="credit-input w-full text-right text-xs border-gray-300 rounded-md shadow-sm" required>
                </td>
                <td class="py-3 px-4 text-center">
                    <button type="button" class="remove-row-btn text-red-500 hover:text-red-700 font-bold text-sm px-2" title="Eliminar fila">&times;</button>
                </td>
            `;

            tbody.insertBefore(newTr, fixedRow);

            const newSelect = newTr.querySelector('.new-account-select');
            newSelect.innerHTML = masterSelect.innerHTML;

            attachEvents();
            calculateTotals();
        });

        function attachEvents() {
            document.querySelectorAll('.remove-row-btn').forEach(btn => {
                btn.onclick = function() {
                    const row = this.closest('tr');
                    if (!row.dataset.fixed) {
                        row.remove();
                        calculateTotals();
                    }
                };
            });

            document.querySelectorAll('.debit-input, .credit-input').forEach(input => {
                input.oninput = calculateTotals;
            });
        }

        function calculateTotals() {
            let totalDebit = 0;
            let totalCredit = 0;

            document.querySelectorAll('.debit-input').forEach(input => {
                totalDebit += parseFloat(input.value) || 0;
            });

            document.querySelectorAll('.credit-input').forEach(input => {
                totalCredit += parseFloat(input.value) || 0;
            });

            document.getElementById('sumDebit').innerText = totalDebit.toLocaleString('es-CL', {minimumFractionDigits: 0, maximumFractionDigits: 2});
            document.getElementById('sumCredit').innerText = totalCredit.toLocaleString('es-CL', {minimumFractionDigits: 0, maximumFractionDigits: 2});

            const dot = document.getElementById('balanceIndicatorDot');
            const text = document.getElementById('balanceIndicatorText');

            if (Math.round(totalDebit * 100) === Math.round(totalCredit * 100) && totalDebit > 0) {
                dot.className = "h-3 w-3 rounded-full bg-green-500 inline-block";
                text.className = "text-[11px] font-bold text-green-600 uppercase";
                text.innerText = "Cuadrado";
            } else {
                dot.className = "h-3 w-3 rounded-full bg-red-500 inline-block";
                text.className = "text-[11px] font-bold text-red-600 uppercase";
                text.innerText = "Descuadrado";
            }
        }

        attachEvents();
        calculateTotals();
    </script>
</body>
</html>