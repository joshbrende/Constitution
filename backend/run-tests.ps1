# Run PHPUnit via artisan using PHP 8.2+ (composer.json requires ^8.2).
# Default PATH often points at WAMP's PHP 8.1 — this script picks 8.2/8.3/8.4 from WAMP if installed.
param(
    [Parameter(ValueFromRemainingArguments = $true)]
    [string[]] $Args
)

$ErrorActionPreference = 'Stop'
$backend = $PSScriptRoot

if ($env:PHP_BINARY -and (Test-Path $env:PHP_BINARY)) {
    $phpExe = $env:PHP_BINARY
    Write-Host "Using PHP_BINARY: $phpExe" -ForegroundColor Cyan
    & $phpExe (Join-Path $backend 'artisan') @('test') + $Args
    exit $LASTEXITCODE
}

$wampPhpRoot = 'C:\wamp64\bin\php'
$phpExe = $null
if (Test-Path $wampPhpRoot) {
    $dir = Get-ChildItem $wampPhpRoot -Directory -ErrorAction SilentlyContinue |
        Where-Object { $_.Name -match '^php8\.(2|3|4)\.' } |
        Sort-Object Name -Descending |
        Select-Object -First 1
    if ($dir) {
        $candidate = Join-Path $dir.FullName 'php.exe'
        if (Test-Path $candidate) { $phpExe = $candidate }
    }
}

if (-not $phpExe) {
    Write-Host @"
PHP 8.2+ not found.

WAMP: install a PHP 8.2+ build (WAMP menu -> PHP -> Version), or set for one session:
  `$env:PHP_BINARY = 'C:\wamp64\bin\php\php8.2.xx\php.exe'

Docker (from this folder, with Docker Desktop running):
  docker compose run --rm laravel.test php artisan test

Then add the chosen php.exe folder to your user PATH *before* the 8.1 folder so `php artisan` works everywhere.
"@ -ForegroundColor Yellow
    exit 1
}

Write-Host "Using: $phpExe" -ForegroundColor Cyan
& $phpExe (Join-Path $backend 'artisan') @('test') + $Args
exit $LASTEXITCODE
