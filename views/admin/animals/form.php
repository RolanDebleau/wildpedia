<?php
$activeMenu = 'hewan';
$isEdit     = isset($animal);
$action     = $isEdit
    ? BASE_URL . '/admin/hewan/' . $animal['id'] . '/update'
    : BASE_URL . '/admin/hewan/tambah';

require __DIR__ . '/../layout_header.php';

function val(array|null $animal, string $key, string $default = ''): string {
    return htmlspecialchars($animal[$key] ?? $default, ENT_QUOTES);
}
?>

<div class="flex items-center gap-3 mb-6">
    <a href="<?= BASE_URL ?>/admin/hewan" class="text-gray-400 hover:text-gray-700 text-sm">&larr; Kembali</a>
    <h1 class="text-xl font-bold text-gray-800"><?= $isEdit ? 'Edit Hewan: ' . htmlspecialchars($animal['name'], ENT_QUOTES) : 'Tambah Hewan Baru' ?></h1>
</div>

<form method="POST" action="<?= $action ?>" enctype="multipart/form-data" class="space-y-6 max-w-3xl">

    <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
        <h2 class="font-semibold text-gray-700 mb-2">Informasi Dasar</h2>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">Nama Umum</label>
                <input type="text" name="name" required value="<?= val($animal ?? null, 'name') ?>"
                       class="input">
            </div>
            <div>
                <label class="label">Nama Latin</label>
                <input type="text" name="latin_name" required value="<?= val($animal ?? null, 'latin_name') ?>"
                       class="input">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">Slug (URL) <span class="text-gray-400 text-xs">— otomatis jika kosong</span></label>
                <input type="text" name="slug" value="<?= val($animal ?? null, 'slug') ?>"
                       placeholder="contoh: harimau-sumatera" class="input">
            </div>
            <div>
                <label class="label">Jenis / Taksonomi</label>
                <input type="text" name="type" required value="<?= val($animal ?? null, 'type') ?>"
                       placeholder="Mamalia, Reptil, Burung..." class="input">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">Status IUCN</label>
                <select name="status" class="input">
                    <?php foreach (['CR','EN','VU','NT','LC','EW','EX'] as $s): ?>
                    <option value="<?= $s ?>" <?= ($animal['status'] ?? 'LC') === $s ? 'selected' : '' ?>>
                        <?= $s ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-center gap-3 pt-6">
                <input type="checkbox" name="is_endemic" id="is_endemic" value="1"
                       <?= !empty($animal['is_endemic']) ? 'checked' : '' ?>
                       class="w-4 h-4 text-green-600">
                <label for="is_endemic" class="text-sm font-medium text-gray-700">Endemik Indonesia</label>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
        <h2 class="font-semibold text-gray-700 mb-2">Detail Ekologi</h2>

        <div>
            <label class="label">Habitat</label>
            <textarea name="habitat" rows="2" class="input"><?= val($animal ?? null, 'habitat') ?></textarea>
        </div>
        <div>
            <label class="label">Makanan</label>
            <textarea name="food" rows="2" class="input"><?= val($animal ?? null, 'food') ?></textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">Populasi Estimasi</label>
                <input type="text" name="population" value="<?= val($animal ?? null, 'population') ?>"
                       placeholder="~400 ekor" class="input">
            </div>
            <div>
                <label class="label">Ukuran Tubuh</label>
                <input type="text" name="size" value="<?= val($animal ?? null, 'size') ?>"
                       placeholder="Panjang 2.5 m, berat 140 kg" class="input">
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
        <h2 class="font-semibold text-gray-700 mb-2">Konten</h2>

        <div>
            <label class="label">Deskripsi</label>
            <textarea name="description" rows="4" required class="input"><?= val($animal ?? null, 'description') ?></textarea>
        </div>
        <div>
            <label class="label">Fakta Menarik</label>
            <textarea name="fun_fact" rows="3" class="input"><?= val($animal ?? null, 'fun_fact') ?></textarea>
        </div>
        <div>
            <label class="label">Upaya Konservasi</label>
            <textarea name="conservation_action" rows="2" class="input"><?= val($animal ?? null, 'conservation_action') ?></textarea>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
        <h2 class="font-semibold text-gray-700 mb-2">Foto</h2>

        <?php if ($isEdit && !empty($animal['image_url'])): ?>
        <div class="mb-2">
            <img src="<?= val($animal, 'image_url') ?>" alt="Foto saat ini"
                 class="w-32 h-24 object-cover rounded-lg border border-gray-200">
            <p class="text-xs text-gray-400 mt-1">Foto saat ini — upload baru untuk mengganti</p>
        </div>
        <?php endif; ?>

        <div>
            <label class="label">Upload Foto (JPG/PNG/WebP, maks 5MB)</label>
            <input type="file" name="image" accept="image/*" class="input">
        </div>
        <div>
            <label class="label">Atau masukkan URL foto</label>
            <input type="text" name="image_url" value="<?= val($animal ?? null, 'image_url') ?>"
                   placeholder="https://... atau /wildpedia/public/assets/animals/..." class="input">
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <h2 class="font-semibold text-gray-700 mb-3">Ancaman</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
            <?php foreach ($threats as $t): ?>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="threats[]" value="<?= $t['id'] ?>"
                       <?= in_array($t['id'], $animalThreatIds ?? []) ? 'checked' : '' ?>
                       class="w-4 h-4 text-green-600">
                <?= htmlspecialchars($t['name'], ENT_QUOTES) ?>
            </label>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="flex gap-3">
        <button type="submit"
                class="bg-green-700 hover:bg-green-800 text-white font-semibold px-6 py-2.5 rounded-lg text-sm transition">
            <?= $isEdit ? 'Simpan Perubahan' : 'Tambah Hewan' ?>
        </button>
        <a href="<?= BASE_URL ?>/admin/hewan"
           class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-6 py-2.5 rounded-lg text-sm transition">
            Batal
        </a>
    </div>
</form>

<style>
.label { display: block; font-size: 0.8rem; font-weight: 500; color: #374151; margin-bottom: 4px; }
.input { width: 100%; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.45rem 0.75rem; font-size: 0.875rem; outline: none; font-family: inherit; }
.input:focus { border-color: #16a34a; box-shadow: 0 0 0 2px rgba(22,163,74,0.2); }
</style>

<?php require __DIR__ . '/../layout_footer.php'; ?>
