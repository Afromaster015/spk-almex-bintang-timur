<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Buat Akun</title>
    @include('components.header')
</head>
<body>
    <section class="bg-gray-100 min-h-screen flex items-center justify-center">
        <div class="bg-white flex max-w-6xl rounded-2xl shadow-lg p-5">
            <a href="{{ url('/') }}" class="w-1/2 sm:block hidden ">
                <img src="{{ asset('images/login-image.jpg') }}" class="rounded-2xl hover:scale-[101%] duration-300" alt="Register Image" />
            </a>
            <div class="w-1/2 flex flex-col items-center justify-center">
                <h2 class="text-4xl font-bold text-center mb-8">Buat Akun</h2>
                <form action="{{ url('/proses-register') }}" class="flex flex-col gap-4 w-[400px]" method="POST">
                    @csrf
                    <input class="p-2 rounded-xl border border-gray-300" type="text" name="username" placeholder="Username" required>
                    @error('username')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    
                    <input class="p-2 rounded-xl border border-gray-300" type="password" name="password" placeholder="Password" required>
                    @error('password')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    
                    <input class="p-2 rounded-xl border border-gray-300" type="text" name="name" placeholder="Nama Lengkap" required>
                    @error('name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    
                    <label class="mt-4 text-gray-700 font-semibold">Hint?</label>
                    <input class="p-2 rounded-xl border border-gray-300" type="text" name="hint" placeholder="Nama Hewan Peliharaan" required>
                    @error('hint')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    
                    <div class="flex gap-4 mt-8">
                        <button class="bg-merah-terang text-white py-2 px-4 rounded-xl hover:bg-merah-gelap hover:scale-105 duration-300 hover:text-[18px] hover:font-bold ease-in-out flex-1" type="submit">Buat</button>
                        <a href="{{ url('/login') }}" class="bg-gray-400 text-white py-2 px-4 rounded-xl hover:bg-gray-500 hover:scale-105 duration-300 hover:text-[18px] hover:font-bold ease-in-out text-center flex-1">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </section>

    @if (session('error'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Pendaftaran Gagal!',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'Coba Lagi'
                });
            });
        </script>
    @endif

    @if (session('success'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Pendaftaran Berhasil!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 2000
                }).then(function() {
                    window.location.href = "{{ url('/login') }}";
                });
            });
        </script>
    @endif
</body>
</html>
