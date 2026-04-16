<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

define('APP_ROOT', dirname(__DIR__));

$baseUrl = str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '')));
$baseUrl = rtrim($baseUrl, '/');
define('BASE_URL', $baseUrl === '/' ? '' : $baseUrl);

// Charger le routeur manuellement (problème de casse PSR-4)
require_once APP_ROOT . '/router/router.php';

$router = require APP_ROOT . '/router/routes.php';
$router->run();
