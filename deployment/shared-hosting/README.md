# Shared Hosting Package

This folder prepares a cPanel-style deployment for the Laravel app in [backend](../../backend).

## Output structure

Run:

```powershell
.\deployment\shared-hosting\prepare-package.ps1
```

It creates:

```text
deployment/shared-hosting/build/
  laravel-app/
  public_html/
```

Use it like this:

1. Upload `laravel-app` to `/home/USERNAME/laravel-app`.
2. Upload the contents of `public_html` into `/home/USERNAME/public_html`.
3. Rename `laravel-app/.env.production.example` to `.env`.
4. Update domain and database values in `.env`.
5. Run:

```bash
php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Notes

- This package excludes the local `.env`.
- It removes the local SQLite file so production can use MySQL.
- `public_html/index.php` is rewritten to load Laravel from `../laravel-app`.
- `laravel-app/bootstrap/public_path.php` is included so `php artisan storage:link` creates `public_html/storage`.
- The package intentionally does not copy `public/storage` into `public_html`, so the storage symlink can be created cleanly.
- Uploaded files already live under `laravel-app/storage/app/public`, so they become public after `storage:link`.
- The app now includes a `/storage/{path}` Laravel fallback, so uploaded covers and audio still load if the shared host refuses to create the symlink.
- If your hosting layout is not `~/laravel-app` and `~/public_html`, update both `public_html/index.php` and `laravel-app/bootstrap/public_path.php`.
- `php artisan storage:link` is still recommended because direct web-server file serving is faster than routing uploads through PHP.
