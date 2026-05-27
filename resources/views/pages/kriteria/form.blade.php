@extends('layouts.app')

@section('title', isset($kriteria->id) ? 'Edit Kriteria - SPK' : 'Tambah Kriteria - SPK')

@section('content')
    <div class="max-w-3xl rounded-3xl bg-white p-8 shadow-sm">
        <h1 class="text-3xl font-semibold mb-4">{{ isset($kriteria->id) ? 'Edit Kriteria' : 'Tambah Kriteria' }}</h1>
        <p class="text-gray-600 mb-6">Lengkapi data kriteria yang akan digunakan dalam perhitungan SPK.</p>

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
                <label class="mb-2 block text-sm font-medium text-gray-700">Kode Kriteria</label>
                <input type="text" name="kode_kriteria" value="{{ old('kode_kriteria', $kriteria->kode_kriteria) }}" class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-merah-terang focus:outline-none" placeholder="Contoh: C1" required>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Nama Kriteria</label>
                <input type="text" name="nama_kriteria" value="{{ old('nama_kriteria', $kriteria->nama_kriteria) }}" class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-merah-terang focus:outline-none" placeholder="Contoh: Kualitas Pelayanan" required>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Jenis</label>
                <select name="jenis" class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-merah-terang focus:outline-none" required>
                    <option value="">Pilih jenis</option>
                    <option value="benefit" {{ old('jenis', $kriteria->jenis) === 'benefit' ? 'selected' : '' }}>Benefit</option>
                    <option value="cost" {{ old('jenis', $kriteria->jenis) === 'cost' ? 'selected' : '' }}>Cost</option>
                </select>
            </div>

            <div class="flex items-center gap-3 pt-4">
                <a href="{{ route('kriteria.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-5 py-3 text-sm text-gray-700 hover:bg-gray-50">Batal</a>
                <button type="submit" class="inline-flex items-center rounded-md bg-merah-terang px-5 py-3 text-sm font-semibold text-white hover:bg-red-600">Simpan</button>
            </div>
        </form>
    </div>
@endsection
