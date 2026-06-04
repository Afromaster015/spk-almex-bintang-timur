@extends('layouts.app')

@section('title', 'Dashboard - Sistem Pendukung Keputusan')

@section('content')
    <div>
        <h1 class="text-[60px] font-semibold">Halo, {{ session('name') }}!</h1>
        <p class="mt-4 text-gray-600 text-[20px]">Selamat datang di aplikasi SPK!</p>
    </div>
@endsection
