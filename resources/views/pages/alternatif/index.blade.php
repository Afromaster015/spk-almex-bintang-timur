@extends('layouts.app')

@section('title', 'Kelola Alternatif - SPK')

@section('content')
    <div class="flex items-center justify-between mb-10">
        <div>
            <h1 class="text-4xl font-semibold">Kelola Alternatif</h1>
            <p class="text-gray-600 mt-2">Lihat seluruh alternatif yang tersimpan dan tambahkan alternatif baru ke database.</p>
        </div>
        <div>
            <a href="{{ route('alternatif.create') }}" class="rounded-md bg-merah-terang px-5 py-3 text-white hover:bg-red-600">Tambah Alternatif</a>
        </div>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-sm font-medium text-gray-500">Kode Pelanggan</th>
                    <th class="px-6 py-4 text-sm font-medium text-gray-500">Nama Pelanggan</th>
                    <th class="px-6 py-4 text-sm font-medium text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($alternatifs as $item)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $item->kode_alternatif }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $item->nama_pelanggan }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 space-x-2">
                            <a href="{{ route('alternatif.edit', $item) }}" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-white hover:bg-blue-700">Edit</a>
                            <form action="{{ route('alternatif.destroy', $item) }}" method="POST" class="inline-block delete-form" data-nama="{{ $item->nama_pelanggan }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-md bg-red-600 px-3 py-2 text-white hover:bg-red-700">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                            Belum ada data pelanggan. Tambahkan alternatif baru menggunakan tombol "Tambah Alternatif".
                        </td>
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
            const nama = this.getAttribute('data-nama') || 'pelanggan';
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
