<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Documentos Pendientes de Contabilizar</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased min-h-screen py-10">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Cabecera -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Documentos V/C Pendientes de Contabilización</h2>
                <p class="text-sm text-gray-500 mt-1">Periodo tributario año: <span class="font-bold text-blue-600">{{ session('working_year', date('Y')) }}</span></p>
            </div>
            <div>
                <a href="{{ route('reports.analytics') }}" class="inline-flex items-center text-xs font-semibold bg-white border border-gray-300 text-gray-700 px-3 py-2 rounded-lg shadow-sm hover:bg-gray-50 transition">
                    &larr; Volver
                </a>
            </div>
        </div>

        <!-- Mensajes de éxito o error -->
        @if(session('success'))
            <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 text-green-700 rounded-r shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 text-red-700 rounded-r shadow-sm">
                <ul class="list-disc list-inside mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('vc_documents.batch_contabilizar') }}" method="POST">
            @csrf

            <!-- Selector / Input ComboBox dinámico para la Cuenta del Neto -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                    <div>
                        <label for="custom_net_account" class="block text-sm font-bold text-gray-700 mb-1">
                            Cuenta Contable para el Neto <span class="text-xs font-normal text-gray-400">(Seleccione o escriba el código)</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="custom_net_account" id="custom_net_account" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none" list="accounts_list" placeholder="Ej: Seleccione o escriba código" autocomplete="off" required>
                            
                            <!-- Datalist poblado dinámicamente desde el modelo Account -->
                            <datalist id="accounts_list">
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->code }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                @endforeach
                            </datalist>
                        </div>
                    </div>
                    <div class="text-md-end md:text-right mt-3 md:mt-0">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2 rounded-lg shadow transition">
                            Contabilizar Seleccionados
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabla de documentos -->
            <div class="bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-left">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th scope="col" class="w-12 px-4 py-3 text-center">
                                <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th scope="col" class="px-6 py-3 text-xs font-bold uppercase tracking-wider">Folio</th>
                            <th scope="col" class="px-6 py-3 text-xs font-bold uppercase tracking-wider">Tipo V/C</th>
                            <th scope="col" class="px-6 py-3 text-xs font-bold uppercase tracking-wider">Fecha Doc.</th>
                            <th scope="col" class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-right">Neto</th>
                            <th scope="col" class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-right">IVA Rec.</th>
                            <th scope="col" class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($documents as $doc)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-center">
                                    <input type="checkbox" name="document_ids[]" value="{{ $doc->id }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 doc-checkbox">
                                </td>
                                <td class="px-6 py-3 font-bold text-gray-900">{{ $doc->folio }}</td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $doc->type_vc === 'V' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $doc->type_vc === 'V' ? 'Venta' : 'Compra' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ $doc->date }}</td>
                                <td class="px-6 py-3 text-sm text-right text-gray-600">{{ number_format($doc->net, 0, ',', '.') }}</td>
                                <td class="px-6 py-3 text-sm text-right text-gray-600">{{ number_format($doc->vat_rec, 0, ',', '.') }}</td>
                                <td class="px-6 py-3 text-sm text-right font-bold text-gray-900">{{ number_format($doc->total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                                    No hay documentos pendientes de contabilizar para el año {{ session('working_year', date('Y')) }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

    </div>

    <script>
        document.getElementById('select-all').addEventListener('change', function() {
            let checkboxes = document.querySelectorAll('.doc-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    </script>
</body>
</html>