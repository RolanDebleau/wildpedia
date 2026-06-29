<?php

namespace App\Models;

use App\Database\DB;
use PDO;

class Animal
{
    // ----- Query -----

    public static function all(array $filters = [], int $page = 1, int $perPage = 24): array
    {
        $db = DB::connect();

        [$where, $params] = self::buildWhere($filters);

        // Total untuk pagination
        $countSql  = "SELECT COUNT(*) FROM animals a {$where}";
        $countStmt = $db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $offset = ($page - 1) * $perPage;

        $orderBy = "ORDER BY CASE status WHEN 'CR' THEN 1 WHEN 'EN' THEN 2 WHEN 'VU' THEN 3 WHEN 'NT' THEN 4 WHEN 'LC' THEN 5 WHEN 'EW' THEN 6 WHEN 'EX' THEN 7 ELSE 8 END";

        $sql = "SELECT * FROM animals a {$where} {$orderBy} LIMIT :limit OFFSET :offset";
        $stmt = $db->prepare($sql);

        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll();

        // Lampirkan ancaman ke setiap hewan
        foreach ($rows as &$row) {
            $row['threats'] = self::getThreats((int) $row['id']);
        }

        return [
            'data'         => $rows,
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int) ceil($total / $perPage),
        ];
    }

    public static function findBySlug(string $slug): ?array
    {
        $db   = DB::connect();
        $stmt = $db->prepare("SELECT * FROM animals WHERE slug = :slug LIMIT 1");
        $stmt->execute([':slug' => $slug]);
        $row  = $stmt->fetch();

        if (!$row) return null;

        $row['threats'] = self::getThreats((int) $row['id']);

        return $row;
    }

    public static function related(string $type, int $excludeId, int $limit = 4): array
    {
        $db   = DB::connect();
        $stmt = $db->prepare(
            "SELECT * FROM animals WHERE type = :type AND id != :id LIMIT :lim"
        );
        $stmt->bindValue(':type', $type);
        $stmt->bindValue(':id', $excludeId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function allTypes(): array
    {
        $db   = DB::connect();
        $stmt = $db->query("SELECT DISTINCT type FROM animals ORDER BY type");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function stats(): array
    {
        $db  = DB::connect();
        $row = $db->query("
            SELECT
                COUNT(*) AS total,
                SUM(status = 'CR') AS cr,
                SUM(status = 'EN') AS en,
                SUM(status = 'VU') AS vu,
                SUM(is_endemic = 1) AS endemic
            FROM animals
        ")->fetch();

        return [
            'total'   => (int) $row['total'],
            'cr'      => (int) $row['cr'],
            'en'      => (int) $row['en'],
            'vu'      => (int) $row['vu'],
            'endemic' => (int) $row['endemic'],
        ];
    }

    public static function findById(int $id): ?array
    {
        $db   = DB::connect();
        $stmt = $db->prepare("SELECT * FROM animals WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row  = $stmt->fetch();
        if (!$row) return null;
        $row['threats'] = self::getThreats((int) $row['id']);
        return $row;
    }

    public static function count(): int
    {
        return (int) DB::connect()->query("SELECT COUNT(*) FROM animals")->fetchColumn();
    }

    public static function findByNameOrLatin(string $name, string $latin): ?array
    {
        $db   = DB::connect();
        $stmt = $db->prepare(
            "SELECT * FROM animals
             WHERE latin_name LIKE :latin OR name LIKE :name
             LIMIT 1"
        );
        $stmt->execute([':latin' => "%{$latin}%", ':name' => "%{$name}%"]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public static function findBySpeciesHint(string $englishName, string $latinHint): ?array
    {
        $db = DB::connect();

        if ($latinHint !== '') {
            $words = array_filter(explode(' ', trim($latinHint)));
            $words = array_values($words);

            if (count($words) >= 2) {
                $genusSpecies = $words[0] . ' ' . $words[1];

                $stmt = $db->prepare("SELECT * FROM animals WHERE latin_name LIKE :gs LIMIT 1");
                $stmt->execute([':gs' => "{$genusSpecies}%"]);
                $row = $stmt->fetch();
                if ($row) return $row;
            }
        }

        static $dictionary = [
            'tiger'        => 'harimau',
            'tiger cat'    => 'harimau',
            'orangutan'    => 'orangutan',
            'proboscis monkey' => 'bekantan',
            'gibbon'       => 'owa',
            'siamang'      => 'owa',
            'rhinoceros'   => 'badak',
            'african elephant' => 'gajah',
            'indian elephant'  => 'gajah',
            'tusker'       => 'gajah',
            'komodo dragon'    => 'komodo',
            'leatherback turtle' => 'penyu',
            'loggerhead turtle'  => 'penyu',
            'box turtle'   => 'kura-kura',
            'mud turtle'   => 'kura-kura',
            'terrapin'     => 'kura-kura',
            'american alligator' => 'buaya',
            'crocodile'    => 'buaya',
            'african crocodile' => 'buaya',
            'macaque'      => 'monyet',
            'langur'       => 'lutung',
            'colobus'      => 'lutung',
            'hornbill'     => 'rangkong',
            'peacock'      => 'merak',
            'whale shark'  => 'hiu paus',
            'dugong'       => 'pesut',
        ];

        $key = strtolower(trim($englishName));

        if (isset($dictionary[$key])) {
            $keyword = $dictionary[$key];
            $stmt = $db->prepare("SELECT * FROM animals WHERE name LIKE :kw LIMIT 1");
            $stmt->execute([':kw' => "%{$keyword}%"]);
            $row = $stmt->fetch();
            if ($row) return $row;
        }

        return null;
    }

    // ----- Helpers -----

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'CR'    => 'Kritis (CR)',
            'EN'    => 'Terancam (EN)',
            'VU'    => 'Rentan (VU)',
            'NT'    => 'Hampir Terancam (NT)',
            'LC'    => 'Tidak Terancam (LC)',
            'EW'    => 'Punah di Alam Liar (EW)',
            'EX'    => 'Punah (EX)',
            default => $status,
        };
    }

    public static function statusColor(string $status): string
    {
        return match ($status) {
            'CR'          => 'status-cr',
            'EN'          => 'status-en',
            'VU'          => 'status-vu',
            'NT'          => 'status-nt',
            'LC'          => 'status-lc',
            'EW', 'EX'   => 'status-ew',
            default       => '',
        };
    }

    // ----- Private -----

    private static function getThreats(int $animalId): array
    {
        $db   = DB::connect();
        $stmt = $db->prepare(
            "SELECT t.* FROM threats t
             JOIN animal_threat at ON at.threat_id = t.id
             WHERE at.animal_id = :id"
        );
        $stmt->execute([':id' => $animalId]);

        return $stmt->fetchAll();
    }

    private static function buildWhere(array $filters): array
    {
        $conditions = [];
        $params     = [];

        if (!empty($filters['search'])) {
            $conditions[] = "(a.name LIKE :search1 OR a.latin_name LIKE :search2
                              OR a.type LIKE :search3 OR a.habitat LIKE :search4)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[':search1'] = $searchTerm;
            $params[':search2'] = $searchTerm;
            $params[':search3'] = $searchTerm;
            $params[':search4'] = $searchTerm;
        }

        if (!empty($filters['status'])) {
            $conditions[] = 'a.status = :status';
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['type'])) {
            $conditions[] = 'a.type = :type';
            $params[':type'] = $filters['type'];
        }

        if (!empty($filters['endemic'])) {
            $conditions[] = 'a.is_endemic = 1';
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        return [$where, $params];
    }
}