@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title ? "{$title} · " : '' }}{{ config('app.name') }}</title>

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body {{ $attributes->merge(['class' => 'min-h-screen font-sans text-gray-900 antialiased']) }}>
        {{ $slot }}
    </body>
</html>
