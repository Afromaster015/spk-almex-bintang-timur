@extends('layouts.app')

@section('title', isset($alternatif->id) ? 'Edit Alternatif - SPK' : 'Tambah Alternatif - SPK')

@section('content')
    <div class="max-w-3xl rounded-3xl bg-white p-8 shadow-sm">
        <h1 class="text-3xl font-semibold mb-4">{{ isset($alternatif->id) ? 'Edit Alternatif' : 'Tambah Alternatif' }}</h1>
        <p class="text-gray-600 mb-6">Tambahkan atau perbarui informasi pelanggan yang akan dievaluasi dalam perhitungan SPK.</p>

        @if ($errors->any())
            <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-700">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ $action }}" method="POST" class="space-y-6">
            @csrf
            @if($method === 'PUT')
                @method('PUT')
            @endif

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Kode Pelanggan</label>
                <input type="text" name="kode_alternatif" value="{{ old('kode_alternatif', $alternatif->kode_alternatif) }}" class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-merah-terang focus:outline-none" placeholder="Contoh: A1" required>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Nama Pelanggan</label>
                <input type="text" name="nama_pelanggan" value="{{ old('nama_pelanggan', $alternatif->nama_pelanggan) }}" class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-merah-terang focus:outline-none" placeholder="Contoh: PT Contoh Jaya" required>
            </div>

            <div class="flex items-center gap-3 pt-4">
                <a href="{{ route('alternatif.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-5 py-3 text-sm text-gray-700 hover:bg-gray-50">Batal</a>
                <button type="submit" class="inline-flex items-center rounded-md bg-merah-terang px-5 py-3 text-sm font-semibold text-white hover:bg-red-600">Simpan</button>
            </div>
        </form>
    </div>
@endsection
