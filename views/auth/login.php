<?php
$currentPage = 'login';
require __DIR__ . '/../layouts/header.php';
?>

<div class="max-w-md mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8">

        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Masuk</h1>
            <p class="text-gray-500 text-sm mt-1">Masuk untuk bisa berkomentar di WildPedia</p>
        </div>

        <?php if (!empty($_SESSION['login_error'])): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-5">
            <?= htmlspecialchars($_SESSION['login_error'], ENT_QUOTES) ?>
            <?php unset($_SESSION['login_error']); ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/login" class="space-y-4">

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Username atau Email</label>
                <input type="text" name="identifier" required autofocus
                       placeholder="Masukkan username atau email..."
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm
                              focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                <input type="password" name="password" required
                       placeholder="Masukkan password..."
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm
                              focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>

            <button type="submit"
                    class="w-full bg-green-700 hover:bg-green-800 active:bg-green-900
                           text-white font-semibold py-2.5 rounded-xl text-sm transition-colors duration-150 mt-2">
                Masuk
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            Belum punya akun?
            <a href="<?= BASE_URL ?>/register" class="text-green-700 font-semibold hover:underline">Daftar di sini</a>
        </p>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
