<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Seleccionar Rango - Libro Diario V/C</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900 flex items-center justify-center min-h-screen p-4">

    <div class="max-w-md w-full mx-auto p-8 bg-white shadow-xl rounded-2xl border border-slate-100">
        
        <!-- Volver -->
        <div class="mb-6">
            <a href="{{ route('reports.journal_context') }}" class="inline-flex items-center text-xs font-semibold bg-white text-slate-600 border border-slate-200 px-3 py-2 rounded-lg hover:bg-slate-50 transition shadow-sm">
                &larr; Volver
            </a>
        </div>

        <div class="text-center mb-6">
            <h1 class="text-xl font-extrabold text-slate-800">Libro Diario</h1>
            <p class="text-xs text-slate-500 mt-1">Selecciona el rango de fechas para generar el registro</p>
        </div>

        <form action="{{ route('vc_documents.journal_book.generate') }}" method="GET" class="space-y-4">
            <div>
                <label for="start_date" class="block text-xs font-semibold text-slate-600 mb-1">Fecha de Inicio</label>
                <input type="date" name="start_date" id="start_date" value="{{ date('Y-m-01') }}" required class="w-full text-xs rounded-lg border-slate-300 shadow-sm p-2.5 bg-white border focus:border-emerald-500 focus:ring-emerald-500">
            </div>

            <div>
                <label for="end_date" class="block text-xs font-semibold text-slate-600 mb-1">Fecha de Término</label>
                <input type="date" name="end_date" id="end_date" value="{{ date('Y-m-t') }}" required class="w-full text-xs rounded-lg border-slate-300 shadow-sm p-2.5 bg-white border focus:border-emerald-500 focus:ring-emerald-500">
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs py-3 px-4 rounded-xl shadow-sm transition flex items-center justify-center">
                    Generar Libro Diario &rarr;
                </button>
            </div>
        </form>

    </div>

</body>
</html>