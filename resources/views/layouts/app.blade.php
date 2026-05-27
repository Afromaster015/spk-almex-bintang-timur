<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('components.header')
    <title>@yield('title', 'Dashboard - Sistem Pendukung Keputusan')</title>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        @include('components.sidebar')
        <div class="flex-1 overflow-auto mx-6 p-7">
            @if(session('success'))
                <div class="mb-5 rounded-lg bg-emerald-100 border border-emerald-300 text-emerald-800 px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-5 rounded-lg bg-red-100 border border-red-300 text-red-800 px-4 py-3">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    @stack('scripts')
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
