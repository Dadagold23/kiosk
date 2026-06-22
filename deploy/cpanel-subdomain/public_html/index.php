<?php

declare(strict_types=1);

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| cPanel subdomain entrypoint
|--------------------------------------------------------------------------
|
| Use this file only if cPanel does not allow the subdomain document root
| to point directly to the Laravel project's public/ directory.
|
| Example:
| - Project folder: /home/USERNAME/kiosk
| - Subdomain doc root: /home/USERNAME/public_html/kiosk
|
| Update $projectRoot below to the real absolute path on the server.
|
*/

$projectRoot = '/home/USERNAME/kiosk';

if (file_exists($maintenance = $projectRoot . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $projectRoot . '/vendor/autoload.php';

$app = require_once $projectRoot . '/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);

