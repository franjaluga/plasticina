<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Documentos V/C</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900 flex items-center justify-center min-h-screen">

    <div class="max-w-xl w-full mx-auto p-6 bg-white shadow-md rounded-lg text-center">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Sistema de Documentos V/C</h1>
        <p class="text-gray-600 mb-6">Gestión de registros de ventas y compras</p>

        <div class="space-y-4">
            <a href="{{ route('vc_documents.create') }}" class="block w-full bg-indigo-600 text-white font-medium py-3 px-4 rounded-md shadow hover:bg-indigo-700 transition">
                Ingresar Nuevo Documento V/C
            </a>
        </div>
        <br>
        <div class="space-y-4">
            <a href="{{ route('vc_documents.upload') }}" class="block w-full bg-indigo-600 text-white font-medium py-3 px-4 rounded-md shadow hover:bg-indigo-700 transition">
                Ingresar vía importador
            </a>
        </div>
        <div class="mt-8 pt-4 border-t border-gray-200 text-xs text-gray-500">
            <p>Sistema en proceso de desarrollo</p>
        </div>
    </div>

</body>
</html>