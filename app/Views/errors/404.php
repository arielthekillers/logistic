<?php
renderPublicHeader("404 Halaman Tidak Ditemukan");
?>

<div class="min-h-screen flex items-center justify-center bg-gray-50 p-6">
    <div class="max-w-md w-full text-center space-y-6 bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
        <div class="w-20 h-20 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center mx-auto text-4xl">
            <i class="ri-map-pin-user-line"></i>
        </div>
        <h1 class="text-4xl font-extrabold text-gray-900">404</h1>
        <h2 class="text-xl font-bold text-gray-800">Halaman Tidak Ditemukan</h2>
        <p class="text-sm text-gray-500">Halaman yang Anda cari tidak tersedia atau rutenya belum dikonfigurasi.</p>
        <a href="<?= url('/dashboard') ?>" class="inline-block px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm shadow">
            Kembali ke Dashboard
        </a>
    </div>
</div>

<?php renderPublicFooter(); ?>
