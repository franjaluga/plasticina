@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Importar documentos V/C desde CSV</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('vc_documents.csv') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="csv_file" class="form-label">Archivo CSV</label>
            <input type="file"
                   name="csv_file"
                   id="csv_file"
                   accept=".csv,.txt"
                   required
                   class="form-control @error('csv_file') is-invalid @enderror">

            @error('csv_file')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">
            Subir y procesar
        </button>
    </form>
</div>
@endsection