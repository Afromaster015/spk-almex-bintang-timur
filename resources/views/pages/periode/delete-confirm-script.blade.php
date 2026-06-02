@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form.delete-periode').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const nama = form.getAttribute('data-nama') || 'periode';
                Swal.fire({
                    title: 'Hapus periode?',
                    text: 'Anda akan menghapus ' + nama + '. Semua alternatif dan nilai yang terkait akan ikut terhapus. Lanjutkan?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#9ca3af',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush
