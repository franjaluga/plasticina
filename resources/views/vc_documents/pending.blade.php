<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Documentos Pendientes de Contabilizar</title>
    <!-- Puedes usar Bootstrap o Tailwind según tu proyecto -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">

    <h2 class="mb-4">Documentos V/C Pendientes de Contabilización</h2>

    <!-- Mensajes de éxito o error -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('vc_documents.batch_contabilizar') }}" method="POST">
        @csrf

        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Contabilizar Seleccionados</button>
            <a href="{{ route('vc_documents.create') }}" class="btn btn-secondary">Crear Nuevo Documento</a>
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="50px">
                        <input type="checkbox" id="select-all" class="form-check-input">
                    </th>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Folio</th>
                    <th>Tipo V/C</th>
                    <th>Neto</th>
                    <th>IVA Rec.</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                    <tr>
                        <td>
                            <input type="checkbox" name="document_ids[]" value="{{ $doc->id }}" class="form-check-input doc-checkbox">
                        </td>
                        <td>{{ $doc->id }}</td>
                        <td>{{ $doc->date }}</td>
                        <td>{{ $doc->folio }}</td>
                        <td>{{ $doc->type_vc }}</td>
                        <td>{{ number_format($doc->net, 0, ',', '.') }}</td>
                        <td>{{ number_format($doc->vat_rec, 0, ',', '.') }}</td>
                        <td>{{ number_format($doc->total, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No hay documentos pendientes de contabilizar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </form>

    <!-- Script básico para seleccionar/deseleccionar todos -->
    <script>
        document.getElementById('select-all').addEventListener('change', function() {
            let checkboxes = document.querySelectorAll('.doc-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    </script>
</body>
</html>