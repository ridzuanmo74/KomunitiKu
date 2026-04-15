<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="flex min-h-dvh min-h-screen flex-col bg-gray-100 text-sm text-gray-900">
            @include('layouts.navigation')

            <div class="flex min-h-0 flex-1">
                <aside class="w-56 shrink-0 overflow-y-auto border-r border-gray-200 bg-white p-3 sm:w-60">
                    @include('layouts.partials.sidebar')
                </aside>

                <div class="min-h-0 min-w-0 flex-1 overflow-y-auto p-4 md:p-6">
                    <div class="mx-auto w-full max-w-7xl">
                        <!-- Page Heading -->
                        @isset($header)
                            <header class="mb-4 rounded-lg border border-gray-200 bg-white">
                                <div class="px-4 py-3 sm:px-5">
                                    {{ $header }}
                                </div>
                            </header>
                        @endisset

                        <!-- Page Content -->
                        <main>
                            {{ $slot }}
                        </main>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
