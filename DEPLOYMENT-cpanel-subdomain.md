# Laravel cPanel Subdomain Deployment

Target production URL:

- `https://www.kiosk.mirrorageconcepts.com`

## Preferred setup

Point the cPanel subdomain document root directly to:

- `/home/USERNAME/kiosk/public`

If cPanel allows that, Laravel will run with the normal `public/index.php` and `public/.htaccess`.

## Fallback setup when cPanel cannot point to `public/`

Use:

- `deploy/cpanel-subdomain/public_html/index.php`
- `deploy/cpanel-subdomain/public_html/.htaccess`

Then:

1. Copy those two files into the subdomain document root.
2. Update `$projectRoot` in `index.php` to the real absolute path of the Laravel project.
3. Keep the rest of the project outside the public web root.

## Environment

Create the production `.env` from:

- `.env.cpanel-subdomain.example`

Important values to set:

- `APP_KEY`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- `MAIL_*`
- `PAYSTACK_PUBLIC_KEY`
- `PAYSTACK_SECRET_KEY`

Keep:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://www.kiosk.mirrorageconcepts.com`
- `ASSET_URL=https://www.kiosk.mirrorageconcepts.com`
- `PAYSTACK_PUBLIC_APP_URL=https://www.kiosk.mirrorageconcepts.com`

## Ports and protocol

Use standard web ports only:

- HTTP: `80`
- HTTPS: `443`

Do not add a custom port to `APP_URL` unless the hosting provider explicitly requires one.

## Required writable paths

Make sure these are writable by the web server user:

- `storage/`
- `storage/app/`
- `storage/framework/`
- `storage/logs/`
- `bootstrap/cache/`

## Laravel optimization commands

Run these on the server after the final `.env` is in place:

```bash
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

If the app uses queues:

```bash
php artisan queue:table
php artisan migrate --force
```

## Route and callback checks

Verify these production endpoints resolve correctly:

- `/`
- `/login`
- `/register`
- `/shop`
- `/cart`
- `/checkout`
- `/orders`
- `/admin`
- `/payments/paystack/callback`
- `/webhooks/paystack`

For Paystack production config:

- Callback URL: `https://www.kiosk.mirrorageconcepts.com/payments/paystack/callback`
- Webhook URL: `https://www.kiosk.mirrorageconcepts.com/webhooks/paystack`

## Common cPanel issues

- Subdomain root points to the Laravel project root instead of `public/`
- `vendor/` is missing
- `APP_KEY` is empty
- `storage` or `bootstrap/cache` is not writable
- PHP version is lower than the Laravel version requires
- `.htaccess` rewrite rules are disabled
- Old cached config is still using a local URL

## Final smoke test

1. Open `https://www.kiosk.mirrorageconcepts.com`
2. Confirm HTTPS loads without redirect loops
3. Log in or register
4. Load static assets
5. Open the shop and cart flow
6. Test Paystack callback and webhook delivery
7. Open `/admin` with an allowed back-office account
