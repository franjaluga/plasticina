<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Owners</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="container py-5">

    <h1 class="mb-4">Gestión de Owners</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Indicador del Service -->
    <div class="alert alert-info">
        <strong>Owner Activo Actual:</strong> 
        {{ $activeOwner ? $activeOwner->name . ' (' . $activeOwner->rut . ')' : 'Ninguno activo' }}
    </div>

    <!-- Formulario de Creación -->
    <div class="card mb-4">
        <div class="card-header">Agregar Nuevo Owner</div>
        <div class="card-body">
            <form action="{{ route('owners.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">RUT</label>
                        <input type="text" name="rut" class="form-control" required maxlength="10">
                    </div>
                    <div class="col-md-5 mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="name" class="form-control" required maxlength="100">
                    </div>
                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Listado -->
    <div class="card">
        <div class="card-header">Listado de Owners</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>RUT</th>
                        <th>Nombre</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($owners as $owner)
                    <tr>
                        <td>{{ $owner->id }}</td>
                        <td>{{ $owner->rut }}</td>
                        <td>{{ $owner->name }}</td>
                        <td>
                            @if($owner->is_active)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <!-- Botón Activar -->
                            @unless($owner->is_active)
                                <form action="{{ route('owners.activate', $owner) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success">Activar</button>
                                </form>
                            @endunless

                            <!-- Formulario rápido de edición -->
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $owner->id }}">Editar</button>
                        </td>
                    </tr>

                    <!-- Modal de Edición por registro -->
                    <div class="modal fade" id="editModal{{ $owner->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form action="{{ route('owners.update', $owner) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Editar Owner</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">RUT</label>
                                            <input type="text" name="rut" value="{{ $owner->rut }}" class="form-control" required maxlength="10">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nombre</label>
                                            <input type="text" name="name" value="{{ $owner->name }}" class="form-control" required maxlength="100">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-primary">Actualizar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>