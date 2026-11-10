<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>App Especiales</title>
    @vite('resources/js/app.js')
</head>
<body style="font-family: sans-serif; background: #f3f4f6; margin: 0;">
    <div id="app"></div>
</body>
</html>
