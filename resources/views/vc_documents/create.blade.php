<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ingresar Documento V/C</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900">

    <header class="bg-white shadow mb-8">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Ingresar Documento V/C') }}
            </h2>
        </div>
    </header>

    <main class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <strong class="font-bold">¡Ups! Hubo un problema.</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('vc_documents.store') }}" method="POST">
                    @csrf

                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">1. Datos de Registro</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label for="month_register" class="block text-sm font-medium text-gray-700">Mes de Registro</label>
                            <input type="number" name="month_register" id="month_register" min="1" max="12" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                        <div>
                            <label for="year_register" class="block text-sm font-medium text-gray-700">Año de Registro</label>
                            <input type="number" name="year_register" id="year_register" min="2000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                        <div>
                            <label for="type_vc" class="block text-sm font-medium text-gray-700">Tipo (V/C)</label>
                            <select name="type_vc" id="type_vc" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="V">Venta</option>
                                <option value="C">Compra</option>
                            </select>
                        </div>
                    </div>

                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">2. Detalles del Documento</h3>
                    
                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-md mb-6">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Datos de la Entidad</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="rut" class="block text-sm font-medium text-gray-700">RUT (Ej: 12345678-9)</label>
                                <input type="text" name="rut" id="rut" maxlength="10" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <p id="rut_message" class="text-xs mt-1 h-4 text-gray-500"></p>
                            </div>
                            <div>
                                <label for="entity_name" class="block text-sm font-medium text-gray-700">Nombre o Razón Social</label>
                                <input type="text" name="entity_name" id="entity_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors" required>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-md mb-6">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Tipo de Documento</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="doctype" class="block text-sm font-medium text-gray-700">Código (Doctype)</label>
                                <input type="number" name="doctype" id="doctype" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <p id="doctype_message" class="text-xs mt-1 h-4 text-gray-500"></p>
                            </div>
                            <div>
                                <label for="document_type_name" class="block text-sm font-medium text-gray-700">Nombre del Documento</label>
                                <input type="text" name="document_type_name" id="document_type_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors" required>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="folio" class="block text-sm font-medium text-gray-700">Folio</label>
                            <input type="number" name="folio" id="folio" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700">Fecha Documento</label>
                            <input type="date" name="date" id="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                    </div>

                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">3. Referencias</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                        <div>
                            <label for="rut_ref" class="block text-sm font-medium text-gray-700">RUT Ref</label>
                            <input type="text" name="rut_ref" id="rut_ref" maxlength="10" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="folio_ref" class="block text-sm font-medium text-gray-700">Folio Ref</label>
                            <input type="number" name="folio_ref" id="folio_ref" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="td_ref" class="block text-sm font-medium text-gray-700">Tipo Doc Ref</label>
                            <input type="text" name="td_ref" id="td_ref" maxlength="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="date_centralize" class="block text-sm font-medium text-gray-700">Fecha Centralización</label>
                            <input type="date" name="date_centralize" id="date_centralize" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">4. Montos</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                        <div>
                            <label for="net" class="block text-sm font-medium text-gray-700">Neto</label>
                            <input type="number" name="net" id="net" value="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="exempt" class="block text-sm font-medium text-gray-700">Exento</label>
                            <input type="number" name="exempt" id="exempt" value="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="vat_rec" class="block text-sm font-medium text-gray-700">IVA Recuperable</label>
                            <input type="number" name="vat_rec" id="vat_rec" value="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="vat_no_rec" class="block text-sm font-medium text-gray-700">IVA No Recup.</label>
                            <input type="number" name="vat_no_rec" id="vat_no_rec" value="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="plus_oth_tax" class="block text-sm font-medium text-gray-700">Otros Imp. (+)</label>
                            <input type="number" name="plus_oth_tax" id="plus_oth_tax" value="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="minus_oth_tax" class="block text-sm font-medium text-gray-700">Otros Imp. (-)</label>
                            <input type="number" name="minus_oth_tax" id="minus_oth_tax" value="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="col-span-2">
                            <label for="total" class="block text-sm font-bold text-gray-900">TOTAL</label>
                            <input type="number" name="total" id="total" value="0" class="mt-1 block w-full rounded-md border-gray-400 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-bold" required>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t">
                        <a href="{{ route('vc_documents.create') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 mr-3">
                            Cancelar / Limpiar
                        </a>
                        <button type="submit" class="bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Guardar Documento
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rutInput = document.getElementById('rut');
            const nameInput = document.getElementById('entity_name');
            const rutMessage = document.getElementById('rut_message');

            rutInput.addEventListener('blur', function() {
                const rutValue = this.value.trim();
                
                if (rutValue.length > 0) {
                    rutMessage.textContent = 'Buscando...';
                    rutMessage.className = 'text-xs mt-1 h-4 text-gray-500';

                    fetch(`/vc-documents/check-entity/${rutValue}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.exists) {
                                nameInput.value = data.name;
                                nameInput.setAttribute('readonly', true);
                                nameInput.classList.add('bg-gray-100', 'text-gray-600', 'cursor-not-allowed');
                                
                                rutMessage.textContent = '✔️ Entidad cargada.';
                                rutMessage.className = 'text-xs mt-1 h-4 text-green-600 font-medium';
                            } else {
                                nameInput.value = '';
                                nameInput.removeAttribute('readonly');
                                nameInput.classList.remove('bg-gray-100', 'text-gray-600', 'cursor-not-allowed');
                                
                                rutMessage.textContent = 'ℹ️ RUT nuevo. Ingrese el nombre para registrarlo.';
                                rutMessage.className = 'text-xs mt-1 h-4 text-blue-600 font-medium';
                                nameInput.focus();
                            }
                        })
                        .catch(error => {
                            rutMessage.textContent = 'Error al consultar. Puede ingresar el nombre manualmente.';
                            rutMessage.className = 'text-xs mt-1 h-4 text-red-500';
                            nameInput.removeAttribute('readonly');
                            nameInput.classList.remove('bg-gray-100');
                        });
                } else {
                    rutMessage.textContent = '';
                }
            });

            const doctypeInput = document.getElementById('doctype');
            const docNameInput = document.getElementById('document_type_name');
            const doctypeMessage = document.getElementById('doctype_message');

            doctypeInput.addEventListener('blur', function() {
                const doctypeValue = this.value.trim();
                
                if (doctypeValue.length > 0) {
                    doctypeMessage.textContent = 'Buscando...';
                    doctypeMessage.className = 'text-xs mt-1 h-4 text-gray-500';

                    fetch(`/vc-documents/check-doctype/${doctypeValue}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.exists) {
                                docNameInput.value = data.name;
                                docNameInput.setAttribute('readonly', true);
                                docNameInput.classList.add('bg-gray-100', 'text-gray-600', 'cursor-not-allowed');
                                
                                doctypeMessage.textContent = '✔️ Tipo de documento cargado.';
                                doctypeMessage.className = 'text-xs mt-1 h-4 text-green-600 font-medium';
                            } else {
                                docNameInput.value = '';
                                docNameInput.removeAttribute('readonly');
                                docNameInput.classList.remove('bg-gray-100', 'text-gray-600', 'cursor-not-allowed');
                                
                                doctypeMessage.textContent = 'ℹ️ Código nuevo. Ingrese el nombre para registrarlo.';
                                doctypeMessage.className = 'text-xs mt-1 h-4 text-blue-600 font-medium';
                                docNameInput.focus();
                            }
                        })
                        .catch(error => {
                            doctypeMessage.textContent = 'Error al consultar. Puede ingresar el nombre manualmente.';
                            doctypeMessage.className = 'text-xs mt-1 h-4 text-red-500';
                            docNameInput.removeAttribute('readonly');
                            docNameInput.classList.remove('bg-gray-100');
                        });
                } else {
                    doctypeMessage.textContent = '';
                }
            });
        });
    </script>
</body>
</html>