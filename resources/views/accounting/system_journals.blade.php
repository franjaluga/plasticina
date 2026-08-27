<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Asientos Contables</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased min-h-screen py-10">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Cabecera -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Asientos Contables del Sistema</h2>
                <p class="text-sm text-gray-500 mt-1">Periodo tributario año: <span class="font-bold text-blue-600">{{ $year }}</span></p>
            </div>
            <div>
                <a href="{{ route('reports.journal_context') }}" class="inline-flex items-center text-xs font-semibold bg-white border border-gray-300 text-gray-700 px-3 py-2 rounded-lg shadow-sm hover:bg-gray-50 transition">
                    &larr; Volver
                </a>
            </div>
        </div>

        <!-- Mensajes de éxito o error -->
        @if(session('success'))
            <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 text-green-700 rounded-r shadow-sm text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 text-red-700 rounded-r shadow-sm text-sm">
                {{ session('error') }}
            </div>
        @endif

        <!-- BLOQUE DE FILTROS -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-700 mb-4">Filtros de Búsqueda</h3>
            <form action="{{ route('accounting.system_journals') }}" method="GET">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <!-- Rango de Asientos -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">N° Asiento (Desde - Hasta)</label>
                        <div class="flex space-x-2">
                            <input type="number" name="entry_from" class="w-1/2 text-xs border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Desde" value="{{ $filters['entry_from'] ?? '' }}">
                            <input type="number" name="entry_to" class="w-1/2 text-xs border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Hasta" value="{{ $filters['entry_to'] ?? '' }}">
                        </div>
                    </div>

                    <!-- Rango de Fecha de Centralización -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Fecha Centralización (Desde - Hasta)</label>
                        <div class="flex space-x-2">
                            <input type="date" name="date_from" class="w-1/2 text-xs border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none" value="{{ $filters['date_from'] ?? '' }}">
                            <input type="date" name="date_to" class="w-1/2 text-xs border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none" value="{{ $filters['date_to'] ?? '' }}">
                        </div>
                    </div>

                    <!-- Rango de Folio Documento -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Folio Doc. (Desde - Hasta)</label>
                        <div class="flex space-x-2">
                            <input type="number" name="folio_from" class="w-1/2 text-xs border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Desde" value="{{ $filters['folio_from'] ?? '' }}">
                            <input type="number" name="folio_to" class="w-1/2 text-xs border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Hasta" value="{{ $filters['folio_to'] ?? '' }}">
                        </div>
                    </div>

                    <!-- RUT -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">RUT Entidad</label>
                        <input type="text" name="rut" class="w-full text-xs border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Ej: 12.345.678-9" value="{{ $filters['rut'] ?? '' }}">
                    </div>

                    <!-- Tipo de Documento -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Tipo Documento</label>
                        <select name="document_type_id" class="w-full text-xs border border-gray-300 rounded-lg p-2 bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">-- Todos --</option>
                            @foreach($documentTypes as $dtype)
                                <option value="{{ $dtype->doctype }}" {{ (isset($filters['document_type_id']) && $filters['document_type_id'] == $dtype->doctype) ? 'selected' : '' }}>
                                    {{ $dtype->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Folio de Referencia -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Folio de Referencia</label>
                        <input type="text" name="folio_ref" class="w-full text-xs border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Ref..." value="{{ $filters['folio_ref'] ?? '' }}">
                    </div>

                    <!-- Botones de Acción de Filtro -->
                    <div class="sm:col-span-2 flex items-end justify-end space-x-2 pt-2">
                        <a href="{{ route('accounting.system_journals') }}" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-semibold px-4 py-2 rounded-lg transition shadow-sm">
                            Limpiar
                        </a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-5 py-2 rounded-lg transition shadow">
                            Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- TABLA DE RESULTADOS -->
        <div class="bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                <thead class="bg-gray-800 text-white font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3 px-4 w-24 text-center">N° Asiento</th>
                        <th class="py-3 px-4">Fecha Centralización</th>
                        <th class="py-3 px-4">Folio</th>
                        <th class="py-3 px-4">Glosa / Descripción</th>
                        <th class="py-3 px-4">RUT</th>
                        <th class="py-3 px-4">Tipo Documento</th>
                        <th class="py-3 px-4">Folio Ref.</th>
                        <th class="py-3 px-4 w-20 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($journals as $journal)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-4 font-bold text-center bg-gray-50">
                                <a href="{{ route('accounting.journals.detail', $journal->id) }}" class="text-blue-600 hover:underline" title="Ver comprobante del asiento">
                                    {{ $journal->entry_number }} &rarr;
                                </a>
                            </td>
                            <td class="py-3 px-4 text-gray-600">{{ $journal->document->date_centralize ?? $journal->date }}</td>
                            
                            <!-- Folio -->
                            <td class="py-3 px-4 font-bold text-gray-900">
                                @if($journal->ref_doc_payed)
                                    -
                                @else
                                    {{ $journal->document->folio ?? 'N/A (Manual)' }}
                                @endif
                            </td>

                            <!-- Glosa / Descripción Actualizada -->
                            <td class="py-3 px-4 text-gray-800">
                                {{ $journal->description ?? 'Asiento automatizado' }}
                            </td>
                            
                            <!-- RUT -->
                            <td class="py-3 px-4 text-gray-600 font-mono">
                                @if($journal->ref_doc_payed && $journal->paidDocument)
                                    {{ $journal->paidDocument->entity->rut ?? 'N/A' }}
                                @else
                                    {{ $journal->document->entity->rut ?? 'N/A' }}
                                @endif
                            </td>

                            <!-- Tipo Documento -->
                            <td class="py-3 px-4 text-gray-600">
                                @if($journal->ref_doc_payed && $journal->paidDocument)
                                    {{ $journal->paidDocument->documentType->name ?? 'Documento Pagado' }}
                                @else
                                    {{ $journal->document->documentType->name ?? 'Asiento Manual' }}
                                @endif
                            </td>
                            
                            <!-- Folio Ref -->
                            <td class="py-3 px-4">
                                @if($journal->ref_doc_payed && $journal->paidDocument)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-800" title="Pago/Cobro aplicado al documento con Folio">
                                        Pago Doc. Folio: {{ $journal->paidDocument->folio }}
                                    </span>
                                @else
                                    <span class="text-gray-500">{{ $journal->document->folio_ref ?? '-' }}</span>
                                @endif
                            </td>
                            
                            <!-- Acciones -->
                            <td class="py-3 px-4 text-center">
                                <form action="{{ route('accounting.audit.destroy', $journal->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este asiento y su documento asociado?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 font-bold px-2.5 py-1 rounded transition text-sm" title="Eliminar Asiento">
                                        &times;
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-500 text-sm">
                                No se encontraron asientos contables con los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>