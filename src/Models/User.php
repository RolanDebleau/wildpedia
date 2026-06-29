<?php

namespace App\Models;

use App\Database\DB;

class User
{
    public static function findByUsername(string $username): ?array
    {
        $db   = DB::connect();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :u LIMIT 1");
        $stmt->execute([':u' => $username]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByEmail(string $email): ?array
    {
        $db   = DB::connect();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :e LIMIT 1");
        $stmt->execute([':e' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findById(int $id): ?array
    {
        $db   = DB::connect();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByUsernameOrEmail(string $identifier): ?array
    {
        $db   = DB::connect();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username OR email = :email LIMIT 1");
        $stmt->execute([':username' => $identifier, ':email' => $identifier]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(string $name, string $username, string $email, string $password): int
    {
        $db   = DB::connect();
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("
            INSERT INTO users (name, username, email, password)
            VALUES (:name, :username, :email, :password)
        ");
        $stmt->execute([
            ':name'     => $name,
            ':username' => $username,
            ':email'    => $email,
            ':password' => $hash,
        ]);

        return (int) $db->lastInsertId();
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
