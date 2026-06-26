<?php

namespace App\Controllers;

use App\Models\Admin;
use App\Models\Animal;
use App\Models\Comment;
use App\Database\DB;

class AdminController
{
    // ---- Auth ----

    private function requireLogin(): void
    {
        if (empty($_SESSION['admin_logged_in'])) {
            header('Location: ' . BASE_URL . '/admin/login');
            exit;
        }
    }

    public function loginForm(): void
    {
        if (!empty($_SESSION['admin_logged_in'])) {
            header('Location: ' . BASE_URL . '/admin');
            exit;
        }
        $pageTitle = 'Login Admin';
        require VIEWS_PATH . '/admin/login.php';
    }

    public function loginPost(): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $admin = Admin::findByUsername($username);

        if ($admin && Admin::verifyPassword($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username']  = $admin['username'];
            header('Location: ' . BASE_URL . '/admin');
            exit;
        }

        $_SESSION['login_error'] = 'Username atau password salah.';
        header('Location: ' . BASE_URL . '/admin/login');
        exit;
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: ' . BASE_URL . '/admin/login');
        exit;
    }

    // ---- Dashboard ----

    public function dashboard(): void
    {
        $this->requireLogin();
        $pageTitle      = 'Dashboard Admin';
        $totalAnimals   = Animal::count();
        $totalComments  = Comment::countPending();
        $recentComments = Comment::all();
        require VIEWS_PATH . '/admin/dashboard.php';
    }

    // ---- Hewan: List ----

    public function animalList(): void
    {
        $this->requireLogin();
        $pageTitle = 'Kelola Hewan';
        $db   = DB::connect();
        $animals = $db->query("SELECT * FROM animals ORDER BY name ASC")->fetchAll();
        require VIEWS_PATH . '/admin/animals/index.php';
    }

    // ---- Hewan: Tambah ----

    public function animalCreate(): void
    {
        $this->requireLogin();
        $pageTitle = 'Tambah Hewan';
        $threats   = DB::connect()->query("SELECT * FROM threats ORDER BY name")->fetchAll();
        require VIEWS_PATH . '/admin/animals/form.php';
    }

