<?php
use App\Models\Animal;

$pageTitle   = htmlspecialchars($animal['name'] . ' — ' . $animal['latin_name'], ENT_QUOTES);
$currentPage = 'animals';

require __DIR__ . '/../layouts/header.php';

$statusColor = Animal::statusColor($animal['status']);
$statusLabel = Animal::statusLabel($animal['status']);
?>

<div class="max-w-5xl mx-auto px-4 py-8">

    <!-- Breadcrumb -->
    <nav class="text-sm text-gray-500 mb-5">
        <a href="<?= BASE_URL ?>/hewan" class="hover:text-green-700">Ensiklopedia</a>
        <span class="mx-2">&rsaquo;</span>
        <a href="<?= BASE_URL ?>/hewan?type=<?= urlencode($animal['type']) ?>" class="hover:text-green-700">
            <?= htmlspecialchars($animal['type'], ENT_QUOTES) ?>
        </a>
        <span class="mx-2">&rsaquo;</span>
        <span class="text-gray-800"><?= htmlspecialchars($animal['name'], ENT_QUOTES) ?></span>
    </nav>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-8">

        <!-- KOLOM KIRI: Foto + Info Cepat -->
        <div class="md:col-span-2 space-y-4">

            <div class="rounded-2xl overflow-hidden border border-gray-200 shadow-sm">
                <img src="<?= htmlspecialchars($animal['image_url'] ?? 'https://placehold.co/600x450/e8f5e9/2d7a4f?text=' . urlencode($animal['name']), ENT_QUOTES) ?>"
                     alt="<?= htmlspecialchars($animal['name'], ENT_QUOTES) ?>"
                     class="w-full h-64 md:h-72 object-cover"
                     onerror="this.src='https://placehold.co/600x450/e8f5e9/2d7a4f?text=No+Image'">
            </div>

            <!-- Badge status + endemik -->
            <div class="flex flex-wrap gap-2">
                <span class="px-4 py-1.5 rounded-full text-sm font-bold border <?= $statusColor ?>">
                    <?= htmlspecialchars($animal['status'], ENT_QUOTES) ?> &mdash; <?= htmlspecialchars($statusLabel, ENT_QUOTES) ?>
                </span>
                <?php if ($animal['is_endemic']): ?>
                <span class="bg-green-100 text-green-800 px-3 py-1.5 rounded-full text-sm font-medium border border-green-200">
                    Endemik Indonesia
                </span>
                <?php endif; ?>
            </div>

            <!-- Tabel info singkat -->
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                <table class="w-full text-sm">
                    <?php
                    $rows = [
                        ['Jenis',    $animal['type']],
                        ['Habitat',  $animal['habitat']],
                        ['Makanan',  $animal['food']],
                        ['Populasi', $animal['population'] ?? 'Tidak diketahui'],
                        ['Ukuran',   $animal['size']       ?? 'Tidak diketahui'],
                    ];
                    foreach ($rows as $i => [$label, $val]):
                    ?>
                    <tr class="<?= $i % 2 === 0 ? 'bg-gray-50' : 'bg-white' ?>">
                        <td class="px-4 py-2.5 text-gray-500 font-medium w-28"><?= htmlspecialchars($label, ENT_QUOTES) ?></td>
                        <td class="px-4 py-2.5 text-gray-800"><?= htmlspecialchars((string) $val, ENT_QUOTES) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <!-- Ancaman -->
            <?php if (!empty($animal['threats'])): ?>
            <div class="bg-red-50 rounded-2xl border border-red-100 p-4">
                <h3 class="text-sm font-semibold text-red-700 mb-2">Ancaman Utama</h3>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($animal['threats'] as $threat): ?>
                    <span class="bg-red-100 text-red-700 text-xs px-3 py-1 rounded-full border border-red-200">
                        <?= htmlspecialchars(($threat['icon'] ? $threat['icon'] . ' ' : '') . $threat['name'], ENT_QUOTES) ?>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- KOLOM KANAN: Detail -->
        <div class="md:col-span-3 space-y-5">

            <div>
                <h1 class="text-3xl font-bold text-gray-900"><?= htmlspecialchars($animal['name'], ENT_QUOTES) ?></h1>
                <p class="text-gray-400 italic text-base mt-1"><?= htmlspecialchars($animal['latin_name'], ENT_QUOTES) ?></p>
            </div>

            <!-- Deskripsi -->
            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
                <h2 class="font-semibold text-gray-800 mb-3">Deskripsi</h2>
                <p class="text-gray-700 leading-relaxed text-sm">
                    <?= htmlspecialchars($animal['description'], ENT_QUOTES) ?>
                </p>
            </div>

            <!-- Fakta Menarik -->
            <?php if (!empty($animal['fun_fact'])): ?>
            <div class="bg-amber-50 rounded-2xl border border-amber-200 p-5">
                <h2 class="font-semibold text-amber-800 mb-2">Fakta Menarik</h2>
                <p class="text-amber-900 text-sm leading-relaxed">
                    <?= htmlspecialchars($animal['fun_fact'], ENT_QUOTES) ?>
                </p>
            </div>
            <?php endif; ?>

            <!-- Skala Status Konservasi IUCN -->
            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
                <h2 class="font-semibold text-gray-800 mb-4">Skala Status Konservasi IUCN</h2>
                <div class="flex items-center gap-1 flex-wrap">
                    <?php
                    $scale = [
                        'EX' => ['label' => 'EX', 'color' => 'bg-gray-900 text-white',   'title' => 'Punah'],
                        'EW' => ['label' => 'EW', 'color' => 'bg-gray-700 text-white',   'title' => 'Punah di Alam'],
                        'CR' => ['label' => 'CR', 'color' => 'bg-red-600 text-white',    'title' => 'Kritis'],
                        'EN' => ['label' => 'EN', 'color' => 'bg-orange-500 text-white', 'title' => 'Terancam'],
                        'VU' => ['label' => 'VU', 'color' => 'bg-yellow-500 text-white', 'title' => 'Rentan'],
                        'NT' => ['label' => 'NT', 'color' => 'bg-blue-400 text-white',   'title' => 'Hampir Terancam'],
                        'LC' => ['label' => 'LC', 'color' => 'bg-green-500 text-white',  'title' => 'Tidak Terancam'],
                    ];
                    foreach ($scale as $code => $info):
                        $isActive = $animal['status'] === $code;
                    ?>
                    <div class="text-center">
                        <div class="px-2.5 py-1.5 rounded-lg text-xs font-bold <?= $info['color'] ?>
                             <?= $isActive ? 'ring-4 ring-offset-1 ring-gray-400 scale-110 z-10 relative' : 'opacity-50' ?>
                             transition-all">
                            <?= $info['label'] ?>
                        </div>
                        <div class="text-xs text-gray-400 mt-1 hidden sm:block" style="font-size:9px">
                            <?= htmlspecialchars($info['title'], ENT_QUOTES) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <p class="text-xs text-gray-500 mt-3">
                    Status <strong><?= htmlspecialchars($statusLabel, ENT_QUOTES) ?></strong> menurut IUCN Red List
                </p>
            </div>

            <!-- Upaya Konservasi -->
            <?php if (!empty($animal['conservation_action'])): ?>
            <div class="bg-green-50 rounded-2xl border border-green-200 p-5">
                <h2 class="font-semibold text-green-800 mb-2">Upaya Konservasi</h2>
                <p class="text-green-900 text-sm leading-relaxed">
                    <?= htmlspecialchars($animal['conservation_action'], ENT_QUOTES) ?>
                </p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- HEWAN TERKAIT -->
    <?php if (!empty($related)): ?>
    <div class="mt-12">
        <h2 class="text-xl font-bold text-gray-800 mb-4">
            Hewan Terkait (<?= htmlspecialchars($animal['type'], ENT_QUOTES) ?>)
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <?php foreach ($related as $rel): ?>
            <a href="<?= BASE_URL ?>/hewan/<?= htmlspecialchars($rel['slug'], ENT_QUOTES) ?>"
               class="bg-white rounded-2xl overflow-hidden border border-gray-200 card-hover block">
                <img src="<?= htmlspecialchars($rel['image_url'] ?? 'https://placehold.co/300x200/e8f5e9/2d7a4f?text=' . urlencode($rel['name']), ENT_QUOTES) ?>"
                     alt="<?= htmlspecialchars($rel['name'], ENT_QUOTES) ?>"
                     class="w-full h-28 object-cover"
                     onerror="this.src='https://placehold.co/300x200/e8f5e9/2d7a4f?text=No+Image'">
                <div class="p-3">
                    <p class="font-semibold text-sm"><?= htmlspecialchars($rel['name'], ENT_QUOTES) ?></p>
                    <span class="text-xs px-2 py-0.5 rounded-full status-<?= strtolower($rel['status']) ?>">
                        <?= htmlspecialchars($rel['status'], ENT_QUOTES) ?>
                    </span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- KOMENTAR -->
    <div class="mt-12" id="komentar">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Komentar</h2>

        <?php
        $comments = \App\Models\Comment::allByAnimal((int) $animal['id'], true);
        ?>

        <?php if (!empty($_SESSION['comment_success'])): ?>
        <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-xl px-4 py-3 mb-4">
            <?= htmlspecialchars($_SESSION['comment_success'], ENT_QUOTES) ?>
            <?php unset($_SESSION['comment_success']); ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['comment_error'])): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-4">
            <?= htmlspecialchars($_SESSION['comment_error'], ENT_QUOTES) ?>
            <?php unset($_SESSION['comment_error']); ?>
        </div>
        <?php endif; ?>

        <!-- Form komentar -->
        <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-6">
            <h3 class="font-semibold text-gray-700 mb-4 text-sm">Tinggalkan Komentar</h3>

            <?php if (empty($_SESSION['user_logged_in'])): ?>
            <div class="bg-stone-50 border border-gray-200 rounded-xl p-4 text-sm text-gray-600">
                Kamu perlu masuk untuk bisa berkomentar.
                <a href="<?= BASE_URL ?>/login" class="text-green-700 font-semibold hover:underline">Masuk</a>
                atau
                <a href="<?= BASE_URL ?>/register" class="text-green-700 font-semibold hover:underline">daftar akun baru</a>.
            </div>
            <?php else: ?>
            <form method="POST" action="<?= BASE_URL ?>/komentar/kirim" class="space-y-3">
                <input type="hidden" name="animal_id" value="<?= (int) $animal['id'] ?>">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Berkomentar sebagai</label>
                    <div class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700">
                        <?= htmlspecialchars($_SESSION['user_name'], ENT_QUOTES) ?>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Komentar</label>
                    <textarea name="isi" required maxlength="1000" rows="3"
                              placeholder="Tulis komentar kamu tentang hewan ini..."
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 resize-none"></textarea>
                </div>
                <button type="submit"
                        class="bg-green-700 hover:bg-green-800 text-white text-sm font-semibold px-5 py-2 rounded-lg transition">
                    Kirim Komentar
                </button>
                <p class="text-xs text-gray-400">Komentar akan tampil setelah disetujui admin.</p>
            </form>
            <?php endif; ?>
        </div>

        <!-- Daftar komentar -->
        <?php if (empty($comments)): ?>
        <p class="text-gray-400 text-sm">Belum ada komentar. Jadilah yang pertama!</p>
        <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($comments as $c): ?>
            <div class="bg-white rounded-2xl border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-semibold text-sm text-gray-800"><?= htmlspecialchars($c['nama'], ENT_QUOTES) ?></span>
                    <span class="text-xs text-gray-400"><?= date('d M Y, H:i', strtotime($c['created_at'])) ?></span>
                </div>
                <p class="text-gray-700 text-sm leading-relaxed"><?= nl2br(htmlspecialchars($c['isi'], ENT_QUOTES)) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="mt-8">
        <a href="<?= BASE_URL ?>/hewan"
           class="inline-flex items-center gap-2 text-green-700 hover:text-green-900 font-medium text-sm">
            &larr; Kembali ke Ensiklopedia
        </a>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
