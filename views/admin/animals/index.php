<?php
$activeMenu = 'hewan';
require __DIR__ . '/../layout_header.php';
?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-bold text-gray-800">Kelola Hewan</h1>
    <a href="<?= BASE_URL ?>/admin/hewan/tambah"
       class="bg-green-700 hover:bg-green-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
        + Tambah Hewan
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
            <tr>
                <th class="px-4 py-3 text-left">Foto</th>
                <th class="px-4 py-3 text-left">Nama</th>
                <th class="px-4 py-3 text-left">Jenis</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Endemik</th>
                <th class="px-4 py-3 text-left">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        <?php foreach ($animals as $a): ?>
        <tr class="hover:bg-gray-50">
            <td class="px-4 py-2">
                <img src="<?= htmlspecialchars($a['image_url'] ?? '', ENT_QUOTES) ?>"
                     alt="<?= htmlspecialchars($a['name'], ENT_QUOTES) ?>"
                     class="w-12 h-10 object-cover rounded-lg bg-gray-100"
                     onerror="this.src='https://placehold.co/48x40/e8f5e9/2d7a4f?text=?'">
            </td>
            <td class="px-4 py-2">
                <div class="font-semibold text-gray-800"><?= htmlspecialchars($a['name'], ENT_QUOTES) ?></div>
                <div class="text-gray-400 text-xs italic"><?= htmlspecialchars($a['latin_name'], ENT_QUOTES) ?></div>
            </td>
            <td class="px-4 py-2 text-gray-600"><?= htmlspecialchars($a['type'], ENT_QUOTES) ?></td>
            <td class="px-4 py-2">
                <span class="px-2 py-0.5 rounded-full text-xs font-bold
                    <?= match($a['status']) {
                        'CR' => 'bg-red-100 text-red-700',
                        'EN' => 'bg-orange-100 text-orange-700',
                        'VU' => 'bg-yellow-100 text-yellow-700',
                        'NT' => 'bg-blue-100 text-blue-700',
                        'LC' => 'bg-green-100 text-green-700',
                        default => 'bg-gray-100 text-gray-600'
                    } ?>">
                    <?= htmlspecialchars($a['status'], ENT_QUOTES) ?>
                </span>
            </td>
            <td class="px-4 py-2 text-center"><?= $a['is_endemic'] ? '✓' : '—' ?></td>
            <td class="px-4 py-2">
                <div class="flex gap-2">
                    <a href="<?= BASE_URL ?>/admin/hewan/<?= $a['id'] ?>/edit"
                       class="text-blue-600 hover:underline text-xs">Edit</a>
                    <form method="POST" action="<?= BASE_URL ?>/admin/hewan/<?= $a['id'] ?>/hapus"
                          onsubmit="return confirm('Hapus <?= htmlspecialchars($a['name'], ENT_QUOTES) ?>?')">
                        <button type="submit" class="text-red-500 hover:underline text-xs">Hapus</button>
                    </form>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../layout_footer.php'; ?>
