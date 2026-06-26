<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'Admin', ENT_QUOTES) ?> — WildPedia Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>body { font-family: 'Nunito', sans-serif; }</style>
</head>
<body class="bg-stone-100 min-h-screen flex">

<!-- Sidebar -->
<aside class="w-56 bg-gray-900 text-white flex flex-col min-h-screen fixed top-0 left-0 z-30">
    <div class="px-5 py-4 border-b border-gray-700">
        <div class="font-bold text-lg">WildPedia</div>
        <div class="text-xs text-gray-400">Panel Admin</div>
    </div>
    <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
        <a href="<?= BASE_URL ?>/admin"
           class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-700 <?= ($activeMenu ?? '') === 'dashboard' ? 'bg-gray-700 font-semibold' : 'text-gray-300' ?>">
            Dashboard
        </a>
        <a href="<?= BASE_URL ?>/admin/hewan"
           class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-700 <?= ($activeMenu ?? '') === 'hewan' ? 'bg-gray-700 font-semibold' : 'text-gray-300' ?>">
            Kelola Hewan
        </a>
        <a href="<?= BASE_URL ?>/admin/komentar"
           class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-700 <?= ($activeMenu ?? '') === 'komentar' ? 'bg-gray-700 font-semibold' : 'text-gray-300' ?>">
            Komentar
            <?php
            $pending = \App\Models\Comment::countPending();
            if ($pending > 0): ?>
            <span class="ml-auto bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full"><?= $pending ?></span>
            <?php endif; ?>
        </a>
    </nav>
    <div class="px-5 py-4 border-t border-gray-700 text-xs text-gray-400">
        <span><?= htmlspecialchars($_SESSION['admin_username'] ?? '', ENT_QUOTES) ?></span>
        <a href="<?= BASE_URL ?>/admin/logout" class="block mt-1 text-red-400 hover:text-red-300">Keluar</a>
    </div>
</aside>

<!-- Konten -->
<div class="ml-56 flex-1 p-6">

<?php if (!empty($_SESSION['flash'])): ?>
<div class="bg-green-100 border border-green-300 text-green-800 text-sm rounded-lg px-4 py-3 mb-4">
    <?= htmlspecialchars($_SESSION['flash'], ENT_QUOTES) ?>
    <?php unset($_SESSION['flash']); ?>
</div>
<?php endif; ?>
