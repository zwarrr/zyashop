# 🚨 LANGKAH PENTING - JALANKAN SQL DI VERCEL POSTGRES

## Cara Menjalankan SQL di Vercel Postgres:

### Opsi 1: Via Vercel Dashboard
1. Buka https://vercel.com/dashboard
2. Pilih project **zyashop**
3. Klik tab **Storage**
4. Klik database Postgres Anda
5. Klik **Query** atau **Data** tab
6. Jalankan SQL berikut:

```sql
ALTER TABLE products RENAME COLUMN image_url TO image;
```

### Opsi 2: Via Command Line (jika sudah setup)
```bash
vercel env pull
# Lalu gunakan connection string untuk connect ke database
psql <connection-string>
# Jalankan:
ALTER TABLE products RENAME COLUMN image_url TO image;
```

### Opsi 3: Via pgAdmin atau DBeaver
1. Dapatkan connection string dari Vercel Dashboard
2. Connect ke database menggunakan pgAdmin/DBeaver
3. Jalankan SQL di atas

## ⚠️ Setelah SQL Dijalankan:
1. Deployment Vercel akan otomatis berjalan
2. Refresh halaman `/produk`
3. Upload gambar produk baru
4. Gambar akan muncul!

## 📊 Verifikasi:
Setelah SQL dijalankan, jalankan query ini untuk verifikasi:
```sql
SELECT column_name, data_type 
FROM information_schema.columns 
WHERE table_name = 'products' 
AND column_name IN ('image', 'image_url');
```

Hasilnya harus menunjukkan kolom `image` (bukan `image_url`).
