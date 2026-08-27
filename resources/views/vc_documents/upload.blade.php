<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Importar documentos V/C - Sistema Contable</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900 flex items-center justify-center min-h-screen p-4">

    <div class="max-w-xl w-full mx-auto p-8 bg-white shadow-xl rounded-2xl border border-slate-100">
        
        <!-- Encabezado Limpio -->
        <div style="margin-bottom: 20px; text-align: left; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px;">
            <h2 style="font-size: 24px; font-weight: bold; color: #1e293b; margin-bottom: 5px;">Importar documentos V/C</h2>
            <p style="font-size: 14px; color: #64748b; margin: 0;">Carga masiva de registros de ventas y compras a través de un archivo CSV.</p>
        </div>

        <!-- Alerta de Éxito -->
        @if(session('success'))
            <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                {{ session('success') }}
            </div>
        @endif

        <!-- Alerta de Errores -->
        @if ($errors->any())
            <div style="background: #fff1f2; border: 1px solid #fecdd3; color: #9f1239; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulario -->
        <form action="{{ route('vc_documents.csv') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div style="margin-bottom: 20px; text-align: left;">
                <label for="csv_file" style="display: block; font-size: 12px; font-weight: bold; text-transform: uppercase; color: #475569; margin-bottom: 8px;">
                    Archivo CSV o TXT
                </label>
                
                <input type="file"
                       name="csv_file"
                       id="csv_file"
                       accept=".csv,.txt"
                       required
                       style="display: block; width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; background: #f8fafc; font-size: 14px; cursor: pointer;"
                       class="@error('csv_file') is-invalid @enderror">

                @error('csv_file')
                    <div style="color: #e11d48; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                <a href="{{ route('welcome') }}" style="font-size: 14px; font-weight: 500; background: #f1f5f9; color: #334155; padding: 10px 16px; border-radius: 8px; text-decoration: none;">
                    &larr; Volver
                </a>
                <button type="submit" style="background: #4f46e5; color: #ffffff; border: none; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;">
                    Subir y procesar
                </button>
            </div>
        </form>

    </div>

</body>
</html>