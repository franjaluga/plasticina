<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Auditoría de Documentos y Asientos - Sistema Contable</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900 p-6">

    <div class="max-w-7xl mx-auto p-8 bg-white shadow-xl rounded-2xl border border-slate-100">
        
        <!-- Cabecera -->
        <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-200">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Auditoría de Documentos y Asientos</h1>
                <p class="text-sm text-slate-500 mt-1">Periodo: <span class="font-bold text-slate-700">{{ session('working_year', date('Y')) }}</span> | Owner: <span class="font-bold text-slate-700">{{ $activeOwner?->name ?? 'N/A' }}</span></p>
            </div>
            <div>
                <a href="{{ route('accounting.reports.index') }}" class="text-xs font-semibold bg-slate-100 text-slate-700 px-4 py-2 rounded-xl hover:bg-slate-200 transition shadow-sm">
                    &larr; Volver a Reportes / Menú
                </a>
            </div>
        </div>

        <!-- Mensajes de éxito / error -->
        @if(session('success'))
            <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm">
                {{ session('error') }}
            </div>
        @endif

        <!-- Tabla de Auditoría -->
        <div class="overflow-x-auto border border-slate-200 rounded-xl">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-100 text-slate-700 uppercase text-xs tracking-wider border-b border-slate-200">
                        <th class="p-3">N° Asiento</th>
                        <th class="p-3">Fecha</th>
                        <th class="p-3">Glosa / Descripción</th>
                        <th class="p-3">Documento Ref. (Folio)</th>
                        <th class="p-3">RUT Referencia</th>
                        <th class="p-3 text-right">Debe</th>
                        <th class="p-3 text-right">Haber</th>
                        <th class="p-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-slate-700">
                    @forelse($journals as $journal)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-3 font-bold text-indigo-600">#{{ $journal->entry_number }}</td>
                            <td class="p-3 whitespace-nowrap">{{ $journal->date }}</td>
                            <td class="p-3 max-w-xs truncate" title="{{ $journal->description }}">{{ $journal->description }}</td>
                            <td class="p-3">
                                @if($journal->document)
                                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-semibold border border-emerald-200">
                                        Folio: {{ $journal->document->folio }} (Tipo: {{ $journal->document->type_vc }})
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-amber-50 text-amber-700 rounded-lg text-xs font-semibold border border-amber-200">
                                        Manual (Sin Doc)
                                    </span>
                                @endif
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                @if($journal->document && $journal->document->entity)
                                    {{ $journal->document->entity->rut }}
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            {{-- Modificado a 0 decimales: number_format(..., 0, ',', '.') --}}
                            <td class="p-3 text-right font-mono">$ {{ number_format($journal->total_debit, 0, ',', '.') }}</td>
                            <td class="p-3 text-right font-mono">$ {{ number_format($journal->total_credit, 0, ',', '.') }}</td>
                            <td class="p-3 text-center whitespace-nowrap">
                                <!-- Botón de Borrado -->
                                <form action="{{ route('accounting.journals.destroy', $journal->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este registro? Si está vinculado a un documento, también se eliminará.');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 bg-rose-50 text-rose-600 border border-rose-200 rounded-lg text-xs font-semibold hover:bg-rose-600 hover:text-white transition shadow-sm">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-6 text-center text-slate-400">
                                No hay asientos contables registrados para este periodo.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>