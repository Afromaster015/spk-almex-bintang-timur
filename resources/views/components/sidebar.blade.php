<div id="sidebar" class="bg-merah-terang h-screen w-72 p-5 pt-8 duration-300 ease-in-out relative flex flex-col">
            
            <i id="btn-toggle" class="bx bx-chevron-left bg-white text-merah-terang text-2xl w-9 h-9 flex items-center justify-center rounded-full absolute -right-4 top-9 border border-merah-terang cursor-pointer ease-in-out duration-300"></i>

            <a href="{{ route('dashboard') }}">
                <div class="flex items-center h-12 gap-x-4">
                    <i class="bx bx-data text-white text-2xl"></i>
                    <h1 id="logo-text" class="text-white font-bold origin-left ease-in-out duration-300 text-xl">Aplikasi SPK</h1>
                </div>
            </a>
            

            <ul class="pt-6 pl-2 space-y-2 flex-1">
                
                <li>
                    <div onclick="toggleSubmenu('sub-kriteria', 'arrow-kriteria')" class="flex items-center justify-between h-12 p-2 hover:bg-white/20 rounded-md cursor-pointer">
                        <div class="flex items-center gap-x-4">
                            <i class="bx bx-layer text-white text-xl"></i>
                            <span class="text-white menu-text duration-300 ease-in-out mx-2">Menu Kriteria</span>
                        </div>
                        <i id="arrow-kriteria" class="bx bx-chevron-down text-white text-xl duration-300 menu-text"></i>
                    </div>

                    <ul id="sub-kriteria" class="max-h-0 opacity-0 overflow-hidden transition-all duration-300 ease-in-out pl-11 pr-2 space-y-1">
                        <li>
                            <a href="{{ route('kriteria.index') }}" class="text-white text-sm p-2 hover:bg-white/20 rounded-md menu-text duration-300 origin-left block">Kriteria</a>
                        </li>
                        <li>
                            <a href="{{ route('kriteria.bobot') }}" class="text-white text-sm p-2 hover:bg-white/20 rounded-md menu-text duration-300 origin-left block">Nilai Bobot Kriteria</a>
                        </li>
                        <li>
                            <a href="{{ route('kriteria.hasil-bobot') }}" class="text-white text-sm p-2 hover:bg-white/20 rounded-md menu-text duration-300 origin-left block">Hasil Bobot Kriteria</a>
                        </li>
                    </ul>
                </li>

                <li>
                    <div onclick="toggleSubmenu('sub-alternatif', 'arrow-alternatif')" class="flex items-center justify-between h-12 p-2 hover:bg-white/20 rounded-md cursor-pointer">
                        <div class="flex items-center gap-x-4">
                            <i class="bx bx-group text-white text-xl"></i>
                            <span class="text-white menu-text duration-300 ease-in-out mx-2">Menu Alternatif</span>
                        </div>
                        <i id="arrow-alternatif" class="bx bx-chevron-down text-white text-xl duration-300 menu-text"></i>
                    </div>

                    <ul id="sub-alternatif" class="max-h-0 opacity-0 overflow-hidden transition-all duration-300 ease-in-out pl-11 pr-2 space-y-1">
                        <li>
                            <a href="{{ route('alternatif.index') }}" class="text-white text-sm p-2 hover:bg-white/20 rounded-md menu-text duration-300 origin-left block">Kelola Alternatif</a>
                        </li>
                        <li>
                            <a href="{{ route('alternatif.select') }}" class="text-white text-sm p-2 hover:bg-white/20 rounded-md menu-text duration-300 origin-left block">Pilih Alternatif</a>
                        </li>
                        <li>
                            <a href="{{ route('nilai-alternatif.index') }}" class="text-white text-sm p-2 hover:bg-white/20 rounded-md menu-text duration-300 origin-left block">Nilai Bobot Alternatif</a>
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="{{ route('perhitungan.index') }}" class="flex items-center gap-x-4 h-12 p-2 hover:bg-white/20 rounded-md cursor-pointer transition-colors duration-200">
                        <i class="bx bx-calculator text-white text-xl"></i>
                        <span class="text-white menu-text duration-300 ease-in-out mx-2">Perhitungan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('periode.index') }}" class="flex items-center gap-x-4 h-12 p-2 hover:bg-white/20 rounded-md cursor-pointer transition-colors duration-200">
                        <i class="bx bx-calendar text-white text-xl"></i>
                        <span class="text-white menu-text duration-300 ease-in-out mx-2">Periode</span>
                    </a>
                </li>

            </ul>

            <ul class="pl-2 border-t border-white/20 pt-4 mt-auto">
                <li>
                    <a href="#" onclick="logoutAman(event)" class="flex items-center gap-x-4 h-12 p-2 hover:bg-white/20 rounded-md cursor-pointer transition-colors duration-200">
                        <i class="bx bx-log-out text-white text-xl"></i>
                        <span class="text-white menu-text duration-300 ease-in-out mx-2">Logout</span>
                    </a>
                </li>
            </ul>

            <script>
                function logoutAman(event) {
                    event.preventDefault(); // Mencegah halaman melompat ke atas karena href="#"
                    
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Anda akan keluar dari sesi ini.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#9ca3af',
                        confirmButtonText: 'Ya, Logout!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Helper url() Laravel akan memastikan alamatnya 100% akurat!
                            window.location.href = "{{ url('/logout') }}";
                        }
                    });
                }
            </script>
            
        </div>