<?php

return [
    'database' => [
        'host' => 'localhost',
        'port' => '3306',
        'name' => 'wildpedia',
        'user' => 'root',
        'pass' => '',
    ],
    'app' => [
        'name' => 'WildPedia Indonesia',
        'url'  => 'http://localhost/wildpedia/public',
    ],
    'huggingface' => [
        'token' => '',
        'model' => 'google/vit-base-patch16-224',
    ],
];
