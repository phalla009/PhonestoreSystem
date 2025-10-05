<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Maintenance mode
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Autoload
require __DIR__.'/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// Make HTTP kernel
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Capture request
$request = Request::capture();

// Handle request and send response
$response = $kernel->handle($request);
$response->send();

// Terminate kernel
$kernel->terminate($request, $response);
