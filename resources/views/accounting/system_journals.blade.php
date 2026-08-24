<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Asientos Contables</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Asientos Contables del Sistema</h2>
            <p class="text-muted mb-0">Periodo tributario año: <span class="fw-bold text-primary">{{ $year }}</span></p>
        </div>
        <div>
            <a href="{{ route('reports.journal_context') }}" class="text-xs font-semibold bg-slate-100 text-slate-700 px-3 py-1.5 rounded-lg hover:bg-slate-200 transition">
                &larr; Volver
            </a>
        </div>
    </div>

    <!-- Mensajes de éxito o error -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- BLOQUE DE FILTROS -->
    <div class="card mb-4 bg-light border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title text-secondary mb-3">Filtros de Búsqueda</h5>
            <form action="{{ route('accounting.system_journals') }}" method="GET">
                <div class="row g-3">
                    <!-- Rango de Asientos -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">N° Asiento (Desde - Hasta)</label>
                        <div class="input-group input-group-sm">
                            <input type="number" name="entry_from" class="form-control" placeholder="Desde" value="{{ $filters['entry_from'] ?? '' }}">
                            <input type="number" name="entry_to" class="form-control" placeholder="Hasta" value="{{ $filters['entry_to'] ?? '' }}">
                        </div>
                    </div>

                    <!-- Rango de Fecha de Centralización -->
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Fecha Centralización (Desde - Hasta)</label>
                        <div class="input-group input-group-sm">
                            <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}">
                            <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}">
                        </div>
                    </div>

                    <!-- Rango de Folio Documento -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Folio Doc. (Desde - Hasta)</label>
                        <div class="input-group input-group-sm">
                            <input type="number" name="folio_from" class="form-control" placeholder="Desde" value="{{ $filters['folio_from'] ?? '' }}">
                            <input type="number" name="folio_to" class="form-control" placeholder="Hasta" value="{{ $filters['folio_to'] ?? '' }}">
                        </div>
                    </div>

                    <!-- RUT -->
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">RUT Entidad</label>
                        <input type="text" name="rut" class="form-control form-control-sm" placeholder="Ej: 12.345.678-9" value="{{ $filters['rut'] ?? '' }}">
                    </div>

                    <!-- Tipo de Documento -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Tipo Documento</label>
                        <select name="document_type_id" class="form-select form-select-sm">
                            <option value="">-- Todos --</option>
                            @foreach($documentTypes as $dtype)
                                {{-- Cambiamos $dtype->id por $dtype->doctype --}}
                                <option value="{{ $dtype->doctype }}" {{ (isset($filters['document_type_id']) && $filters['document_type_id'] == $dtype->doctype) ? 'selected' : '' }}>
                                    {{ $dtype->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Folio de Referencia -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Folio de Referencia</label>
                        <input type="text" name="folio_ref" class="form-control form-control-sm" placeholder="Ref..." value="{{ $filters['folio_ref'] ?? '' }}">
                    </div>

                    <!-- Botones de Acción de Filtro -->
                    <div class="col-md-6 d-flex align-items-end justify-content-end gap-2">
                        <a href="{{ route('accounting.system_journals') }}" class="btn btn-outline-secondary btn-sm px-3">Limpiar</a>
                        <button type="submit" class="btn btn-primary btn-sm px-4">Filtrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- TABLA DE RESULTADOS -->
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th width="100px">N° Asiento</th>
                <th>Fecha Centralización</th>
                <th>Folio</th>
                <th>Glosa / Descripción</th>
                <th>RUT</th>
                <th>Tipo Documento</th>
                <th>Folio Ref.</th>
                <th width="80px" class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($journals as $journal)
                <tr>
                    <td class="fw-bold text-center bg-light">
                        <a href="{{ route('accounting.journals.detail', $journal->id) }}" class="text-decoration-none text-primary" title="Ver comprobante del asiento">
                            {{ $journal->entry_number }} &rarr;
                        </a>
                    </td>
                    <td>{{ $journal->document->date_centralize ?? $journal->date }}</td>
                    <td class="fw-bold">{{ $journal->document->folio ?? 'N/A (Manual)' }}</td>
                    <td>{{ $journal->description ?? 'Asiento por documento V/C' }}</td>
                    <td>{{ $journal->document->entity->rut ?? 'N/A' }}</td>
                    <td>{{ $journal->document->documentType->name ?? 'Asiento Manual' }}</td>
                    <td>{{ $journal->document->folio_ref ?? '-' }}</td>
                    <td class="text-center">
                        <form action="{{ route('accounting.audit.destroy', $journal->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este asiento y su documento asociado?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" title="Eliminar Asiento">
                                &times;
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">No se encontraron asientos contables con los filtros seleccionados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>