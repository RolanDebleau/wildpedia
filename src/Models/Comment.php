<?php

namespace App\Models;

use App\Database\DB;
use PDO;

class Comment
{
    public static function allByAnimal(int $animalId, bool $onlyApproved = true): array
    {
        $db   = DB::connect();
        $sql  = "SELECT * FROM comments WHERE animal_id = :id"
              . ($onlyApproved ? " AND approved = 1" : "")
              . " ORDER BY created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $animalId]);
        return $stmt->fetchAll();
    }

    public static function all(): array
    {
        $db = DB::connect();
        $stmt = $db->query("
            SELECT c.*, a.name AS animal_name, a.slug AS animal_slug
            FROM comments c
            JOIN animals a ON a.id = c.animal_id
            ORDER BY c.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public static function create(int $animalId, string $nama, string $isi, ?int $userId = null): bool
    {
        $db   = DB::connect();
        $stmt = $db->prepare(
            "INSERT INTO comments (animal_id, user_id, nama, isi, approved) VALUES (:animal_id, :user_id, :nama, :isi, 0)"
        );
        return $stmt->execute([
            ':animal_id' => $animalId,
            ':user_id'   => $userId,
            ':nama'      => trim($nama),
            ':isi'       => trim($isi),
        ]);
    }

    public static function approve(int $id): bool
    {
        $db   = DB::connect();
        $stmt = $db->prepare("UPDATE comments SET approved = 1 WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public static function delete(int $id): bool
    {
        $db   = DB::connect();
        $stmt = $db->prepare("DELETE FROM comments WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public static function countPending(): int
    {
        $db = DB::connect();
        return (int) $db->query("SELECT COUNT(*) FROM comments WHERE approved = 0")->fetchColumn();
    }
}
