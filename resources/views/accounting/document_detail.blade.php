<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detalle del Documento</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-slate-900 font-sans p-8 max-w-3xl mx-auto">

    <!-- Cabecera -->
    <div class="flex justify-between items-baseline border-b border-slate-300 pb-4 mb-6">
        <div>
            <h1 class="text-xl font-semibold tracking-tight">Detalle del Documento</h1>
            <p class="text-xs text-slate-500">Folio N°: <span class="font-bold text-slate-800">{{ $document->folio }}</span></p>
        </div>
        <div>
            <a href="javascript:history.back()" class="text-xs font-semibold bg-slate-100 text-slate-700 px-3 py-1.5 rounded-lg hover:bg-slate-200 transition">
                &larr; Volver
            </a>
        </div>
    </div>

    <!-- Contenido Detallado -->
    <div class="space-y-6 text-xs">
        
        <!-- Bloque 1: Datos Generales -->
        <div class="border border-slate-200 rounded-xl p-4 bg-slate-50/50">
            <h3 class="font-bold uppercase tracking-wider text-[10px] text-slate-500 mb-3">1. Información de Registro</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 font-mono">
                <div>
                    <span class="block text-slate-400 font-sans">Tipo (V/C)</span>
                    <span class="font-bold text-slate-800 text-sm">{{ $document->type_vc }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 font-sans">Mes / Año</span>
                    <span class="font-bold text-slate-800 text-sm">{{ $document->month_register }} / {{ $document->year_register }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 font-sans">Fecha Doc.</span>
                    <span class="font-bold text-slate-800 text-sm">{{ $document->date }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 font-sans">Contabilizado</span>
                    <span class="font-bold {{ $document->journal ? 'text-green-600' : 'text-amber-600' }} text-sm">
                        {{ $document->journal ? 'Sí' : 'Pendiente' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Bloque 2: Entidad y Documento -->
        <div class="border border-slate-200 rounded-xl p-4 bg-slate-50/50">
            <h3 class="font-bold uppercase tracking-wider text-[10px] text-slate-500 mb-3">2. Entidad y Tipo de Documento</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <span class="block text-slate-400 font-sans">RUT / Entidad</span>
                    <span class="font-bold text-slate-800 text-sm font-mono">{{ $document->entity->rut ?? 'N/A' }}</span>
                    <p class="text-slate-600">{{ $document->entity->name ?? 'Sin nombre registrado' }}</p>
                </div>
                <div>
                    <span class="block text-slate-400 font-sans">Tipo de Documento (Doctype)</span>
                    <span class="font-bold text-slate-800 text-sm font-mono">{{ $document->document_type_id ?? 'N/A' }}</span>
                    <p class="text-slate-600">{{ $document->documentType->name ?? $document->documentType->description ?? 'Sin nombre registrado' }}</p>
                </div>
            </div>
        </div>

        <!-- Bloque 3: Referencias -->
        <div class="border border-slate-200 rounded-xl p-4 bg-slate-50/50">
            <h3 class="font-bold uppercase tracking-wider text-[10px] text-slate-500 mb-3">3. Referencias</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 font-mono">
                <div>
                    <span class="block text-slate-400 font-sans">RUT Ref</span>
                    <span class="text-slate-800">{{ $document->rut_ref ?? '-' }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 font-sans">Folio Ref</span>
                    <span class="text-slate-800">{{ $document->folio_ref ?? '-' }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 font-sans">Tipo Doc Ref</span>
                    <span class="text-slate-800">{{ $document->td_ref ?? '-' }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 font-sans">F. Centralización</span>
                    <span class="text-slate-800">{{ $document->date_centralize ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Bloque 4: Montos -->
        <div class="border border-slate-200 rounded-xl p-4 bg-slate-50/50">
            <h3 class="font-bold uppercase tracking-wider text-[10px] text-slate-500 mb-3">4. Desglose de Montos</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 font-mono">
                <div>
                    <span class="block text-slate-400 font-sans">Neto</span>
                    <span class="text-slate-800">{{ number_format($document->net, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 font-sans">Exento</span>
                    <span class="text-slate-800">{{ number_format($document->exempt, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 font-sans">IVA Recuperable</span>
                    <span class="text-slate-800">{{ number_format($document->vat_rec, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 font-sans">IVA No Recup.</span>
                    <span class="text-slate-800">{{ number_format($document->vat_no_rec, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 font-sans">Otros Imp. (+)</span>
                    <span class="text-slate-800">{{ number_format($document->plus_oth_tax, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="block text-slate-400 font-sans">Otros Imp. (-)</span>
                    <span class="text-slate-800">{{ number_format($document->minus_oth_tax, 0, ',', '.') }}</span>
                </div>
                <div class="col-span-2 border-t pt-2 border-slate-200">
                    <span class="block text-slate-400 font-sans font-bold">TOTAL</span>
                    <span class="font-extrabold text-slate-900 text-sm">{{ number_format($document->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

    </div>

</body>
</html>