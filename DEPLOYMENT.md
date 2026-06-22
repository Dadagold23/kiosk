# Deployment Guide

Target domain: `https://www.kiosk.mirrorageconcepts.com`

## 1. Server prerequisites

- PHP `8.2+`
- Composer `2+`
- MySQL/MariaDB
- Node.js `18+` (for frontend build)
- Web server (Nginx or Apache)
- Process manager for queue workers (recommended: Supervisor)

## 2. Project setup

```bash
git clone <your-repo-url> kiosk
cd kiosk
composer install --no-dev --optimize-autoloader
npm ci
npm run build
cp .env.example .env
php artisan key:generate
```

## 3. Environment configuration

Update `.env` values at minimum:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://www.kiosk.mirrorageconcepts.com`
- `APP_FORCE_HTTPS=true`
- `TRUSTED_PROXIES=*`
- `SESSION_DOMAIN=www.kiosk.mirrorageconcepts.com`
- `SESSION_SECURE_COOKIE=true`
- Database credentials (`DB_*`)
- Mail credentials (`MAIL_*`)
- Paystack credentials (`PAYSTACK_*`)

## 4. Database and app optimization

```bash
php artisan migrate --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 5. Queue worker (required)

Queue driver is `database`; run a worker continuously:

```bash
php artisan queue:work --tries=3 --timeout=120 --sleep=2
```

Use Supervisor/systemd in production so this process auto-restarts.

## 6. Scheduler (required for scheduled tasks)

Set cron to run every minute:

```cron
* * * * * cd /path/to/kiosk && php artisan schedule:run >> /dev/null 2>&1
```

## 7. Web server notes

- Point document root to `public/`
- Ensure HTTPS certificate is valid for `www.kiosk.mirrorageconcepts.com`
- Forward proxy headers correctly (for HTTPS detection)
- Keep `public/storage` symlink available

## 8. Post-deploy checks

- `php artisan about` shows:
  - Environment: `production`
  - Mail: configured mailer
  - Queue: `database`
- Test:
  - login/logout
  - checkout callback URL
  - queued jobs processing
  - SMTP send path
