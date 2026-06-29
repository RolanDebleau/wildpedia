<?php
use App\Models\Animal;

$pageTitle   = 'Hasil Identifikasi';
$currentPage = 'identify';

require __DIR__ . '/layouts/header.php';
?>

<div class="max-w-3xl mx-auto px-4 py-10">

    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Hasil Identifikasi</h1>
    </div>

    <div class="space-y-5">

        <!-- Foto yang diupload -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <img src="<?= htmlspecialchars(BASE_URL . '/uploads/' . $filename, ENT_QUOTES) ?>"
                 alt="Foto yang diupload"
                 class="w-full max-h-72 object-contain bg-gray-50">
        </div>

        <!-- Cocok dengan database -->
        <?php if ($matched): ?>
        <div class="bg-green-50 border-2 border-green-400 rounded-2xl p-5">
            <div class="flex items-center gap-2 mb-3">
                <h2 class="font-bold text-green-800 text-lg">Ditemukan di Database WildPedia!</h2>
            </div>
            <div class="flex gap-4 items-start">
                <?php if (!empty($matched['image_url'])): ?>
                <img src="<?= htmlspecialchars($matched['image_url'], ENT_QUOTES) ?>"
                     alt="<?= htmlspecialchars($matched['name'], ENT_QUOTES) ?>"
                     class="w-24 h-24 object-cover rounded-xl flex-shrink-0">
                <?php endif; ?>
                <div class="flex-1">
                    <h3 class="font-bold text-lg text-gray-900">
                        <?= htmlspecialchars($matched['name'], ENT_QUOTES) ?>
                    </h3>
                    <p class="text-gray-500 italic text-sm">
                        <?= htmlspecialchars($matched['latin_name'], ENT_QUOTES) ?>
                    </p>
                    <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-bold status-<?= strtolower($matched['status']) ?>">
                        <?= htmlspecialchars(Animal::statusLabel($matched['status']), ENT_QUOTES) ?>
                    </span>
                    <p class="text-gray-600 text-sm mt-2">
                        <?= htmlspecialchars(mb_substr($matched['description'] ?? '', 0, 120) . '...', ENT_QUOTES) ?>
                    </p>
                </div>
            </div>
            <a href="<?= BASE_URL ?>/hewan/<?= htmlspecialchars($matched['slug'], ENT_QUOTES) ?>"
               class="mt-4 inline-block bg-green-700 hover:bg-green-600 text-white px-5 py-2 rounded-xl text-sm font-semibold transition">
                Lihat Info Lengkap &rarr;
            </a>
        </div>
        <?php endif; ?>

        <!-- Hasil dari Hugging Face -->
        <?php if (!empty($topResults)): ?>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <h2 class="font-bold text-gray-800 mb-1">Hasil Identifikasi AI</h2>
            <p class="text-xs text-gray-400 mb-4">Model: google/vit-base-patch16-224 (Hugging Face Inference API)</p>
            <div class="space-y-3">
                <?php foreach ($topResults as $i => $result):
                    $rawLabel = $result['label'] ?? 'Tidak diketahui';
                    // Label model ImageNet sering berformat "tiger, Panthera tigris"
                    $labelParts = explode(',', $rawLabel);
                    $name       = trim($labelParts[0]);
                    $altName    = isset($labelParts[1]) ? trim($labelParts[1]) : null;
                    $score      = round(($result['score'] ?? 0) * 100, 1);
                ?>
                <div class="flex items-center gap-3 p-3 rounded-xl <?= $i === 0 ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-100' ?>">
                    <span class="text-lg font-bold text-gray-400 w-6"><?= $i + 1 ?></span>

                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm <?= $i === 0 ? 'text-green-800' : 'text-gray-700' ?> truncate capitalize">
                            <?= htmlspecialchars($name, ENT_QUOTES) ?>
                        </p>
                        <?php if ($altName): ?>
                        <p class="text-xs text-gray-400 italic truncate">
                            <?= htmlspecialchars($altName, ENT_QUOTES) ?>
                        </p>
                        <?php endif; ?>
                    </div>

                    <div class="text-right flex-shrink-0">
                        <p class="text-sm font-bold <?= $i === 0 ? 'text-green-700' : 'text-gray-600' ?>">
                            <?= $score ?>%
                        </p>
                        <div class="w-20 bg-gray-200 rounded-full h-1.5 mt-1">
                            <div class="bg-green-500 h-1.5 rounded-full" style="width:<?= $score ?>%"></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <p class="text-xs text-gray-400 mt-4">
                Model ini dilatih dengan dataset umum (ImageNet) dan belum dikhususkan untuk satwa Indonesia,
                sehingga label hasil kadang berupa nama umum berbahasa Inggris.
            </p>
        </div>
        <?php endif; ?>

        <!-- Tombol aksi -->
        <div class="text-center">
            <a href="<?= BASE_URL ?>/identifikasi"
               class="inline-block bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl text-sm font-medium transition">
                &larr; Coba Foto Lain
            </a>
            <a href="<?= BASE_URL ?>/hewan"
               class="inline-block ml-3 bg-green-700 hover:bg-green-600 text-white px-6 py-2.5 rounded-xl text-sm font-medium transition">
                Lihat Semua Hewan
            </a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/layouts/footer.php'; ?>
