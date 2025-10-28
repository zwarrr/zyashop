-- Manual SQL untuk rename kolom products.image_url menjadi products.image
-- Jalankan di database production Vercel

ALTER TABLE products RENAME COLUMN image_url TO image;
