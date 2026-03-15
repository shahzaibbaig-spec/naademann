# Naad-e-mann

This repository is now organized so GitHub updates stay focused on source files instead of generated output.

## Main folders

- `backend/` - the main Laravel website source code. This is the folder you should update for the live app.
- `deployment/shared-hosting/` - deployment helpers for shared hosting.
- `deployment/shared-hosting/build/` - generated package output. This is local build output and is no longer meant to be committed.
- Root `*.html`, `css/`, `js/`, and `images/` - legacy static prototype files kept for reference.

## Recommended GitHub workflow

If you are updating the Laravel website:

1. Make your code changes inside `backend/`.
2. Commit and push:
   ```powershell
   git add .
   git commit -m "Update website"
   git push origin main
   ```
3. On the server, pull the latest changes:
   ```powershell
   git pull origin main
   ```

## Shared hosting package

If you still deploy through a shared-hosting package, generate it locally with:

```powershell
.\deployment\shared-hosting\prepare-package.ps1
```

That command recreates `deployment/shared-hosting/build/` from `backend/`.

## Local uploads

For local upload testing, start PHP's built-in server directly with higher upload limits:

```powershell
cd .\backend\public
php -d upload_max_filesize=64M -d post_max_size=64M -d max_execution_time=120 -S 127.0.0.1:8001 ..\vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php
```
