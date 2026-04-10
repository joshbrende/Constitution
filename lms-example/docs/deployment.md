# Deployment Guide

This guide covers deploying the Laravel LMS to staging and production environments.

**Production domain:** `www.ttm-group.co.za` (TTM Group branded).

## Pre-Deployment Checklist

- [ ] All tests passing
- [ ] Environment variables configured (see TTM Group branding below)
- [ ] Database migrations ready
- [ ] Assets compiled and optimized
- [ ] Storage symlink created
- [ ] Favicon copied to `public/favicon.ico` (from main site if same server)
- [ ] Queue workers configured (if using queues)
- [ ] Cron jobs configured
- [ ] Backup strategy in place

## Environment Configuration

### 1. Environment File

Copy `.env.example` to `.env` and configure for **TTM Group production**:

```env
APP_NAME="TTM Group LMS"
APP_ENV=production
APP_KEY=base64:... # Generate with: php artisan key:generate
APP_DEBUG=false
APP_URL=https://www.ttm-group.co.za

# If LMS is in a subfolder (e.g. /lms):
# APP_URL=https://www.ttm-group.co.za/lms

# Optional brand overrides (config/brand.php defaults match main site)
# BRAND_WEBSITE_URL=https://www.ttm-group.co.za
# BRAND_CONTACT_EMAIL=events@ttm-group.co.za
# BRAND_CONTACT_PHONE=+27 66 243 1698

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lms_production
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=database
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@your-domain.com"
MAIL_FROM_NAME="${APP_NAME}"

# If using queues for notifications
QUEUE_CONNECTION=database
```

### 2. Security Settings

**Production `.env`:**
- `APP_DEBUG=false` (never true in production)
- `APP_ENV=production`
- Strong `APP_KEY` (auto-generated)
- Secure database credentials
- Proper mail configuration

## Deployment Steps

### Option A: Traditional Server (Apache/Nginx)

#### 1. Server Requirements

- PHP 8.2+ with extensions: `openssl`, `pdo`, `mbstring`, `tokenizer`, `xml`, `curl`, `zip`, `gd`
- MySQL 5.7+ or MariaDB 10.3+
- Composer
- Node.js & NPM (for asset compilation, optional)

#### 2. Upload Files

```bash
# Exclude unnecessary files
rsync -avz --exclude='node_modules' --exclude='.git' \
  --exclude='.env' --exclude='storage/logs/*' \
  ./ user@server:/var/www/lms/
```

#### 3. Install Dependencies

```bash
cd /var/www/lms
composer install --optimize-autoloader --no-dev
```

#### 4. Configure Environment

```bash
cp .env.example .env
nano .env  # Edit with production values
php artisan key:generate
```

#### 5. Database Setup

```bash
php artisan migrate --force
php artisan db:seed --class=RolesSeeder  # If needed
```

#### 6. Storage & Cache

```bash
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 7. Permissions

```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### 8. Web Server Configuration

**Apache (`/etc/apache2/sites-available/lms.conf`):**

```apache
<VirtualHost *:80>
    ServerName www.ttm-group.co.za
    DocumentRoot /var/www/lms/public

    <Directory /var/www/lms/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/lms_error.log
    CustomLog ${APACHE_LOG_DIR}/lms_access.log combined
</VirtualHost>
```

**Nginx (`/etc/nginx/sites-available/lms`):**

```nginx
server {
    listen 80;
    server_name www.ttm-group.co.za;
    root /var/www/lms/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Option B: Laravel Forge / Envoyer

1. **Connect repository** (GitHub/GitLab/Bitbucket)
2. **Configure server** (PHP version, database)
3. **Set environment variables** in Forge dashboard
4. **Deploy**: Forge handles migrations, cache clearing, etc.

### Option C: Docker / Laravel Sail

```bash
# Build and start
docker-compose up -d

# Run migrations
docker-compose exec app php artisan migrate

