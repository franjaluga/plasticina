<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Documentos Pendientes de Contabilizar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Documentos V/C Pendientes de Contabilización</h2>
            <p class="text-muted mb-0">Periodo tributario año: <span class="fw-bold text-primary">{{ session('working_year', date('Y')) }}</span></p>
        </div>
        <div>
            <a href="{{ route('welcome') }}" class="text-xs font-semibold bg-slate-100 text-slate-700 px-3 py-1.5 rounded-lg hover:bg-slate-200 transition">
                &larr; Volver
            </a>
        </div>
    </div>

    <!-- Mensajes de éxito o error -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('vc_documents.batch_contabilizar') }}" method="POST">
        @csrf

        <!-- Selector / Input ComboBox dinámico para la Cuenta del Neto -->
        <div class="card p-3 mb-4 bg-light border-0 shadow-sm">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <label for="custom_net_account" class="form-label fw-bold text-secondary mb-1">
                        Cuenta Contable para el Neto <small class="text-muted">(Seleccione o escriba el código)</small>
                    </label>
                    <div class="input-group">
                        <input type="text" name="custom_net_account" id="custom_net_account" class="form-control" list="accounts_list" placeholder="Ej: Seleccione o escriba código" autocomplete="off">
                        
                        <!-- Datalist poblado dinámicamente desde el modelo Account -->
                        <datalist id="accounts_list">
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->code }}">{{ $acc->code }} - {{ $acc->name }}</option>
                            @endforeach
                        </datalist>
                    </div>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-bold">Contabilizar Seleccionados</button>
                </div>
            </div>
        </div>

        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th width="50px" class="text-center">
                        <input type="checkbox" id="select-all" class="form-check-input">
                    </th>
                    <th>Folio</th>
                    <th>Tipo V/C</th>
                    <th>Fecha Doc.</th>
                    <th class="text-end">Neto</th>
                    <th class="text-end">IVA Rec.</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" name="document_ids[]" value="{{ $doc->id }}" class="form-check-input doc-checkbox">
                        </td>
                        <td class="fw-bold">{{ $doc->folio }}</td>
                        <td>
                            <span class="badge bg-{{ $doc->type_vc === 'V' ? 'success' : 'info' }}">
                                {{ $doc->type_vc === 'V' ? 'Venta' : 'Compra' }}
                            </span>
                        </td>
                        <td>{{ $doc->date }}</td>
                        <td class="text-end">{{ number_format($doc->net, 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($doc->vat_rec, 0, ',', '.') }}</td>
                        <td class="text-end fw-bold">{{ number_format($doc->total, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No hay documentos pendientes de contabilizar para el año {{ session('working_year', date('Y')) }}.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </form>

    <script>
        document.getElementById('select-all').addEventListener('change', function() {
            let checkboxes = document.querySelectorAll('.doc-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    </script>
</body>
</html>