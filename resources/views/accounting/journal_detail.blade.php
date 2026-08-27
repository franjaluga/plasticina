<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Asiento Contable N° {{ $journal->entry_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Comprobante de Asiento Contable</h2>
            <p class="text-muted mb-0">Asiento N°: <span class="fw-bold text-dark">{{ $journal->entry_number }}</span> | Año: <span class="fw-bold text-primary">{{ $journal->year }}</span></p>
        </div>
        <div class="d-flex gap-2">
            <!-- Botón para volver al listado -->
            <a href="{{ route('accounting.system_journals') }}" class="btn btn-outline-secondary btn-sm">
                &larr; Volver al Listado
            </a>

            <!-- Botón para eliminar el asiento (Reutiliza la ruta accounting.audit.destroy) -->
            <form action="{{ route('accounting.audit.destroy', $journal->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este asiento y su documento asociado?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">
                    Eliminar Asiento
                </button>
            </form>
        </div>
    </div>

    <!-- Mensajes de éxito o error por si acaso -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Cabecera de información del Asiento -->
    <div class="card mb-4 bg-light border-0 shadow-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-2">
                    <strong>Fecha del Asiento:</strong> {{ $journal->date }}
                </div>
                <div class="col-md-4 mb-2">
                    <strong>Tipo de Origen:</strong> 
                    {{ $journal->vc_document_id ? 'Documento V/C (ID: ' . $journal->vc_document_id . ')' : 'Asiento Manual / Pago' }}
                </div>
                <div class="col-md-4 mb-2">
                    <strong>Estado:</strong> 
                    <span class="badge bg-{{ $journal->is_balanced ? 'success' : 'danger' }}">
                        {{ $journal->is_balanced ? 'Cuadrado' : 'Descuadrado' }}
                    </span>
                </div>
                @if($journal->description)
                    <div class="col-12 mt-2">
                        <strong>Glosa / Descripción:</strong> {{ $journal->description }}
                    </div>
                @endif
                @if($journal->document && $journal->document->entity)
                    <div class="col-12 mt-2">
                        <strong>Entidad Asociada:</strong> {{ $journal->document->entity->name }} (RUT: {{ $journal->document->entity->rut }})
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tabla de Líneas de Detalle (Debe / Haber) -->
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th width="150px">Código Cuenta</th>
                <th>Nombre de la Cuenta</th>
                <th>Componente</th>
                <th class="text-end" width="150px">Debe</th>
                <th class="text-end" width="150px">Haber</th>
            </tr>
        </thead>
        <tbody>
            @foreach($journal->entries as $entry)
                <tr>
                    <td class="fw-bold text-center">{{ $entry->account_code }}</td>
                    <td>{{ $entry->account->name ?? 'Cuenta Sin Nombre' }}</td>
                    <td><span class="badge bg-secondary">{{ ucfirst($entry->component_name) }}</span></td>
                    <td class="text-end">{{ $entry->debit > 0 ? number_format($entry->debit, 0, ',', '.') : '-' }}</td>
                    <td class="text-end">{{ $entry->credit > 0 ? number_format($entry->credit, 0, ',', '.') : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="table-secondary fw-bold">
            <tr>
                <td colspan="3" class="text-end">TOTALES:</td>
                <td class="text-end">{{ number_format($journal->total_debit, 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($journal->total_credit, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

</body>
</html>