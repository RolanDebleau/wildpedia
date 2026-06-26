<?php

namespace App\Models;

use App\Database\DB;

class Admin
{
    public static function findByUsername(string $username): ?array
    {
        $db   = DB::connect();
        $stmt = $db->prepare("SELECT * FROM admins WHERE username = :u LIMIT 1");
        $stmt->execute([':u' => $username]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
