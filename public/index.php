<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Cek mode maintenance (jika ada)
if (file_exists(__DIR__.'/../storage/framework/maintenance.php')) {
    require __DIR__.'/../storage/framework/maintenance.php';
}

// Daftarkan autoloader Composer
require __DIR__.'/../vendor/autoload.php';

// Bootstrap aplikasi Laravel dan tangani request
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