# Install dependencies
docker-compose exec app composer install --optimize-autoloader --no-dev
```

## Staging Environment

### Setup

1. Create separate database: `lms_staging`
2. Use staging `.env`:
   ```env
   APP_ENV=staging
   APP_DEBUG=true  # Can be true for debugging
   APP_URL=https://staging.your-domain.com
   ```
3. Use test data or production copy (sanitized)
4. Configure separate mail (e.g., Mailtrap for testing)

### Testing on Staging

- [ ] User registration/login
- [ ] Course enrollment
- [ ] Course learning flow
- [ ] Knowledge Checks
- [ ] Assignment submission
- [ ] Notifications
- [ ] Certificate generation
- [ ] Admin functions
- [ ] Mobile responsiveness

## Queue Workers (If Using Queues)

For notifications and other queued jobs:

```bash
# Supervisor config: /etc/supervisor/conf.d/lms-worker.conf
[program:lms-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/lms/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/lms/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start lms-worker:*
```

## Cron Jobs

Laravel's scheduler (if needed):

```bash
# Add to crontab: crontab -e
* * * * * cd /var/www/lms && php artisan schedule:run >> /dev/null 2>&1
```

## Database Backups

### Automated Backups

```bash
# Daily backup script: /usr/local/bin/lms-backup.sh
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u your_db_user -p'your_password' lms_production > /backups/lms_$DATE.sql
# Keep last 7 days
find /backups -name "lms_*.sql" -mtime +7 -delete
```

```bash
# Add to crontab: crontab -e
0 2 * * * /usr/local/bin/lms-backup.sh
```

## Monitoring & Logs

### Log Files

- Laravel logs: `storage/logs/laravel.log`
- Web server logs: Apache/Nginx access/error logs
- Queue worker logs: `storage/logs/worker.log`

### Monitoring Tools

- **Laravel Telescope** (dev/staging only)
- **Sentry** (error tracking)
- **New Relic** (performance monitoring)
- **Server monitoring**: UptimeRobot, Pingdom

## SSL/HTTPS

Use Let's Encrypt:

```bash
sudo certbot --apache -d your-domain.com
# or
sudo certbot --nginx -d your-domain.com
```

Update `.env`:
```env
APP_URL=https://www.ttm-group.co.za
```

### Favicon (TTM Group branding)

Copy the main site favicon into the LMS public folder so the LMS shows the TTM Group icon:

```bash
cp /path/to/main-site/favicon.ico /var/www/lms/public/favicon.ico
```

If the main site and LMS share the same server (e.g. main at `/var/www/ttm-group` and LMS at `/var/www/lms`), use the same `favicon.ico` from the main site assets.

## Performance Optimization

### 1. Cache Configuration

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. Opcache (PHP)

Enable in `php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
```

### 3. Database Optimization

- Add indexes for frequently queried columns
- Use database query caching
- Optimize slow queries

### 4. Asset Optimization

```bash
# If using Laravel Mix/Vite
npm run production
```

## Rollback Procedure

If deployment fails:

```bash
# Rollback migrations
php artisan migrate:rollback --step=1

# Restore previous code version
git checkout previous-tag

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Post-Deployment

1. **Verify**:
   - Homepage loads
   - Login works
   - Course enrollment works
   - Database connections

2. **Monitor**:
   - Error logs
   - Performance metrics
   - User feedback

3. **Update documentation**:
   - Deployment date
   - Version number
   - Known issues

## Troubleshooting

### Common Issues

**500 Error:**
- Check `.env` configuration
- Verify file permissions
- Check Laravel logs: `storage/logs/laravel.log`

**Database Connection:**
- Verify credentials in `.env`
- Check database server is running
- Test connection: `php artisan tinker` → `DB::connection()->getPdo();`

**Storage Issues:**
- Ensure `storage:link` is created
- Check `storage/` permissions
- Verify disk space

**Queue Not Processing:**
- Check supervisor status: `supervisorctl status`
- Review queue worker logs
- Verify database queue table exists

## Security Checklist

- [ ] `APP_DEBUG=false` in production
- [ ] Strong database passwords
- [ ] HTTPS enabled
- [ ] File permissions set correctly
- [ ] `.env` file not publicly accessible
- [ ] Regular security updates
- [ ] SQL injection protection (Laravel Eloquent)
- [ ] XSS protection (Blade escaping)
- [ ] CSRF protection enabled
- [ ] Rate limiting configured

## Support

For deployment issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Review server logs (Apache/Nginx)
3. Check database connectivity
4. Verify environment variables
5. Test on staging first
