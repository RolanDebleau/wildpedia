# 🦎 WildPedia Indonesia

**Ensiklopedia digital hewan-hewan nusantara yang dilindungi** — dibangun dengan PHP Native murni tanpa framework, dilengkapi fitur identifikasi hewan via foto menggunakan AI (Hugging Face).

---

## 📋 Daftar Isi

- [Fitur](#fitur)
- [Teknologi](#teknologi)
- [Struktur Proyek](#struktur-proyek)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
- [Daftar Route](#daftar-route)
- [Akun Default](#akun-default)
- [Screenshot](#screenshot)

---

## ✨ Fitur

### Publik
- 📚 **Ensiklopedia Hewan** — daftar hewan dilindungi Indonesia dengan filter berdasarkan status IUCN (CR, EN, VU, dll), jenis hewan, status endemik, dan pencarian nama
- 🔍 **Detail Hewan** — informasi lengkap: deskripsi, nama latin, habitat, makanan, populasi, ukuran, fakta unik, ancaman, dan aksi konservasi
- 🤖 **Identifikasi Foto AI** — upload foto hewan, sistem AI (Hugging Face Vision) akan mengenali jenisnya dan mencocokkan dengan database hewan Indonesia
- 💬 **Komentar** — user yang sudah login bisa meninggalkan komentar di halaman detail hewan
- 👤 **Register & Login User** — sistem autentikasi untuk pengunjung

### Admin
- 📊 **Dashboard Admin** — statistik jumlah hewan, komentar, dan log identifikasi
- 🐾 **Kelola Data Hewan** — tambah, edit, hapus data hewan beserta foto dan ancaman
- 💬 **Moderasi Komentar** — setujui atau hapus komentar dari pengunjung
- 🔐 **Login Admin** — panel terpisah dari akun user biasa

### Keamanan
- ✅ CSRF protection pada semua form POST
- ✅ Escape HTML pada semua output (anti-XSS)
- ✅ Validasi MIME type file upload (tidak hanya ekstensi)
- ✅ Password di-hash dengan bcrypt

---

## 🛠 Teknologi

| Komponen | Teknologi |
|---|---|
| Backend | PHP 8+ Native (tanpa framework) |
| Database | MySQL / MariaDB |
| Web Server | Apache (XAMPP / Laragon) |
| AI Identifikasi | Hugging Face Inference API |
| Model AI | `google/vit-base-patch16-224` |
| Frontend | HTML, CSS, Bootstrap |
| Autoload | PSR-4 Manual (tanpa Composer) |

---

## 📁 Struktur Proyek

```
wildpedia/
├── config.php                  # Konfigurasi database, URL, & HF token
├── wildpedia_complete.sql       # Skema + data lengkap (gunakan ini)
├── public/
│   ├── index.php               # Entry point (front controller)
│   ├── .htaccess               # URL rewriting Apache
│   └── assets/
│       └── animals/            # Foto-foto hewan
├── src/
│   ├── router.php              # Routing manual
│   ├── Database/
│   │   └── DB.php              # Koneksi PDO (singleton)
│   ├── Models/
│   │   ├── Animal.php          # Model hewan
│   │   ├── Comment.php         # Model komentar
│   │   ├── User.php            # Model user
│   │   ├── Admin.php           # Model admin
│   │   └── IdentifyLog.php     # Model log identifikasi AI
│   ├── Controllers/
│   │   ├── AnimalController.php
│   │   ├── IdentifyController.php
│   │   ├── CommentController.php
│   │   ├── AuthController.php
│   │   └── AdminController.php
│   └── Helpers/
│       └── Helper.php          # Fungsi utilitas (CSRF, redirect, dll)
├── views/
│   ├── layouts/
│   │   ├── header.php
│   │   └── footer.php
│   ├── animals/
│   │   ├── index.php           # Daftar hewan + filter
│   │   └── show.php            # Detail hewan
│   ├── auth/
│   │   ├── login.php
│   │   └── register.php
│   ├── admin/
│   │   ├── dashboard.php
│   │   ├── animals/
│   │   │   ├── index.php
│   │   │   └── form.php
│   │   └── comments.php
│   ├── identify.php            # Form upload foto
│   ├── identify_result.php     # Hasil identifikasi AI
│   └── 404.php
└── uploads/
    └── identify/               # Foto yang diupload user (auto-dibuat)
```

---

## 🚀 Instalasi

### Prasyarat
- XAMPP / Laragon (PHP 8+, MySQL, Apache)
- `mod_rewrite` Apache aktif

### 1. Clone / Ekstrak Project

```bash
# Taruh project di folder web server
# XAMPP  : C:/xampp/htdocs/wildpedia
# Laragon: C:/laragon/www/wildpedia
```

### 2. Setup Database

Buka **phpMyAdmin**, pilih tab **SQL**, lalu paste dan jalankan isi file:

```
wildpedia_complete.sql
```

> File ini sudah berisi skema lengkap + data 27 hewan + data admin default. Cukup jalankan **sekali**.

### 3. Konfigurasi

Buka `config.php` dan sesuaikan:

```php
return [
    'database' => [
        'host' => 'localhost',
        'port' => '3306',
        'name' => 'wildpedia',
        'user' => 'root',
        'pass' => 'password_database_kamu',
    ],
    'app' => [
        'name' => 'WildPedia Indonesia',
        'url'  => 'http://localhost/wildpedia/public',
    ],
    'huggingface' => [
        'token' => 'hf_token_kamu_disini',   // dari huggingface.co/settings/tokens
        'model' => 'google/vit-base-patch16-224',
    ],
];
```

> ⚠️ **Jangan commit `config.php` ke Git!** Pastikan sudah ada di `.gitignore`.

### 4. Aktifkan mod_rewrite

Pastikan di `httpd.conf` Apache:
```
AllowOverride All
```

### 5. Akses Aplikasi

Buka browser:
```
http://localhost/wildpedia/public
```

---

## 🔑 Akun Default

| Role | Username | Password |
|------|----------|----------|
| Admin | `admin` | `admin123` |

> Segera ganti password admin setelah login pertama melalui panel admin.

---

## 🗺 Daftar Route

### Publik

| Method | URL | Keterangan |
|--------|-----|------------|
| GET | `/hewan` | Daftar hewan + filter + pagination |
| GET | `/hewan/{slug}` | Detail hewan |
| GET | `/identifikasi` | Form upload foto untuk identifikasi AI |
| POST | `/identifikasi` | Proses identifikasi foto |
| POST | `/komentar/kirim` | Kirim komentar (perlu login) |
| GET | `/register` | Form registrasi user |
| POST | `/register` | Proses registrasi |
| GET | `/login` | Form login user |
| POST | `/login` | Proses login |
| GET | `/logout` | Logout user |

### Admin

| Method | URL | Keterangan |
|--------|-----|------------|
| GET | `/admin` | Dashboard admin |
| GET | `/admin/login` | Form login admin |
| POST | `/admin/login` | Proses login admin |
| GET | `/admin/logout` | Logout admin |
| GET | `/admin/hewan` | Daftar hewan |
| GET | `/admin/hewan/tambah` | Form tambah hewan |
| POST | `/admin/hewan/tambah` | Simpan hewan baru |
| GET | `/admin/hewan/{id}/edit` | Form edit hewan |
| POST | `/admin/hewan/{id}/update` | Update data hewan |
| POST | `/admin/hewan/{id}/hapus` | Hapus hewan |
| GET | `/admin/komentar` | Daftar komentar |
| POST | `/admin/komentar/{id}/setujui` | Setujui komentar |
| POST | `/admin/komentar/{id}/hapus` | Hapus komentar |

---

## 🐾 Data Hewan

Project ini sudah dilengkapi **27 hewan Indonesia** dari berbagai status konservasi IUCN:

| Status | Keterangan | Jumlah |
|--------|-----------|--------|
| 🔴 CR | Critically Endangered (Kritis) | 6 |
| 🟠 EN | Endangered (Terancam) | 12 |
| 🟡 VU | Vulnerable (Rentan) | 6 |
| 🟢 LC | Least Concern (Tidak Terancam) | 3 |

Contoh hewan: Harimau Sumatera, Orangutan, Badak Jawa, Komodo, Gajah Sumatera, Pesut Mahakam, Elang Jawa, dan masih banyak lagi.