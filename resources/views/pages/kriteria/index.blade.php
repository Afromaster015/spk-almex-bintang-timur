@extends('layouts.app')

@section('title', 'Kriteria - SPK')

@section('content')
    <div class="flex items-center justify-between mb-10">
        <div>
            <h1 class="text-4xl font-semibold">Manajemen Kriteria</h1>
            <p class="text-gray-600 mt-2">Kelola data kriteria dan bobot prioritas AHP untuk evaluasi pelanggan.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('kriteria.bobot') }}" class="rounded-md bg-yellow-500 px-5 py-3 text-white hover:bg-yellow-600">Nilai Bobot Kriteria</a>
            <a href="{{ route('kriteria.create') }}" class="rounded-md bg-merah-terang px-5 py-3 text-white hover:bg-red-600">Tambah Kriteria</a>
        </div>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-sm font-medium text-gray-500">Kode</th>
                    <th class="px-6 py-4 text-sm font-medium text-gray-500">Nama Kriteria</th>
                    <th class="px-6 py-4 text-sm font-medium text-gray-500">Jenis</th>
                    <th class="px-6 py-4 text-sm font-medium text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($kriterias as $item)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $item->kode_kriteria }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $item->nama_kriteria }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 capitalize">{{ $item->jenis }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 space-x-2">
                            <a href="{{ route('kriteria.edit', $item) }}" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-white hover:bg-blue-700">Edit</a>
                            <form action="{{ route('kriteria.destroy', $item) }}" method="POST" class="inline-block delete-form" data-nama="{{ $item->nama_kriteria }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-md bg-red-600 px-3 py-2 text-white hover:bg-red-700">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada data kriteria. Silakan tambahkan kriteria baru.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('form.delete-form').forEach(function(form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const nama = this.getAttribute('data-nama') || 'kriteria';
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan menghapus " + nama,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
</script>
@endpush
