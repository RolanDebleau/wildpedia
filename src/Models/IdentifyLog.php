<?php

namespace App\Models;

use App\Database\DB;

class IdentifyLog
{
    public static function create(array $data): void
    {
        $db   = DB::connect();
        $stmt = $db->prepare(
            "INSERT INTO identify_logs
             (image_path, api_result, identified_animal, confidence, user_ip, created_at, updated_at)
             VALUES (:image_path, :api_result, :identified_animal, :confidence, :user_ip, NOW(), NOW())"
        );

        $stmt->execute([
            ':image_path'        => $data['image_path'],
            ':api_result'        => $data['api_result'] ?? null,
            ':identified_animal' => $data['identified_animal'] ?? null,
            ':confidence'        => $data['confidence'] ?? null,
            ':user_ip'           => $data['user_ip'] ?? null,
        ]);
    }
}
