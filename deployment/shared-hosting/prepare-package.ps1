param(
    [string]$BackendPath = (Join-Path $PSScriptRoot '..\..\backend'),
    [string]$BuildPath = (Join-Path $PSScriptRoot 'build')
)

$ErrorActionPreference = 'Stop'

$backend = (Resolve-Path $BackendPath).Path

if (-not (Test-Path $backend)) {
    throw "Backend path not found: $BackendPath"
}

if (Test-Path $BuildPath) {
    Remove-Item $BuildPath -Recurse -Force
}

$appPath = Join-Path $BuildPath 'laravel-app'
$publicPath = Join-Path $BuildPath 'public_html'

New-Item -ItemType Directory -Path $appPath -Force | Out-Null
New-Item -ItemType Directory -Path $publicPath -Force | Out-Null

$excludedRootNames = @(
    '.env',
    'node_modules'
)

Get-ChildItem $backend -Force | Where-Object { $_.Name -notin $excludedRootNames } | ForEach-Object {
    Copy-Item $_.FullName -Destination (Join-Path $appPath $_.Name) -Recurse -Force
}

$publicSource = Join-Path $backend 'public'

Get-ChildItem $publicSource -Force | Where-Object { $_.Name -ne 'storage' } | ForEach-Object {
    Copy-Item $_.FullName -Destination (Join-Path $publicPath $_.Name) -Recurse -Force
}

$envTemplateSource = Join-Path $PSScriptRoot '.env.production.example'
Copy-Item $envTemplateSource -Destination (Join-Path $appPath '.env.production.example') -Force

$publicPathTemplate = Join-Path $backend 'bootstrap\public_path.php.example'
if (Test-Path $publicPathTemplate) {
    Copy-Item $publicPathTemplate -Destination (Join-Path $appPath 'bootstrap\public_path.php') -Force
}

$runtimeCleanupPaths = @(
    (Join-Path $appPath 'storage\logs\*.log'),
    (Join-Path $appPath 'storage\framework\views\*'),
    (Join-Path $appPath 'storage\framework\cache\data\*'),
    (Join-Path $appPath 'storage\framework\sessions\*'),
    (Join-Path $appPath 'bootstrap\cache\*.php')
)

foreach ($pattern in $runtimeCleanupPaths) {
    Get-ChildItem $pattern -Force -ErrorAction SilentlyContinue | Remove-Item -Recurse -Force -ErrorAction SilentlyContinue
}

$sqlitePath = Join-Path $appPath 'database\database.sqlite'
if (Test-Path $sqlitePath) {
    Remove-Item $sqlitePath -Force
}

$indexPhp = @'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../laravel-app/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../laravel-app/vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../laravel-app/bootstrap/app.php';
$app->usePublicPath(__DIR__);

$app->handleRequest(Request::capture());
'@

Set-Content -Path (Join-Path $publicPath 'index.php') -Value $indexPhp -Encoding ASCII

$readme = @'
Shared hosting package generated successfully.

Upload targets:
- build/laravel-app -> /home/USERNAME/laravel-app
- build/public_html/* -> /home/USERNAME/public_html

Important:
- Rename laravel-app/.env.production.example to .env and fill production values.
- Run php artisan key:generate --force
- Run php artisan migrate --force
- Run php artisan db:seed --force
- Run php artisan storage:link
- Uploaded files also have a Laravel /storage fallback if symlinks are unavailable, but storage:link is still recommended for performance.
- This package already includes laravel-app/bootstrap/public_path.php for the sibling layout above.
- If you use a different folder layout, update public_html/index.php and laravel-app/bootstrap/public_path.php together.
'@

Set-Content -Path (Join-Path $BuildPath 'README.txt') -Value $readme -Encoding ASCII

Write-Output "Package created:"
Write-Output "  App: $appPath"
Write-Output "  Public: $publicPath"
