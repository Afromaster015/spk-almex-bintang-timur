@extends('layouts.app')

@section('title', 'Hasil Bobot Kriteria - SPK')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-semibold">Hasil Bobot Kriteria</h1>
                <p class="text-gray-600 mt-2">Menampilkan hasil bobot kriteria yang dihasilkan dari perhitungan AHP.</p>
            </div>
            <a href="{{ route('kriteria.bobot') }}" class="rounded-md bg-yellow-500 px-5 py-3 text-white hover:bg-yellow-600">Kembali ke Nilai Bobot Kriteria</a>
        </div>

        <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-gray-200 text-left">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-sm font-medium text-gray-500">Kode</th>
                        <th class="px-6 py-4 text-sm font-medium text-gray-500">Nama Kriteria</th>
                        <th class="px-6 py-4 text-sm font-medium text-gray-500">Jenis</th>
                        <th class="px-6 py-4 text-sm font-medium text-gray-500">Bobot AHP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($kriteria as $item)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $item->kode_kriteria }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $item->nama_kriteria }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 capitalize">{{ $item->jenis }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $item->bobot ? number_format($item->bobot, 4) : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada data kriteria. Tambahkan kriteria terlebih dahulu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="rounded-3xl bg-blue-50 border border-blue-200 p-6 text-blue-900">
            <p class="font-semibold">Catatan:</p>
            <p class="mt-2">Jika bobot masih tampil "-", jalankan perhitungan di menu <strong>Nilai Bobot Kriteria</strong> terlebih dahulu.</p>
        </div>
    </div>
@endsection
