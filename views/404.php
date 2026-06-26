<?php
$pageTitle   = '404 &mdash; Halaman Tidak Ditemukan';
$currentPage = '';

require __DIR__ . '/layouts/header.php';
?>

<div class="max-w-xl mx-auto px-4 py-24 text-center">
    <h1 class="text-6xl font-bold text-gray-200 mb-4">404</h1>
    <h2 class="text-2xl font-semibold text-gray-700 mb-3">Halaman Tidak Ditemukan</h2>
    <p class="text-gray-500 mb-8">Halaman yang kamu cari tidak ada atau telah dipindahkan.</p>
    <a href="<?= BASE_URL ?>/hewan"
       class="bg-green-700 hover:bg-green-600 text-white px-6 py-3 rounded-xl font-semibold text-sm transition inline-block">
        Kembali ke Beranda
    </a>
</div>

<?php require __DIR__ . '/layouts/footer.php'; ?>
