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
            <div class="mb-5 rounded-3xl bg-white p-4 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="text-base font-medium text-gray-700">
                        <span class="font-semibold text-lg">Periode Aktif:</span>
                        @if($currentPeriode)
                            <span class="ml-2 text-xl font-semibold text-gray-900">{{ $currentPeriode->nama_periode }} {{ $currentPeriode->tahun }}</span>
                        @else
                            <span class="ml-2 text-lg font-semibold text-red-600">Belum ada periode terpilih.</span>
                        @endif
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        <span class="text-sm text-gray-600">Ingin ubah periode?</span>
                        <a href="{{ route('periode.index') }}" class="rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700">Kelola Periode</a>
                    </div>
                </div>
            </div>

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
