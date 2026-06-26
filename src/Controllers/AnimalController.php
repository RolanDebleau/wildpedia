<?php

namespace App\Controllers;

use App\Models\Animal;

class AnimalController
{
    public function index(): void
    {
        $filters = [
            'search'  => trim($_GET['search']  ?? ''),
            'status'  => trim($_GET['status']  ?? ''),
            'type'    => trim($_GET['type']    ?? ''),
            'endemic' => trim($_GET['endemic'] ?? ''),
        ];

        $page    = max(1, (int) ($_GET['page'] ?? 1));
        $animals = Animal::all($filters, $page);
        $types   = Animal::allTypes();
        $stats   = Animal::stats();

        $statuses = ['CR', 'EN', 'VU', 'NT', 'LC'];

        require VIEWS_PATH . '/animals/index.php';
    }

    public function show(string $slug): void
    {
        $animal = Animal::findBySlug($slug);

        if (!$animal) {
            http_response_code(404);
            require VIEWS_PATH . '/404.php';
            return;
        }

        $related = Animal::related($animal['type'], (int) $animal['id']);

        require VIEWS_PATH . '/animals/show.php';
    }
}
