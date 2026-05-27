@extends('layouts.app')

@section('title', 'Nilai Bobot Alternatif - SPK')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-semibold">Nilai Bobot Alternatif</h1>
                <p class="text-gray-600 mt-2">Isi atau perbarui nilai bobot alternatif untuk setiap kriteria.</p>
            </div>
        </div>

        @if($kriterias->isEmpty() || $alternatifs->isEmpty())
            <div class="rounded-3xl bg-yellow-50 border border-yellow-200 p-6 text-yellow-800">
                <p class="font-semibold">Informasi tidak lengkap.</p>
                <p class="mt-2">Pastikan sudah ada data kriteria dan data pelanggan sebelum memasukkan nilai alternatif.</p>
            </div>
        @else
            @if($errors->any())
                <div class="rounded-3xl bg-red-50 border border-red-200 p-6 text-red-800">
                    <p class="font-semibold mb-3">Terjadi kesalahan:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('nilai-alternatif.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200 text-left">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-sm font-medium text-gray-700">Kode Pelanggan</th>
                                <th class="px-4 py-3 text-sm font-medium text-gray-700">Nama Pelanggan</th>
                                @foreach($kriterias as $kriteria)
                                    <th class="px-4 py-3 text-sm font-medium text-gray-700">{{ $kriteria->kode_kriteria }}<br><span class="text-xs font-normal">{{ $kriteria->nama_kriteria }}</span></th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($alternatifs as $alternatif)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4 text-sm font-semibold text-gray-700">{{ $alternatif->kode_alternatif }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-700">{{ $alternatif->nama_pelanggan }}</td>
                                    @foreach($kriterias as $kriteria)
                                        <td class="px-4 py-4">
                                            <select 
                                                name="nilai[{{ $alternatif->id }}][{{ $kriteria->id }}]" 
                                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-merah-terang focus:outline-none {{ $errors->has('nilai.' . $alternatif->id . '.' . $kriteria->id) ? 'border-red-500' : '' }}"
                                                required>
                                                <option value="">Pilih...</option>
                                                <option value="5" {{ old('nilai.' . $alternatif->id . '.' . $kriteria->id, $nilaiAlternatif->get($alternatif->id . '_' . $kriteria->id)?->nilai) == 5 ? 'selected' : '' }}>Baik (5)</option>
                                                <option value="3" {{ old('nilai.' . $alternatif->id . '.' . $kriteria->id, $nilaiAlternatif->get($alternatif->id . '_' . $kriteria->id)?->nilai) == 3 ? 'selected' : '' }}>Sedang (3)</option>
                                                <option value="1" {{ old('nilai.' . $alternatif->id . '.' . $kriteria->id, $nilaiAlternatif->get($alternatif->id . '_' . $kriteria->id)?->nilai) == 1 ? 'selected' : '' }}>Buruk (1)</option>
                                            </select>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('alternatif.index') }}" class="rounded-md border border-gray-300 bg-white px-5 py-3 text-sm text-gray-700 hover:bg-gray-50">Batal</a>
                    <button type="submit" class="rounded-md bg-merah-terang px-5 py-3 text-sm font-semibold text-white hover:bg-red-600">Simpan Semua Nilai</button>
                </div>
            </form>

            <div class="rounded-3xl border border-blue-200 bg-blue-50 p-6 text-blue-800">
                <p class="font-semibold mb-2">Catatan:</p>
                <ul class="list-disc pl-5 space-y-1 text-sm">
                    <li>Pilih nilai untuk setiap kombinasi pelanggan dan kriteria</li>
                    <li><strong>Baik (5)</strong> - Nilai tertinggi / Terbaik</li>
                    <li><strong>Sedang (3)</strong> - Nilai tengah</li>
                    <li><strong>Buruk (1)</strong> - Nilai terendah</li>
                    <li>Tekan "Simpan Semua Nilai" untuk menyimpan perubahan</li>
                </ul>
            </div>
        @endif
    </div>
@endsection
