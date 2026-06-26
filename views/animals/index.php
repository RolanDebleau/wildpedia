<?php
use App\Models\Animal;

$pageTitle   = 'Ensiklopedia Hewan Indonesia';
$currentPage = 'animals';

require __DIR__ . '/../layouts/header.php';
?>

<!-- HERO -->
<div class="bg-forest-950 text-white">
    <div class="max-w-7xl mx-auto px-4 py-10">
        <h1 class="text-3xl font-bold mb-1">Ensiklopedia Hewan Nusantara</h1>
        <p class="text-forest-300 mb-6">Temukan informasi lengkap hewan-hewan Indonesia &mdash; dari yang langka hingga yang umum dijumpai</p>

        <!-- Statistik -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            <?php
            $statItems = [
                ['num' => $stats['total'],   'label' => 'Total Hewan',   'color' => 'bg-white/10'],
                ['num' => $stats['cr'],      'label' => 'Kritis (CR)',   'color' => 'bg-red-900/50'],
                ['num' => $stats['en'],      'label' => 'Terancam (EN)', 'color' => 'bg-orange-900/50'],
                ['num' => $stats['vu'],      'label' => 'Rentan (VU)',   'color' => 'bg-yellow-900/50'],
                ['num' => $stats['endemic'], 'label' => 'Endemik',       'color' => 'bg-forest-900/80'],
            ];
            ?>
            <?php foreach ($statItems as $s): ?>
            <div class="<?= $s['color'] ?> rounded-xl p-3 text-center border border-white/10">
                <div class="text-2xl font-bold"><?= (int) $s['num'] ?></div>
                <div class="text-xs text-gray-300"><?= htmlspecialchars($s['label'], ENT_QUOTES) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- FILTER & SEARCH -->
<div class="bg-white border-b border-gray-200 sticky top-[56px] z-40 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 py-3">
        <form method="GET" action="<?= BASE_URL ?>/hewan" class="flex flex-wrap gap-2 items-center">

            <input type="text" name="search"
                   value="<?= htmlspecialchars($filters['search'] ?? '', ENT_QUOTES) ?>"
                   placeholder="Cari nama hewan, habitat, atau jenis..."
                   class="flex-1 min-w-52 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">

            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">Semua Status</option>
                <?php
                $statusLabels = [
                    'CR' => 'Kritis (CR)',
                    'EN' => 'Terancam (EN)',
                    'VU' => 'Rentan (VU)',
                    'NT' => 'Hampir Terancam (NT)',
                    'LC' => 'Tidak Terancam (LC)',
                ];
                foreach ($statuses as $st): ?>
                <option value="<?= $st ?>" <?= ($filters['status'] ?? '') === $st ? 'selected' : '' ?>>
                    <?= $statusLabels[$st] ?? $st ?>
                </option>
                <?php endforeach; ?>
            </select>

            <select name="type" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">Semua Jenis</option>
                <?php foreach ($types as $t): ?>
                <option value="<?= htmlspecialchars($t, ENT_QUOTES) ?>" <?= ($filters['type'] ?? '') === $t ? 'selected' : '' ?>>
                    <?= htmlspecialchars($t, ENT_QUOTES) ?>
                </option>
                <?php endforeach; ?>
            </select>

            <label class="flex items-center gap-2 text-sm cursor-pointer border border-gray-300 rounded-lg px-3 py-2">
                <input type="checkbox" name="endemic" value="1"
                       <?= !empty($filters['endemic']) ? 'checked' : '' ?>
                       class="accent-green-600">
                Endemik Indonesia
            </label>

            <button type="submit"
                    class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                Cari
            </button>

            <?php if (array_filter($filters)): ?>
            <a href="<?= BASE_URL ?>/hewan"
               class="text-sm text-gray-500 hover:text-red-600 px-2 py-2 transition">
                &times; Reset
            </a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- GRID HEWAN -->
