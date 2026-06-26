<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'WildPedia Indonesia', ENT_QUOTES) ?> &mdash; Ensiklopedia Hewan Nusantara</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        forest: {
                            50: '#f0fdf4', 100: '#dcfce7', 200: '#bbf7d0',
                            400: '#4ade80', 600: '#16a34a', 700: '#15803d',
                            800: '#166534', 900: '#14532d', 950: '#0a2e1a',
                        }
                    },
                    fontFamily: {
                        sans: ['Nunito', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Nunito', sans-serif; }
        .status-cr { background:#fee2e2; color:#b91c1c; }
        .status-en { background:#ffedd5; color:#c2410c; }
        .status-vu { background:#fef9c3; color:#a16207; }
        .status-nt { background:#dbeafe; color:#1d4ed8; }
        .status-lc { background:#dcfce7; color:#15803d; }
        .status-ew, .status-ex { background:#f3f4f6; color:#374151; }
        .card-hover { transition: transform .2s ease, box-shadow .2s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 12px 30px rgba(0,0,0,0.1); }
        .nav-link { transition: color .15s; }
        .nav-link:hover { color: #16a34a; }
        .nav-link.active { color: #15803d; font-weight: 600; border-bottom: 2px solid #16a34a; }
    </style>
</head>
<body class="bg-stone-50 text-gray-800 min-h-screen flex flex-col">

<!-- NAVBAR -->
<nav class="bg-forest-950 text-white shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">

        <a href="<?= BASE_URL ?>/hewan" class="flex items-center gap-2">
            <div>
                <span class="text-lg font-bold tracking-wide">WildPedia</span>
                <span class="text-forest-400 text-sm ml-1">Indonesia</span>
            </div>
        </a>

        <div class="hidden md:flex items-center gap-6 text-sm">
            <a href="<?= BASE_URL ?>/hewan"
               class="nav-link text-gray-300 hover:text-white pb-1 <?= ($currentPage ?? '') === 'animals' ? 'active text-white' : '' ?>">
                Ensiklopedia
            </a>
            <a href="<?= BASE_URL ?>/identifikasi"
               class="nav-link text-gray-300 hover:text-white pb-1 <?= ($currentPage ?? '') === 'identify' ? 'active text-white' : '' ?>">
                Identifikasi Foto
            </a>

            <span class="w-px h-4 bg-forest-800"></span>

            <?php if (!empty($_SESSION['user_logged_in'])): ?>
                <span class="text-gray-300">
                    Hai, <span class="text-white font-semibold"><?= htmlspecialchars($_SESSION['user_name'], ENT_QUOTES) ?></span>
                </span>
                <a href="<?= BASE_URL ?>/logout" class="nav-link text-gray-300 hover:text-white pb-1">Keluar</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/login" class="nav-link text-gray-300 hover:text-white pb-1">Masuk</a>
                <a href="<?= BASE_URL ?>/register"
                   class="bg-forest-600 hover:bg-forest-700 text-white text-sm font-semibold px-3 py-1.5 rounded-lg transition">
                    Daftar
                </a>
            <?php endif; ?>

            <a href="<?= BASE_URL ?>/admin/login"
               class="nav-link text-gray-500 hover:text-white pb-1 text-xs uppercase tracking-wide">
                Login Admin
            </a>
        </div>

        <button x-data x-on:click="$dispatch('toggle-mobile-menu')" class="md:hidden text-white p-1">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    <div x-data="{ open: false }" x-on:toggle-mobile-menu.window="open = !open"
         x-show="open" class="md:hidden bg-forest-900 px-4 pb-3">
        <a href="<?= BASE_URL ?>/hewan" class="block py-2 text-gray-300 hover:text-white">Ensiklopedia</a>
        <a href="<?= BASE_URL ?>/identifikasi" class="block py-2 text-gray-300 hover:text-white">Identifikasi Foto</a>
        <div class="border-t border-forest-800 my-2"></div>
        <?php if (!empty($_SESSION['user_logged_in'])): ?>
            <p class="py-2 text-gray-400 text-sm">Hai, <?= htmlspecialchars($_SESSION['user_name'], ENT_QUOTES) ?></p>
            <a href="<?= BASE_URL ?>/logout" class="block py-2 text-gray-300 hover:text-white">Keluar</a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/login" class="block py-2 text-gray-300 hover:text-white">Masuk</a>
            <a href="<?= BASE_URL ?>/register" class="block py-2 text-gray-300 hover:text-white">Daftar</a>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/admin/login" class="block py-2 text-gray-500 hover:text-white text-sm">Login Admin</a>
    </div>
</nav>

<!-- MAIN CONTENT -->
<main class="flex-1">