    public function animalStore(): void
    {
        $this->requireLogin();
        $data = $this->parseAnimalPost();

        // Upload foto
        $imageUrl = $this->handleUpload();
        if ($imageUrl !== null) $data['image_url'] = $imageUrl;

        $db = DB::connect();
        $db->prepare("
            INSERT INTO animals
                (name, latin_name, slug, type, status, habitat, food,
                 population, size, description, fun_fact, image_url,
                 conservation_action, is_endemic)
            VALUES
                (:name, :latin_name, :slug, :type, :status, :habitat, :food,
                 :population, :size, :description, :fun_fact, :image_url,
                 :conservation_action, :is_endemic)
        ")->execute($data);

        $animalId = (int) $db->lastInsertId();
        $this->syncThreats($animalId, $_POST['threats'] ?? []);

        $_SESSION['flash'] = 'Hewan berhasil ditambahkan.';
        header('Location: ' . BASE_URL . '/admin/hewan');
        exit;
    }

    // ---- Hewan: Edit ----

    public function animalEdit(int $id): void
    {
        $this->requireLogin();
        $db     = DB::connect();
        $animal = $db->prepare("SELECT * FROM animals WHERE id = :id");
        $animal->execute([':id' => $id]);
        $animal = $animal->fetch();

        if (!$animal) { http_response_code(404); echo 'Tidak ditemukan'; return; }

        $threats         = $db->query("SELECT * FROM threats ORDER BY name")->fetchAll();
        $animalThreatIds = array_column(
            $db->prepare("SELECT threat_id FROM animal_threat WHERE animal_id = :id")
               ->execute([':id' => $id]) ? [] : [],
            'threat_id'
        );
        $stmt = $db->prepare("SELECT threat_id FROM animal_threat WHERE animal_id = :id");
        $stmt->execute([':id' => $id]);
        $animalThreatIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        $pageTitle = 'Edit Hewan: ' . $animal['name'];
        require VIEWS_PATH . '/admin/animals/form.php';
    }

    public function animalUpdate(int $id): void
    {
        $this->requireLogin();
        $data       = $this->parseAnimalPost();
        $data[':id'] = $id;

        // Upload foto baru jika ada
        $imageUrl = $this->handleUpload();
        if ($imageUrl !== null) {
            $data['image_url'] = $imageUrl;
        } else {
            // Pertahankan foto lama
            $db   = DB::connect();
            $stmt = $db->prepare("SELECT image_url FROM animals WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $data['image_url'] = $stmt->fetchColumn();
        }

        $db = DB::connect();
        $db->prepare("
            UPDATE animals SET
                name=:name, latin_name=:latin_name, slug=:slug, type=:type,
                status=:status, habitat=:habitat, food=:food,
                population=:population, size=:size, description=:description,
                fun_fact=:fun_fact, image_url=:image_url,
                conservation_action=:conservation_action, is_endemic=:is_endemic
            WHERE id=:id
        ")->execute($data);

        $this->syncThreats($id, $_POST['threats'] ?? []);

        $_SESSION['flash'] = 'Data hewan berhasil diperbarui.';
        header('Location: ' . BASE_URL . '/admin/hewan');
        exit;
    }

    // ---- Hewan: Hapus ----

    public function animalDelete(int $id): void
    {
        $this->requireLogin();
        $db = DB::connect();
        $db->prepare("DELETE FROM animals WHERE id = :id")->execute([':id' => $id]);
        $_SESSION['flash'] = 'Hewan berhasil dihapus.';
        header('Location: ' . BASE_URL . '/admin/hewan');
        exit;
    }

    // ---- Komentar ----

    public function commentList(): void
    {
        $this->requireLogin();
        $pageTitle = 'Kelola Komentar';
        $comments  = Comment::all();
        require VIEWS_PATH . '/admin/comments.php';
    }

    public function commentApprove(int $id): void
    {
        $this->requireLogin();
        Comment::approve($id);
        $_SESSION['flash'] = 'Komentar disetujui.';
        header('Location: ' . BASE_URL . '/admin/komentar');
        exit;
    }

    public function commentDelete(int $id): void
    {
        $this->requireLogin();
        Comment::delete($id);
        $_SESSION['flash'] = 'Komentar dihapus.';
        header('Location: ' . BASE_URL . '/admin/komentar');
        exit;
    }

    // ---- Helpers ----

    private function parseAnimalPost(): array
    {
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        if ($slug === '') {
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
        }

        return [
            ':name'                => $name,
            ':latin_name'          => trim($_POST['latin_name'] ?? ''),
            ':slug'                => $slug,
            ':type'                => trim($_POST['type'] ?? ''),
            ':status'              => trim($_POST['status'] ?? 'LC'),
            ':habitat'             => trim($_POST['habitat'] ?? ''),
            ':food'                => trim($_POST['food'] ?? ''),
            ':population'          => trim($_POST['population'] ?? ''),
            ':size'                => trim($_POST['size'] ?? ''),
            ':description'         => trim($_POST['description'] ?? ''),
            ':fun_fact'            => trim($_POST['fun_fact'] ?? ''),
            ':image_url'           => trim($_POST['image_url'] ?? ''),
            ':conservation_action' => trim($_POST['conservation_action'] ?? ''),
            ':is_endemic'          => isset($_POST['is_endemic']) ? 1 : 0,
        ];
    }

    private function handleUpload(): ?string
    {
        if (empty($_FILES['image']['tmp_name'])) return null;

        $file    = $_FILES['image'];
        $allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        $finfo   = finfo_open(FILEINFO_MIME_TYPE);
        $mime    = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowed)) return null;
        if ($file['size'] > 5 * 1024 * 1024) return null;

        $ext      = match ($mime) {
            'image/jpeg', 'image/jpg' => 'jpg',
            'image/png'               => 'png',
            'image/webp'              => 'webp',
            default                   => 'jpg',
        };

        $filename = uniqid('animal_', true) . '.' . $ext;
        $destDir  = ROOT_PATH . '/public/assets/animals/';
        if (!is_dir($destDir)) mkdir($destDir, 0755, true);

        $dest = $destDir . $filename;
        if (!move_uploaded_file($file['tmp_name'], $dest)) return null;

        return '/wildpedia/public/assets/animals/' . $filename;
    }

    private function syncThreats(int $animalId, array $threatIds): void
    {
        $db = DB::connect();
        $db->prepare("DELETE FROM animal_threat WHERE animal_id = :id")->execute([':id' => $animalId]);

        if (empty($threatIds)) return;

        $stmt = $db->prepare("INSERT IGNORE INTO animal_threat (animal_id, threat_id) VALUES (:a, :t)");
        foreach ($threatIds as $tid) {
            $stmt->execute([':a' => $animalId, ':t' => (int) $tid]);
        }
    }
}
