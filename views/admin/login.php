<?php
// Pastikan session sudah jalan (dipanggil dari index.php via router, session_start() sudah ada)
// Jika sudah login, langsung redirect ke dashboard
if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: ' . BASE_URL . '/admin');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Admin — WildPedia</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
<style>
  body { font-family: 'Nunito', sans-serif; }
</style>
</head>
<body class="bg-stone-100 min-h-screen flex items-center justify-center p-4">

<div class="bg-white rounded-2xl shadow-lg p-8 w-full max-w-sm">

    <!-- Logo -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-green-700 rounded-2xl mb-3">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-800">WildPedia</h1>
        <p class="text-gray-500 text-sm mt-1">Panel Admin</p>
    </div>

    <!-- Pesan error -->
    <?php if (!empty($_SESSION['login_error'])): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-5 flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        <?= htmlspecialchars($_SESSION['login_error'], ENT_QUOTES) ?>
        <?php unset($_SESSION['login_error']); ?>
    </div>
    <?php endif; ?>

    <!-- Form login -->
    <form method="POST" action="<?= BASE_URL ?>/admin/login" class="space-y-4">

        <div>
            <label for="username" class="block text-sm font-semibold text-gray-700 mb-1.5">
                Username
            </label>
            <input
                type="text"
                id="username"
                name="username"
                required
                autofocus
                autocomplete="username"
                placeholder="Masukkan username..."
                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm
                       focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent
                       transition placeholder-gray-400"
            >
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">
                Password
            </label>
            <input
                type="password"
                id="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Masukkan password..."
                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm
                       focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent
                       transition placeholder-gray-400"
            >
        </div>

        <button
            type="submit"
            class="w-full bg-green-700 hover:bg-green-800 active:bg-green-900
                   text-white font-semibold py-2.5 rounded-xl text-sm
                   transition-colors duration-150 mt-2"
        >
            Masuk ke Panel Admin
        </button>

    </form>

    <p class="text-center text-xs text-gray-400 mt-6">
        <a href="<?= BASE_URL ?>/hewan" class="hover:text-green-700 transition-colors">
            &larr; Kembali ke WildPedia
        </a>
    </p>

</div>

</body>
</html>