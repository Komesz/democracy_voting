<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }}</title>

        @vite(['resources/src/css/main.css', 'resources/src/main.js'])
    </head>
    <body class="antialiased">
        <div id="app"></div>

    </body>
</html>
