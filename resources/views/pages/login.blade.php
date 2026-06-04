<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    @include('components.header')
</head>
<body>
    <section class="bg-gray-100 min-h-screen flex items-center justify-center">
        <div class="bg-white flex max-w-6xl rounded-2xl shadow-lg p-5">
            <div class="w-1/2 flex flex-col items-center justify-center">
                <h2 class="text-4xl font-bold text-center">Aplikasi SPK</h2>
                <form action="{{ url('/proses-login') }}" class="flex flex-col gap-4 w-[400px]" method="POST">
                    @csrf
                    <input class="p-2 mt-16 rounded-xl border border-gray-300" type="text" name="username" placeholder="Username">
                    <input class="p-2 rounded-xl border border-gray-300" type="password" name="password" placeholder="Password">
                    <button class="bg-merah-terang text-white py-2 px-4 rounded-xl hover:bg-merah-gelap hover:scale-105 duration-300 hover:text-[18px] hover:font-bold ease-in-out mt-8" type="submit" name="login">Login</button>
                    
                    <div class="text-center mt-6 text-sm">
                        <a href="{{ url('/forgot-password') }}" class="text-blue-600 hover:text-blue-800 hover:underline">Lupa password?</a>
                        <span class="text-gray-400 mx-2">|</span>
                        <a href="{{ url('/register') }}" class="text-blue-600 hover:text-blue-800 hover:underline">Buat akun</a>
                    </div>
                    </form>
            </div>
                <a href="{{ url('/') }}" class="w-1/2 sm:block hidden ">
                    <img src="{{ asset('images/login-image.jpg') }}" class="rounded-2xl hover:scale-[101%] duration-300" alt="Login Image" />
                </a>
            </div>
    </section>

    @if (session('error'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Login Gagal!',
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
                    title: 'Login Berhasil!',
                    text: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 2000 // Muncul selama 2 detik
                }).then(function() {
                    // Setelah 2 detik selesai, barulah JavaScript memindahkan halaman ke dashboard
                    window.location.href = "{{ url('/dashboard') }}";
                });
            });
        </script>
    @endif
</body>
</html>