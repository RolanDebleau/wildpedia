<?php

namespace App\Controllers;

use App\Helpers\Helper;
use App\Models\Animal;
use App\Models\IdentifyLog;

class IdentifyController
{
    public function index(): void
    {
        $errors    = Helper::getFlash('errors') ?? [];
        $error     = Helper::getFlash('error') ?? null;
        $imagePath = Helper::getFlash('imagePath') ?? null;

        require VIEWS_PATH . '/identify.php';
    }

    public function process(): void
    {
        // Verifikasi CSRF
        if (!Helper::verifyCsrf()) {
            http_response_code(403);
            echo 'Permintaan tidak valid.';
            return;
        }

        // Validasi file
        $errors = $this->validateUpload();

        if ($errors) {
            Helper::flash('errors', $errors);
            Helper::redirect('/identifikasi');
        }

        $file     = $_FILES['photo'];
        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = 'identify/' . uniqid('img_', true) . '.' . $ext;
        $destDir  = UPLOAD_PATH . '/identify';
        $destPath = UPLOAD_PATH . '/' . $filename;

        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            Helper::flash('error', 'Gagal menyimpan file. Coba lagi.');
            Helper::redirect('/identifikasi');
        }

        try {
            $predictions = $this->callHuggingFaceApi($destPath);

            // Cocokkan dengan database
            $matched = null;

            foreach ($predictions as $pred) {
                $rawLabel = $pred['label'] ?? '';

                // Label ImageNet umumnya "nama_inggris, nama latin (opsional)"
                // contoh: "tiger, Panthera tigris" atau "Tiger Cat"
                $labelParts = array_map('trim', explode(',', $rawLabel));
                $englishName = $labelParts[0] ?? '';
                $latinHint   = $labelParts[1] ?? '';

                $matched = Animal::findBySpeciesHint($englishName, $latinHint);

                if ($matched) break;
            }

            // Simpan log
            IdentifyLog::create([
                'image_path'        => $filename,
                'api_result'        => json_encode($predictions),
                'identified_animal' => $predictions[0]['label'] ?? null,
                'confidence'        => isset($predictions[0]['score'])
                                       ? round($predictions[0]['score'] * 100, 1)
                                       : null,
                'user_ip'           => Helper::ip(),
            ]);

            // Disamakan dengan nama variabel yang dipakai di identify_result.php
            $topResults = $predictions;

            require VIEWS_PATH . '/identify_result.php';

        } catch (\Exception $e) {
            $error     = 'Identifikasi gagal: ' . $e->getMessage();
            $imagePath = BASE_URL . '/uploads/' . $filename;

            require VIEWS_PATH . '/identify.php';
        }
    }

    // ----- Private -----

    private function validateUpload(): array
    {
        $errors = [];

        if (empty($_FILES['photo']['tmp_name'])) {
            $errors[] = 'Silakan pilih foto hewan.';
            return $errors;
        }

        $file = $_FILES['photo'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Terjadi kesalahan saat upload.';
            return $errors;
        }

        // Validasi tipe MIME
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowed = ['image/jpeg', 'image/png', 'image/webp'];

        if (!in_array($mimeType, $allowed)) {
            $errors[] = 'File harus berupa gambar (JPG, PNG, atau WEBP).';
        }

        // Max 5MB
        if ($file['size'] > 5 * 1024 * 1024) {
            $errors[] = 'Ukuran file maksimal 5MB.';
        }

        return $errors;
    }

    private function callHuggingFaceApi(string $filePath): array
    {
        if (!function_exists('curl_init')) {
            throw new \RuntimeException(
                'Ekstensi PHP cURL belum aktif. Buka php.ini di Laragon, hilangkan tanda ";" ' .
                'di depan baris "extension=curl", lalu restart Apache.'
            );
        }

        $config = require ROOT_PATH . '/config.php';
        $token  = $config['huggingface']['token'] ?? '';
        $model  = $config['huggingface']['model'] ?? 'google/vit-base-patch16-224';

        if ($token === '' || str_starts_with($token, 'hf_xxxx')) {
            throw new \RuntimeException(
                'Token Hugging Face belum diisi. Buka config.php dan isi huggingface.token ' .
                'dengan token dari https://huggingface.co/settings/tokens.'
            );
        }

        $url = "https://router.huggingface.co/hf-inference/models/{$model}";

        $imageData = file_get_contents($filePath);
        $mimeType  = mime_content_type($filePath);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $imageData,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $token,
                'Content-Type: ' . $mimeType,
            ],
        ]);

        $body  = curl_exec($ch);
        $error = curl_error($ch);
        $code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error) {
            throw new \RuntimeException('cURL error: ' . $error);
        }

        if ($code === 503) {
            throw new \RuntimeException('Model sedang dimuat di server Hugging Face, coba lagi dalam beberapa saat.');
        }

        if ($code === 401) {
            throw new \RuntimeException('Token Hugging Face tidak valid atau sudah tidak berlaku.');
        }

        if ($code < 200 || $code >= 300) {
            throw new \RuntimeException("API mengembalikan status {$code}. Respons: " . substr($body, 0, 200));
        }

        $data = json_decode($body, true);

        if (!is_array($data) || isset($data['error'])) {
            throw new \RuntimeException('Respons API tidak valid: ' . ($data['error'] ?? 'format tidak dikenali'));
        }

        // Response berupa array langsung: [{label, score}, ...]
        return array_slice($data, 0, 5);
    }

    // Label model ImageNet kadang berformat "n02129165 tiger, Panthera tigris"
    // atau "tiger, Panthera tigris" -- ambil kata pertama sebelum koma agar
    // lebih mudah dicocokkan dengan nama umum/latin di database lokal.
    private function cleanImagenetLabel(string $label): string
    {
        $label = preg_replace('/^n\d+\s+/', '', $label); // buang kode taxon ImageNet jika ada
        $parts = explode(',', $label);
        return trim($parts[0]);
    }
}
