<?php
$activeMenu = 'komentar';
require __DIR__ . '/layout_header.php';
?>

<h1 class="text-xl font-bold text-gray-800 mb-6">Kelola Komentar</h1>

<?php if (empty($comments)): ?>
<div class="bg-white rounded-2xl border border-gray-200 p-8 text-center text-gray-400">
    Belum ada komentar.
</div>
<?php else: ?>
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
            <tr>
                <th class="px-4 py-3 text-left">Nama</th>
                <th class="px-4 py-3 text-left">Komentar</th>
                <th class="px-4 py-3 text-left">Hewan</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Tanggal</th>
                <th class="px-4 py-3 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        <?php foreach ($comments as $c): ?>
        <tr class="hover:bg-gray-50 <?= !$c['approved'] ? 'bg-yellow-50' : '' ?>">
            <td class="px-4 py-3 font-semibold"><?= htmlspecialchars($c['nama'], ENT_QUOTES) ?></td>
            <td class="px-4 py-3 text-gray-700 max-w-xs">
                <div class="line-clamp-2"><?= htmlspecialchars($c['isi'], ENT_QUOTES) ?></div>
            </td>
            <td class="px-4 py-3">
                <a href="<?= BASE_URL ?>/hewan/<?= htmlspecialchars($c['animal_slug'], ENT_QUOTES) ?>"
                   class="text-green-700 hover:underline" target="_blank">
                    <?= htmlspecialchars($c['animal_name'], ENT_QUOTES) ?>
                </a>
            </td>
            <td class="px-4 py-3">
                <?php if ($c['approved']): ?>
                <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">Disetujui</span>
                <?php else: ?>
                <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-0.5 rounded-full">Menunggu</span>
                <?php endif; ?>
            </td>
            <td class="px-4 py-3 text-gray-400 text-xs">
                <?= date('d/m/Y H:i', strtotime($c['created_at'])) ?>
            </td>
            <td class="px-4 py-3">
                <div class="flex gap-2">
                    <?php if (!$c['approved']): ?>
                    <form method="POST" action="<?= BASE_URL ?>/admin/komentar/<?= $c['id'] ?>/setujui">
                        <button type="submit" class="text-green-600 hover:underline text-xs">Setujui</button>
                    </form>
                    <?php endif; ?>
                    <form method="POST" action="<?= BASE_URL ?>/admin/komentar/<?= $c['id'] ?>/hapus"
                          onsubmit="return confirm('Hapus komentar ini?')">
                        <button type="submit" class="text-red-500 hover:underline text-xs">Hapus</button>
                    </form>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php require __DIR__ . '/layout_footer.php'; ?>
