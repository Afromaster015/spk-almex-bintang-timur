@extends('layouts.app')

@section('title', 'Manajemen Periode - SPK')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-4xl font-semibold">Manajemen Periode</h1>
                <p class="text-gray-600 mt-2">Buat dan pilih periode kuartal untuk evaluasi SPK setiap 3 bulan.</p>
            </div>
        </div>

        @if($periodes->isEmpty())
            <div class="rounded-3xl bg-yellow-50 border border-yellow-200 p-6 text-yellow-800">
                <p class="font-semibold">Belum ada periode.</p>
                <p class="mt-2">Buat periode baru terlebih dahulu agar perhitungan dan input nilai dapat dibatasi ke kuartal.</p>
            </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-[1fr_420px]">
            <div class="rounded-3xl bg-white p-8 shadow-sm">
                <h2 class="text-2xl font-semibold mb-5">Daftar Periode</h2>

                <div class="overflow-x-auto rounded-3xl border border-gray-200 bg-gray-50 p-4">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-700">Periode</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-700">Tahun</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-700">Status</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($periodes as $periode)
                                <tr>
                                    <td class="px-4 py-4 text-gray-700 font-semibold">{{ $periode->nama_periode }}</td>
                                    <td class="px-4 py-4 text-gray-700">{{ $periode->tahun }}</td>
                                    @php $isActive = optional($currentPeriode)->id === $periode->id; @endphp
                                    <td class="px-4 py-4">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $isActive ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }}">
                                            {{ $isActive ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 space-x-2">
                                        @if($isActive)
                                            <form action="{{ route('periode.destroy', $periode) }}" method="POST" class="inline delete-periode" data-nama="{{ $periode->nama_periode }} {{ $periode->tahun }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700">Hapus</button>
                                            </form>
                                        @else
                                            <form action="{{ route('periode.set') }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="periode_id" value="{{ $periode->id }}">
                                                <button type="submit" class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">Pilih</button>
                                            </form>

                                            <form action="{{ route('periode.destroy', $periode) }}" method="POST" class="inline delete-periode" data-nama="{{ $periode->nama_periode }} {{ $periode->tahun }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700">Hapus</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada periode yang dibuat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-3xl bg-white p-8 shadow-sm">
                <h2 class="text-2xl font-semibold mb-5">Buat Periode Baru</h2>

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

                <form action="{{ route('periode.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block">
                            <span class="text-sm font-medium text-gray-700">Kuartal</span>
                            <select name="nama_periode" class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm focus:border-merah-terang focus:outline-none" required>
                                <option value="">Pilih Kuartal</option>
                                <option value="Q1">Q1</option>
                                <option value="Q2">Q2</option>
                                <option value="Q3">Q3</option>
                                <option value="Q4">Q4</option>
                            </select>
                        </label>

                        <label class="block">
                            <span class="text-sm font-medium text-gray-700">Tahun</span>
                            <input type="number" name="tahun" min="2000" max="2100" value="{{ old('tahun', date('Y')) }}" class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-merah-terang focus:outline-none" placeholder="2026" required>
                        </label>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="rounded-xl bg-merah-terang px-5 py-3 text-sm font-semibold text-white hover:bg-red-600">Buat Periode</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@include('pages.periode.delete-confirm-script')
