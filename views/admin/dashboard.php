<?php
$activeMenu = 'dashboard';
require __DIR__ . '/layout_header.php';
?>

<h1 class="text-xl font-bold text-gray-800 mb-6">Dashboard</h1>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
        <div class="text-3xl font-bold text-green-700"><?= $totalAnimals ?></div>
        <div class="text-sm text-gray-500 mt-1">Total Hewan</div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
        <div class="text-3xl font-bold text-yellow-600"><?= $totalComments ?></div>
        <div class="text-sm text-gray-500 mt-1">Komentar Menunggu</div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
        <a href="<?= BASE_URL ?>/admin/hewan/tambah"
           class="block bg-green-700 hover:bg-green-800 text-white text-center font-semibold py-3 rounded-xl text-sm transition">
            + Tambah Hewan Baru
        </a>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-200 p-5">
    <h2 class="font-semibold text-gray-700 mb-4">Komentar Terbaru</h2>
    <?php if (empty($recentComments)): ?>
    <p class="text-gray-400 text-sm">Belum ada komentar.</p>
    <?php else: ?>
    <div class="divide-y divide-gray-100">
        <?php foreach (array_slice($recentComments, 0, 5) as $c): ?>
        <div class="py-3 flex items-start justify-between gap-4">
            <div>
                <span class="font-semibold text-sm"><?= htmlspecialchars($c['nama'], ENT_QUOTES) ?></span>
                <span class="text-gray-400 text-xs ml-2">pada <?= htmlspecialchars($c['animal_name'], ENT_QUOTES) ?></span>
                <?php if (!$c['approved']): ?>
                <span class="ml-2 bg-yellow-100 text-yellow-700 text-xs px-2 py-0.5 rounded-full">Menunggu</span>
                <?php endif; ?>
                <p class="text-gray-600 text-sm mt-0.5"><?= htmlspecialchars(mb_substr($c['isi'], 0, 100), ENT_QUOTES) ?>...</p>
            </div>
            <a href="<?= BASE_URL ?>/admin/komentar" class="text-xs text-green-700 whitespace-nowrap">Lihat</a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/layout_footer.php'; ?>
