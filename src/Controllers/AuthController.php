<?php

namespace App\Controllers;

use App\Models\User;
use App\Helpers\Helper;

class AuthController
{
    // ---- Register ----

    public function registerForm(): void
    {
        if (!empty($_SESSION['user_logged_in'])) {
            header('Location: ' . BASE_URL . '/hewan');
            exit;
        }
        $pageTitle = 'Daftar Akun';
        require VIEWS_PATH . '/auth/register.php';
    }

    public function registerPost(): void
    {
        $name     = trim($_POST['name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirm  = trim($_POST['password_confirm'] ?? '');

        $errors = [];

        if ($name === '' || $username === '' || $email === '' || $password === '') {
            $errors[] = 'Semua kolom wajib diisi.';
        }
        if ($username !== '' && !preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username)) {
            $errors[] = 'Username hanya boleh huruf, angka, underscore (min. 3 karakter).';
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid.';
        }
        if (mb_strlen($password) < 6) {
            $errors[] = 'Password minimal 6 karakter.';
        }
        if ($password !== $confirm) {
            $errors[] = 'Konfirmasi password tidak sama.';
        }
        if ($username !== '' && User::findByUsername($username)) {
            $errors[] = 'Username sudah dipakai, pilih yang lain.';
        }
        if ($email !== '' && User::findByEmail($email)) {
            $errors[] = 'Email sudah terdaftar, silakan login.';
        }

        if (!empty($errors)) {
            $_SESSION['register_errors'] = $errors;
            $_SESSION['register_old']    = ['name' => $name, 'username' => $username, 'email' => $email];
            header('Location: ' . BASE_URL . '/register');
            exit;
        }

        $userId = User::create($name, $username, $email, $password);

        // Langsung login setelah berhasil daftar
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id']        = $userId;
        $_SESSION['user_name']      = $name;
        $_SESSION['user_username']  = $username;

        $_SESSION['flash'] = 'Pendaftaran berhasil! Selamat datang, ' . $name . '.';
        header('Location: ' . BASE_URL . '/hewan');
        exit;
    }

    // ---- Login ----

    public function loginForm(): void
    {
        if (!empty($_SESSION['user_logged_in'])) {
            header('Location: ' . BASE_URL . '/hewan');
            exit;
        }
        $pageTitle = 'Masuk';
        require VIEWS_PATH . '/auth/login.php';
    }

    public function loginPost(): void
    {
        $identifier = trim($_POST['identifier'] ?? ''); // username atau email
        $password   = trim($_POST['password'] ?? '');

        $user = $identifier !== '' ? User::findByUsernameOrEmail($identifier) : null;

        if ($user && User::verifyPassword($password, $user['password'])) {
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id']        = (int) $user['id'];
            $_SESSION['user_name']      = $user['name'];
            $_SESSION['user_username']  = $user['username'];

            // Jika user diarahkan ke login dari halaman tertentu (misal mau komentar),
            // balikkan lagi ke halaman itu.
            $redirectTo = $_SESSION['redirect_after_login'] ?? (BASE_URL . '/hewan');
            unset($_SESSION['redirect_after_login']);

            header('Location: ' . $redirectTo);
            exit;
        }

        $_SESSION['login_error'] = 'Username/email atau password salah.';
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    public function logout(): void
    {
        unset($_SESSION['user_logged_in'], $_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_username']);
        header('Location: ' . BASE_URL . '/hewan');
        exit;
    }
}