<div class="max-w-7xl mx-auto px-4 py-6">

    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-gray-500">
            Menampilkan <strong><?= (int) $animals['total'] ?></strong> hewan
            <?php if (!empty($filters['search'])): ?>
                untuk pencarian &ldquo;<strong><?= htmlspecialchars($filters['search'], ENT_QUOTES) ?></strong>&rdquo;
            <?php endif; ?>
        </p>
        <span class="text-xs text-gray-400">
            <?= $animals['current_page'] ?>/<?= $animals['last_page'] ?> halaman
        </span>
    </div>

    <?php if (empty($animals['data'])): ?>
    <div class="text-center py-20">
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Hewan tidak ditemukan</h3>
        <p class="text-gray-500 mb-4">Coba kata kunci lain atau hapus filter</p>
        <a href="<?= BASE_URL ?>/hewan" class="text-green-700 hover:underline">Lihat semua hewan &rarr;</a>
    </div>
    <?php else: ?>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
        <?php foreach ($animals['data'] as $animal): ?>
        <a href="<?= BASE_URL ?>/hewan/<?= htmlspecialchars($animal['slug'], ENT_QUOTES) ?>"
           class="bg-white rounded-2xl overflow-hidden border border-gray-200 card-hover group block">

            <div class="relative">
                <img src="<?= htmlspecialchars($animal['image_url'] ?? 'https://placehold.co/400x300/e8f5e9/2d7a4f?text=' . urlencode($animal['name']), ENT_QUOTES) ?>"
                     alt="<?= htmlspecialchars($animal['name'], ENT_QUOTES) ?>"
                     class="w-full h-36 object-cover group-hover:scale-105 transition-transform duration-300"
                     onerror="this.src='https://placehold.co/400x300/e8f5e9/2d7a4f?text=No+Image'">

                <span class="absolute top-2 right-2 text-xs font-bold px-2 py-0.5 rounded-full status-<?= strtolower($animal['status']) ?>">
                    <?= htmlspecialchars($animal['status'], ENT_QUOTES) ?>
                </span>

                <?php if ($animal['is_endemic']): ?>
                <span class="absolute top-2 left-2 text-xs bg-green-700 text-white px-2 py-0.5 rounded-full">
                    Endemik
                </span>
                <?php endif; ?>
            </div>

            <div class="p-3">
                <h3 class="font-semibold text-sm text-gray-800 leading-tight">
                    <?= htmlspecialchars($animal['name'], ENT_QUOTES) ?>
                </h3>
                <p class="text-xs text-gray-400 italic mt-0.5 truncate">
                    <?= htmlspecialchars($animal['latin_name'], ENT_QUOTES) ?>
                </p>
                <p class="text-xs text-gray-500 mt-1.5 bg-gray-50 rounded px-2 py-0.5 inline-block">
                    <?= htmlspecialchars($animal['type'], ENT_QUOTES) ?>
                </p>
            </div>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($animals['last_page'] > 1): ?>
    <div class="mt-8 flex justify-center gap-1 flex-wrap">
        <?php
        $currentFilters = array_filter($filters);

        // Tombol prev
        if ($animals['current_page'] > 1):
            $prevUrl = BASE_URL . '/hewan?' . http_build_query($currentFilters + ['page' => $animals['current_page'] - 1]);
        ?>
        <a href="<?= $prevUrl ?>"
           class="px-3 py-1.5 rounded-lg border border-gray-300 text-sm text-gray-600 hover:bg-gray-50">&laquo;</a>
        <?php endif; ?>

        <?php for ($p = max(1, $animals['current_page'] - 2); $p <= min($animals['last_page'], $animals['current_page'] + 2); $p++):
            $pageUrl = BASE_URL . '/hewan?' . http_build_query($currentFilters + ['page' => $p]);
            $isActive = $p === $animals['current_page'];
        ?>
        <a href="<?= $pageUrl ?>"
           class="px-3 py-1.5 rounded-lg border text-sm
                  <?= $isActive ? 'bg-green-700 text-white border-green-700 font-semibold' : 'border-gray-300 text-gray-600 hover:bg-gray-50' ?>">
            <?= $p ?>
        </a>
        <?php endfor; ?>

        <?php if ($animals['current_page'] < $animals['last_page']):
            $nextUrl = BASE_URL . '/hewan?' . http_build_query($currentFilters + ['page' => $animals['current_page'] + 1]);
        ?>
        <a href="<?= $nextUrl ?>"
           class="px-3 py-1.5 rounded-lg border border-gray-300 text-sm text-gray-600 hover:bg-gray-50">&raquo;</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php endif; ?>
</div>

<!-- CTA Identifikasi -->
<div class="bg-forest-950 text-white py-10 mt-8">
    <div class="max-w-2xl mx-auto text-center px-4">
        <h2 class="text-xl font-bold mb-2">Temukan Hewan Sekitarmu</h2>
        <p class="text-forest-300 mb-5 text-sm">
            Upload foto hewan yang kamu jumpai &mdash; kami akan identifikasi jenisnya dan tampilkan informasi lengkapnya
        </p>
        <a href="<?= BASE_URL ?>/identifikasi"
           class="bg-green-600 hover:bg-green-500 text-white px-6 py-3 rounded-xl font-semibold text-sm transition inline-block">
            Coba Identifikasi Foto &rarr;
        </a>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
