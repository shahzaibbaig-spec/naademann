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
