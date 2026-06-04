<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Lupa Password</title>
    @include('components.header')
</head>
<body>
    <section class="bg-gray-100 min-h-screen flex items-center justify-center">
        <div class="bg-white flex max-w-6xl rounded-2xl shadow-lg p-5">
            <a href="{{ url('/') }}" class="w-1/2 sm:block hidden ">
                <img src="{{ asset('images/login-image.jpg') }}" class="rounded-2xl hover:scale-[101%] duration-300" alt="Forgot Password Image" />
            </a>
            <div class="w-1/2 flex flex-col items-center justify-center">
                <h2 class="text-4xl font-bold text-center mb-8">Lupa Password</h2>
                <form action="{{ url('/proses-forgot-password') }}" class="flex flex-col gap-4 w-[400px]" method="POST">
                    @csrf
                    <input class="p-2 rounded-xl border border-gray-300" type="text" name="username" placeholder="Username" required>
                    @error('username')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    
                    <input class="p-2 rounded-xl border border-gray-300" type="text" name="hint" placeholder="Hint" required>
                    @error('hint')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    
                    <div class="flex gap-4 mt-8">
                        <button class="bg-merah-terang text-white py-2 px-4 rounded-xl hover:bg-merah-gelap hover:scale-105 duration-300 hover:text-[18px] hover:font-bold ease-in-out flex-1" type="submit">Kirim</button>
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
                    title: 'Username atau Hint Salah!',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'Coba Lagi'
                });
            });
        </script>
    @endif

    @if (session('password_found'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Password Ditemukan!',
                    text: "Password Anda: {{ session('password_found') }}",
                    confirmButtonColor: '#10b981',
                    confirmButtonText: 'OK',
                    timer: 5000,
                    timerProgressBar: true
                }).then(function() {
                    window.location.href = "{{ url('/login') }}";
                });
            });
        </script>
    @endif
</body>
</html>
