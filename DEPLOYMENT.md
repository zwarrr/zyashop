# Zyashop - Deployment Guide

## Environment Configuration

Project ini menggunakan **2 environment files** yang terpisah:

### 1. **`.env`** - Localhost Development
```bash
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=zyashop_db
DB_USERNAME=root
DB_PASSWORD=
```
File ini untuk development di localhost dengan MySQL lokal.

### 2. **`.env.vercel`** - Vercel Production
```bash
DB_HOST=5h8j1o.h.filess.io
DB_PORT=61002
DB_DATABASE=ZyaShop_cattaskfog
DB_USERNAME=ZyaShop_cattaskfog
DB_PASSWORD=a81b358b3b11eebbe3adce8c61e5454d546ac773
```
File ini akan otomatis di-copy ke `.env` saat build di Vercel.

---

## Vercel Deployment

### How It Works

1. **Push code ke GitHub** → Vercel auto-detect changes
2. **Build process** → `vercel-build.sh` runs:
   - Copy `.env.vercel` → `.env`
   - Install composer dependencies
   - Create /tmp directories
3. **Deploy** → App runs with Filess.io database

### Prerequisites
- Vercel account connected to GitHub
- Filess.io MySQL database (already configured in `.env.vercel`)

---

## Deployment Steps

### 1. Push to GitHub

```bash
git add .
git commit -m "Update: Separate env files for localhost and Vercel"
git push origin main
```

### 2. Vercel Auto-Deploy
- Vercel detects push and starts build
- Build script copies `.env.vercel` to `.env`
- App deploys with production database

### 3. Verify Deployment
- Open: https://zyashop.vercel.app
- Check logs di Vercel Dashboard jika ada error

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
