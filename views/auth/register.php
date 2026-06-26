<?php
$currentPage = 'register';
require __DIR__ . '/../layouts/header.php';

$errors = $_SESSION['register_errors'] ?? [];
$old    = $_SESSION['register_old'] ?? [];
unset($_SESSION['register_errors'], $_SESSION['register_old']);
?>

<div class="max-w-md mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8">

        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Daftar Akun</h1>
            <p class="text-gray-500 text-sm mt-1">Buat akun untuk bisa berkomentar di WildPedia</p>
        </div>

        <?php if (!empty($errors)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-5">
            <ul class="list-disc list-inside space-y-1">
                <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err, ENT_QUOTES) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/register" class="space-y-4">

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" required
                       value="<?= htmlspecialchars($old['name'] ?? '', ENT_QUOTES) ?>"
                       placeholder="Nama kamu..."
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm
                              focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Username</label>
                <input type="text" name="username" required
                       value="<?= htmlspecialchars($old['username'] ?? '', ENT_QUOTES) ?>"
                       placeholder="Tanpa spasi, contoh: budi_99"
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm
                              focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                <input type="email" name="email" required
                       value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES) ?>"
                       placeholder="nama@email.com"
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm
                              focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                <input type="password" name="password" required minlength="6"
                       placeholder="Minimal 6 karakter"
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm
                              focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Konfirmasi Password</label>
                <input type="password" name="password_confirm" required minlength="6"
                       placeholder="Ulangi password..."
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm
                              focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>

            <button type="submit"
                    class="w-full bg-green-700 hover:bg-green-800 active:bg-green-900
                           text-white font-semibold py-2.5 rounded-xl text-sm transition-colors duration-150 mt-2">
                Daftar Sekarang
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            Sudah punya akun?
            <a href="<?= BASE_URL ?>/login" class="text-green-700 font-semibold hover:underline">Masuk di sini</a>
        </p>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
