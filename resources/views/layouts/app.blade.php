<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="min-h-screen bg-neutral-50 text-neutral-900 antialiased">
        <div class="flex min-h-screen flex-col">
            <header class="h-14 shrink-0 border-b border-neutral-200 bg-white"></header>

            <div class="flex min-h-0 flex-1">
                <aside class="w-64 shrink-0 border-r border-neutral-200 bg-white"></aside>

                <main class="min-w-0 flex-1">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @livewireScripts
    </body>
</html>
