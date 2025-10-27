# Zyashop - Deployment Guide

## Vercel Deployment

### Prerequisites
- Vercel account
- GitHub repository connected to Vercel
- Filess.io MySQL database

### Environment Variables (Set di Vercel Dashboard)

**PENTING:** Jangan lupa set environment variable `DB_PASSWORD` di Vercel Dashboard!

```bash
# Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://zyashop.vercel.app
APP_KEY=base64:TGaVaXwhWgjn9akhE4SCSIjGG/cQqvICUQ+PgLA3Bs0=

# Cache
APP_CONFIG_CACHE=/tmp/config.php
APP_EVENTS_CACHE=/tmp/events.php
APP_PACKAGES_CACHE=/tmp/packages.php
APP_ROUTES_CACHE=/tmp/routes.php
APP_SERVICES_CACHE=/tmp/services.php
VIEW_COMPILED_PATH=/tmp
CACHE_DRIVER=array

# Logging
LOG_CHANNEL=stderr

# Session
SESSION_DRIVER=cookie
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

# Security
SANCTUM_STATEFUL_DOMAINS=zyashop.vercel.app,*.vercel.app

# Storage
FILESYSTEM_DISK=public

# Queue
QUEUE_CONNECTION=sync

# Database (Filess.io)
DB_CONNECTION=mysql
DB_HOST=5h8j1o.h.filess.io
DB_PORT=61002
DB_DATABASE=ZyaShop_cattaskfog
DB_USERNAME=ZyaShop_cattaskfog
DB_PASSWORD=your_password_here  # SET THIS IN VERCEL DASHBOARD!
```

### Deployment Steps

1. **Push to GitHub:**
   ```bash
   git add .
   git commit -m "Fix Vercel deployment configuration"
   git push origin main
   ```

2. **Vercel akan otomatis deploy**
   - Vercel akan detect perubahan di GitHub
   - Build akan berjalan otomatis
   - Jika error, cek logs di Vercel Dashboard

3. **Set Environment Variables:**
   - Buka Vercel Dashboard → Project Settings → Environment Variables
   - Tambahkan variable `DB_PASSWORD` dengan value password database Filess.io
   - Redeploy project

### Troubleshooting Error 500

**Penyebab Umum:**

1. **Missing `DB_PASSWORD`** - Environment variable tidak diset
2. **Database Connection Failed** - Cek kredensial Filess.io
3. **Storage Permission** - Vercel menggunakan `/tmp` untuk writable storage
4. **Missing Vendor** - Pastikan `composer install` berjalan saat build
5. **Cache Issues** - Clear cache: `vercel env rm CACHE_*`

**Check Logs:**
```bash
# Di Vercel Dashboard → Deployments → [Latest] → View Function Logs
```

### Testing Locally

```bash
# Start local server
php artisan serve

# Test database connection
php artisan migrate:status

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### File Upload Limitations di Vercel

⚠️ **PENTING:** Vercel serverless functions memiliki batasan:
- Max file size: **4.5 MB** per request
- Max request duration: **30 seconds**
- Storage: Read-only (kecuali `/tmp`)

Untuk file upload yang lebih besar, pertimbangkan:
- Cloudinary
- AWS S3
- Vercel Blob Storage
- ImgBB

### Database Information

**Provider:** Filess.io (Free MySQL Database)
- Host: `5h8j1o.h.filess.io`
- Port: `61002`
- Database: `ZyaShop_cattaskfog`
- Username: `ZyaShop_cattaskfog`
- Password: **(Set di Vercel Environment Variables)**

### Support

Jika masih error 500:
1. Cek Vercel Function Logs
2. Pastikan semua environment variables terisi
3. Test database connection dari local
4. Cek apakah migration sudah jalan
