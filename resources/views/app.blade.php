<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }} - Portfolio</title>
    @vite(['resources/css/app.css', 'resource/js/app.js'])
    @livewireStyles
</head>

<body class="antialiased">
    {{ $slot }}
    @livewireScripts
</body>

</html>