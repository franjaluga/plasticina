<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Mi aplicación')</title>
    <!-- Puedes enlazar CSS de Bootstrap o tu propio estilo -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<div class="container mt-4">
    @yield('content')
</div>

@stack('scripts')
</body>
</html>
