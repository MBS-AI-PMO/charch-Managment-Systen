# Production build script for Church CMS Phase 1
# Run from project root in elevated PowerShell.

$ErrorActionPreference = 'Stop'
Write-Host "==> Installing PHP dependencies (no dev)..."
& 'D:\XAMPP\php\php.exe' 'D:\XAMPP\composer\composer' install --no-dev --optimize-autoloader

Write-Host "==> Caching config / routes / views / events..."
& 'D:\XAMPP\php\php.exe' artisan config:cache
& 'D:\XAMPP\php\php.exe' artisan route:cache
& 'D:\XAMPP\php\php.exe' artisan view:cache
& 'D:\XAMPP\php\php.exe' artisan event:cache

Write-Host "==> Building JS/CSS bundle..."
npm ci
npm run build

Write-Host "==> Linking public storage (if not yet)..."
if (-not (Test-Path "public/storage")) {
    cmd /c mklink /J "public\storage" "storage\app\public"
}

Write-Host "==> Done. Verify .env APP_ENV=production, APP_DEBUG=false, APP_KEY set."
