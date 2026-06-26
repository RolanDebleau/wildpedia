<?php

namespace App\Controllers;

use App\Models\Animal;
use App\Models\Comment;

class CommentController
{
    public function store(): void
    {
        $animalId = (int) ($_POST['animal_id'] ?? 0);
        $animal   = $animalId ? Animal::findById($animalId) : null;
        $slug     = $animal['slug'] ?? '';

        // Wajib login dulu sebelum bisa komentar
        if (empty($_SESSION['user_logged_in'])) {
            $_SESSION['redirect_after_login'] = BASE_URL . '/hewan/' . $slug . '#komentar';
            $_SESSION['login_error']          = 'Silakan login atau daftar dulu untuk mengirim komentar.';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $isi = trim($_POST['isi'] ?? '');

        if (!$animal || $isi === '') {
            $_SESSION['comment_error'] = 'Komentar tidak boleh kosong.';
            header('Location: ' . BASE_URL . '/hewan/' . $slug);
            exit;
        }

        if (mb_strlen($isi) > 1000) {
            $_SESSION['comment_error'] = 'Komentar terlalu panjang (maks 1000 karakter).';
            header('Location: ' . BASE_URL . '/hewan/' . $slug);
            exit;
        }

        $nama = $_SESSION['user_name'] ?? 'Pengguna';
        Comment::create($animalId, $nama, $isi, (int) $_SESSION['user_id']);

        $_SESSION['comment_success'] = 'Komentar berhasil dikirim dan menunggu persetujuan admin.';
        header('Location: ' . BASE_URL . '/hewan/' . $slug . '#komentar');
        exit;
    }
}
