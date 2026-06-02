@extends('layouts.app')

@section('title', 'Pilih Alternatif - SPK')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-4xl font-semibold">Pilih Alternatif</h1>
                <p class="text-gray-600 mt-2">Tandai alternatif yang ingin digunakan pada periode aktif saat ini.</p>
            </div>
            <div class="w-full sm:w-96">
                <label for="searchAlternatif" class="sr-only">Cari Alternatif</label>
                <input
                    id="searchAlternatif"
                    type="text"
                    placeholder="Cari Kode atau Nama Pelanggan..."
                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-merah-terang focus:outline-none"
                />
            </div>
        </div>

        @if(!optional($currentPeriode)->id)
            <div class="rounded-3xl bg-yellow-50 border border-yellow-200 p-6 text-yellow-800">
                <p class="font-semibold">Periode aktif belum dipilih.</p>
                <p class="mt-2">Silakan pilih periode aktif di menu Periode terlebih dahulu sebelum memilih alternatif.</p>
            </div>
        @endif

        <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-gray-200 text-left">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-sm font-medium text-gray-500">Kode Pelanggan</th>
                        <th class="px-6 py-4 text-sm font-medium text-gray-500">Nama Pelanggan</th>
                        <th class="px-6 py-4 text-sm font-medium text-gray-500">Status</th>
                        <th class="px-6 py-4 text-sm font-medium text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($alternatifs as $alternatif)
                        @php
                            $isActive = optional($currentPeriode)->id && in_array($alternatif->id, $selectedAlternatifIds ?? []);
                        @endphp
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $alternatif->kode_alternatif }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $alternatif->nama_pelanggan }}</td>
                            <td class="px-6 py-4 text-sm font-semibold {{ $isActive ? 'text-emerald-700' : 'text-gray-600' }}">
                                {{ $isActive ? 'Aktif' : 'Tidak Aktif' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                @if(optional($currentPeriode)->id)
                                    <form action="{{ route('alternatif.toggle-status', $alternatif) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="rounded-md px-4 py-2 text-sm font-semibold {{ $isActive ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-emerald-600 text-white hover:bg-emerald-700' }}">
                                            {{ $isActive ? 'Batal Pilih' : 'Pilih' }}
                                        </button>
                                    </form>
                                @else
                                    <button disabled class="rounded-md bg-gray-300 px-4 py-2 text-sm text-gray-700">Pilih</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                Belum ada alternatif di database. Tambahkan alternatif baru di menu Kelola Alternatif.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
        const searchInput = document.getElementById('searchAlternatif');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const query = this.value.toLowerCase().trim();
                document.querySelectorAll('tbody tr').forEach(function (row) {
                    const kode = row.querySelector('td:nth-child(1)')?.textContent.toLowerCase() || '';
                    const nama = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                    const shouldShow = kode.includes(query) || nama.includes(query);
                    row.style.display = shouldShow ? '' : 'none';
                });
            });
        }
    </script>
    @endpush
@endsection
