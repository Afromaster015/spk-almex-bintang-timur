const sidebar = document.getElementById("sidebar");
const btnToggle = document.getElementById("btn-toggle");
const logoText = document.getElementById("logo-text");
const menuTexts = document.querySelectorAll(".menu-text");

// Fungsi untuk membuka/menutup sidebar
function toggleSidebar() {
    // Logika mengubah ukuran sidebar dan memutar ikon toggle
    sidebar.classList.toggle("w-72");
    sidebar.classList.toggle("w-20");
    btnToggle.classList.toggle("rotate-180");

    // Logika untuk menyembunyikan teks logo dan menu saat sidebar diperkecil
    if (logoText) {
        logoText.classList.toggle("scale-0");
    }

    menuTexts.forEach((text) => {
        text.classList.toggle("scale-0");
        text.classList.toggle("origin-left");
    });

    // 3. Menutup sub menu yang terbuka saat sidebar diperkecil
    if (sidebar.classList.contains("w-20")) {
        // Mendaftarkan ID submenu dan panah
        const daftarSubmenu = [
            { idSub: "sub-kriteria", idArrow: "arrow-kriteria" },
            { idSub: "sub-alternatif", idArrow: "arrow-alternatif" },
        ];

        // Looping untuk mengecek semua submenu
        daftarSubmenu.forEach((menu) => {
            const submenu = document.getElementById(menu.idSub);
            const arrow = document.getElementById(menu.idArrow);

            // Jika submenu memiliki class 'max-h-[200px]', artinya sedang terbuka
            if (submenu.classList.contains("max-h-[200px]")) {
                // Hapus class pembuka
                submenu.classList.remove(
                    "max-h-[200px]",
                    "opacity-100",
                    "mt-1",
                    "mb-2",
                );
                // Kembalikan class penutup
                submenu.classList.add("max-h-0", "opacity-0");
                // Putar kembali panah indikatornya ke posisi semula
                arrow.classList.remove("rotate-180");
            }
        });
    }
}

// Untuk hide dan show sidebar dengan tombol toggle
btnToggle.addEventListener("click", function () {
    toggleSidebar();
});

// Untuk membuka sidebar ketika diklik saat sidebar tertutup
sidebar.addEventListener("click", function (event) {
    // Jika sidebar sedang tertutup (memiliki class w-20) dan bukan tombol toggle yang diklik
    if (
        sidebar.classList.contains("w-20") &&
        event.target !== btnToggle &&
        !btnToggle.contains(event.target)
    ) {
        toggleSidebar();
    }
});

// Fungsi untuk mengatur klik Submenu
function toggleSubmenu(submenuId, arrowId) {
    const submenu = document.getElementById(submenuId);
    const arrow = document.getElementById(arrowId);

    // Tukar class tinggi dan transparansi untuk animasi meluncur
    submenu.classList.toggle("max-h-0");
    submenu.classList.toggle("max-h-[200px]"); // 200px cukup untuk menampung beberapa list menu
    submenu.classList.toggle("opacity-0");
    submenu.classList.toggle("opacity-100");

    // (Opsional) Tambahkan sedikit jarak atas/bawah saat terbuka
    submenu.classList.toggle("mt-1");
    submenu.classList.toggle("mb-2");

    // Putar panah 180 derajat
    arrow.classList.toggle("rotate-180");
}

function konfirmasiLogout(event) {
    event.preventDefault();

    Swal.fire({
        title: "Konfirmasi",
        text: "Apakah anda yakin ingin keluar dari Aplikasi SPK ?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ef4444",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Ya",
        cancelButtonText: "Tidak",
    }).then((result) => {
        // Jika user menekan tombol 'Ya'
        if (result.isConfirmed) {
            window.location.href = "/logout";
        }
        // Jika memilih 'Tidak'
    });
}
