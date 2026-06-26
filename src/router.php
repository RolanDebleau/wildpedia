<?php

use App\Controllers\AnimalController;
use App\Controllers\IdentifyController;
use App\Controllers\AdminController;
use App\Controllers\CommentController;
use App\Controllers\AuthController;

$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($basePath && strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}
$uri = '/' . ltrim($uri, '/');

// ---- Redirect root ----
if ($uri === '/' || $uri === '') {
    header('Location: ' . BASE_URL . '/hewan');
    exit;
}

// ---- Publik: Hewan ----
if ($uri === '/hewan' && $method === 'GET') {
    (new AnimalController())->index(); exit;
}
if (preg_match('#^/hewan/([a-z0-9\-]+)$#', $uri, $m) && $method === 'GET') {
    (new AnimalController())->show($m[1]); exit;
}

// ---- Publik: Identifikasi ----
if ($uri === '/identifikasi' && $method === 'GET') {
    (new IdentifyController())->index(); exit;
}
if ($uri === '/identifikasi' && $method === 'POST') {
    (new IdentifyController())->process(); exit;
}

// ---- Publik: Komentar (POST) ----
if ($uri === '/komentar/kirim' && $method === 'POST') {
    (new CommentController())->store(); exit;
}

// ---- Publik: Auth User (register / login / logout) ----
if ($uri === '/register' && $method === 'GET') {
    (new AuthController())->registerForm(); exit;
}
if ($uri === '/register' && $method === 'POST') {
    (new AuthController())->registerPost(); exit;
}
if ($uri === '/login' && $method === 'GET') {
    (new AuthController())->loginForm(); exit;
}
if ($uri === '/login' && $method === 'POST') {
    (new AuthController())->loginPost(); exit;
}
if ($uri === '/logout') {
    (new AuthController())->logout(); exit;
}

// ---- Admin: Auth ----
if ($uri === '/admin/login' && $method === 'GET') {
    (new AdminController())->loginForm(); exit;
}
if ($uri === '/admin/login' && $method === 'POST') {
    (new AdminController())->loginPost(); exit;
}
if ($uri === '/admin/logout') {
    (new AdminController())->logout(); exit;
}

// ---- Admin: Dashboard ----
if (($uri === '/admin' || $uri === '/admin/') && $method === 'GET') {
    (new AdminController())->dashboard(); exit;
}

// ---- Admin: Hewan ----
if ($uri === '/admin/hewan' && $method === 'GET') {
    (new AdminController())->animalList(); exit;
}
if ($uri === '/admin/hewan/tambah' && $method === 'GET') {
    (new AdminController())->animalCreate(); exit;
}
if ($uri === '/admin/hewan/tambah' && $method === 'POST') {
    (new AdminController())->animalStore(); exit;
}
if (preg_match('#^/admin/hewan/(\d+)/edit$#', $uri, $m) && $method === 'GET') {
    (new AdminController())->animalEdit((int) $m[1]); exit;
}
if (preg_match('#^/admin/hewan/(\d+)/update$#', $uri, $m) && $method === 'POST') {
    (new AdminController())->animalUpdate((int) $m[1]); exit;
}
if (preg_match('#^/admin/hewan/(\d+)/hapus$#', $uri, $m) && $method === 'POST') {
    (new AdminController())->animalDelete((int) $m[1]); exit;
}

// ---- Admin: Komentar ----
if ($uri === '/admin/komentar' && $method === 'GET') {
    (new AdminController())->commentList(); exit;
}
if (preg_match('#^/admin/komentar/(\d+)/setujui$#', $uri, $m) && $method === 'POST') {
    (new AdminController())->commentApprove((int) $m[1]); exit;
}
if (preg_match('#^/admin/komentar/(\d+)/hapus$#', $uri, $m) && $method === 'POST') {
    (new AdminController())->commentDelete((int) $m[1]); exit;
}

// ---- 404 ----
http_response_code(404);
require VIEWS_PATH . '/404.php';
