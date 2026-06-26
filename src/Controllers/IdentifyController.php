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
            $topResults = $this->callInatApi($destPath, $file['name']);

            // Cocokkan dengan database
            $matched = null;

            foreach ($topResults as $result) {
                $latinName  = $result['taxon']['name']                  ?? '';
                $commonName = $result['taxon']['preferred_common_name'] ?? '';

                $matched = Animal::findByNameOrLatin($commonName, $latinName);

                if ($matched) break;
            }

            // Simpan log
            IdentifyLog::create([
                'image_path'        => $filename,
                'api_result'        => json_encode($topResults),
                'identified_animal' => $topResults[0]['taxon']['name'] ?? null,
                'confidence'        => isset($topResults[0]['combined_score'])
                                       ? round($topResults[0]['combined_score'] * 100, 1)
                                       : null,
                'user_ip'           => Helper::ip(),
            ]);

            require VIEWS_PATH . '/identify_result.php';

        } catch (\Exception $e) {
            $error     = 'Identifikasi gagal: ' . $e->getMessage()
                         . ' Pastikan server terhubung ke internet.';
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

    private function callInatApi(string $filePath, string $originalName): array
    {
        $url = 'https://api.inaturalist.org/v1/computervision/score_image';

        $cfile = new \CURLFile($filePath, mime_content_type($filePath), $originalName);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => [
                'image' => $cfile,
                'lat'   => -2.5,
                'lng'   => 118.0,
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => ['Accept: application/json'],
        ]);

        $body  = curl_exec($ch);
        $error = curl_error($ch);
        $code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error) {
            throw new \RuntimeException('cURL error: ' . $error);
        }

        if ($code < 200 || $code >= 300) {
            throw new \RuntimeException("API mengembalikan status {$code}.");
        }

        $data = json_decode($body, true);

        if (!isset($data['results'])) {
            throw new \RuntimeException('Respons API tidak valid.');
        }

        return array_slice($data['results'], 0, 5);
    }
}
