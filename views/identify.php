<?php
use App\Helpers\Helper;

$pageTitle   = 'Identifikasi Foto Hewan';
$currentPage = 'identify';

require __DIR__ . '/layouts/header.php';
?>

<div class="max-w-3xl mx-auto px-4 py-10">

    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Identifikasi Hewan</h1>
        <p class="text-gray-500 mt-2 text-sm">
            Upload foto hewan yang kamu jumpai, kami akan mengidentifikasinya menggunakan AI (Hugging Face)
        </p>
    </div>

    <!-- FORM UPLOAD -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6"
         x-data="uploadForm()" x-cloak>

        <form method="POST" action="<?= BASE_URL ?>/identifikasi" enctype="multipart/form-data"
              @submit="submitting = true" class="space-y-5">

            <input type="hidden" name="_token" value="<?= Helper::csrfToken() ?>">

            <!-- Drop zone -->
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center cursor-pointer
                        hover:border-green-500 hover:bg-green-50 transition-all"
                 @click="$refs.fileInput.click()"
                 @dragover.prevent="dragOver = true"
                 @dragleave="dragOver = false"
                 @drop.prevent="handleDrop($event)"
                 :class="dragOver ? 'border-green-500 bg-green-50' : ''">

                <template x-if="preview">
                    <div>
                        <img :src="preview" class="max-h-64 mx-auto rounded-xl object-contain mb-3">
                        <p class="text-sm text-green-700 font-medium" x-text="fileName"></p>
                        <p class="text-xs text-gray-400 mt-1">Klik untuk ganti foto</p>
                    </div>
                </template>

                <template x-if="!preview">
                    <div>
                        <p class="text-gray-600 font-medium">Klik atau drag &amp; drop foto hewan di sini</p>
                        <p class="text-gray-400 text-sm mt-1">Format: JPG, PNG, WEBP &bull; Maks 5MB</p>
                    </div>
                </template>

                <input type="file" name="photo" accept="image/*" x-ref="fileInput" class="hidden"
                       @change="handleFile($event)">
            </div>

            <!-- Error validasi -->
            <?php if (!empty($errors)): ?>
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <p class="text-red-700 text-sm font-medium mb-1">Terjadi kesalahan:</p>
                <?php foreach ($errors as $err): ?>
                    <p class="text-red-600 text-sm">&bull; <?= htmlspecialchars($err, ENT_QUOTES) ?></p>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Error API -->
            <?php if (!empty($error)): ?>
            <div class="bg-orange-50 border border-orange-200 rounded-xl p-4">
                <p class="text-orange-800 text-sm font-medium"><?= htmlspecialchars($error, ENT_QUOTES) ?></p>
                <p class="text-orange-600 text-xs mt-1">Pastikan koneksi internet aktif dan coba lagi.</p>
            </div>
            <?php endif; ?>

            <button type="submit"
                    :disabled="!hasFile || submitting"
                    class="w-full bg-green-700 hover:bg-green-600 text-white py-3 rounded-xl font-semibold text-sm
                           disabled:opacity-40 disabled:cursor-not-allowed transition flex items-center justify-center gap-2">
                <template x-if="submitting">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </template>
                <span x-text="submitting ? 'Mengidentifikasi...' : 'Identifikasi Hewan'"></span>
            </button>
        </form>
    </div>

    <!-- Panduan -->
    <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <?php
        $tips = [
            ['Foto Jelas',   'Pastikan hewan terlihat jelas, tidak buram atau terlalu gelap'],
            ['Fokus Hewan',  'Hewan menjadi subjek utama foto, bukan latar belakang'],
            ['Cahaya Cukup', 'Foto di siang hari atau area bercahaya untuk hasil terbaik'],
        ];
        foreach ($tips as [$title, $desc]):
        ?>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <p class="font-semibold text-sm text-gray-700"><?= htmlspecialchars($title, ENT_QUOTES) ?></p>
            <p class="text-xs text-gray-400 mt-1"><?= htmlspecialchars($desc, ENT_QUOTES) ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function uploadForm() {
    return {
        preview: null,
        fileName: '',
        hasFile: false,
        dragOver: false,
        submitting: false,

        handleFile(event) {
            const file = event.target.files[0];
            if (file) this.setPreview(file);
        },

        handleDrop(event) {
            this.dragOver = false;
            const file = event.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                this.$refs.fileInput.files = event.dataTransfer.files;
                this.setPreview(file);
            }
        },

        setPreview(file) {
            this.fileName = file.name;
            this.hasFile = true;
            const reader = new FileReader();
            reader.onload = e => { this.preview = e.target.result; };
            reader.readAsDataURL(file);
        }
    }
}
</script>

<?php require __DIR__ . '/layouts/footer.php'; ?>
