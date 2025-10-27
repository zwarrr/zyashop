# Ajax Loader Documentation

## 📋 Overview
Ajax Loader adalah komponen loading overlay yang reusable dan konsisten untuk semua halaman admin dan user. Komponen ini sudah diterapkan di seluruh aplikasi dengan styling yang seragam menggunakan Tailwind CSS dan Poppins font.

## 🎨 Features
- ✅ Reusable component (1 file untuk semua halaman)
- ✅ Consistent styling dengan desain existing
- ✅ Smooth animations (fade in/out)
- ✅ Customizable title dan message
- ✅ Auto prevent scrolling saat loading
- ✅ High z-index (80) untuk overlay semua modal
- ✅ Responsive design

## 📁 File Location
```
resources/views/partials/ajax_loader.blade.php
```

## 🔧 Installation

### 1. Include di Blade Template
Tambahkan di setiap halaman yang membutuhkan ajax loader:

```blade
<body>
  @include('partials.ajax_loader')
  
  <!-- Your content here -->
</body>
```

### 2. File yang Sudah Menggunakan Ajax Loader
- ✅ `resources/views/admin/cards.blade.php`
- ✅ `resources/views/admin/dashboard.blade.php`
- ✅ `resources/views/admin/produk.blade.php`
- ✅ `resources/views/admin/kategori.blade.php`
- ✅ `resources/views/admin/laporan.blade.php`
- ✅ `resources/views/admin/profile.blade.php`
- ✅ `resources/views/zyashp.blade.php`

## 📖 Usage

### Method 1: Manual Show/Hide
```javascript
// Show loader
showAjaxLoader('Memproses...', 'Mohon tunggu sebentar');

// Your async operation here
await someAsyncOperation();

// Hide loader
hideAjaxLoader();
```

### Method 2: With Try-Catch
```javascript
try {
  showAjaxLoader('Menyimpan Data', 'Sedang menyimpan...');
  
  const response = await fetch('/api/endpoint', {
    method: 'POST',
    body: formData
  });
  
  const data = await response.json();
  
  hideAjaxLoader();
  
  // Handle success
  alert('Success!');
} catch (error) {
  hideAjaxLoader();
  
  // Handle error
  alert('Error occurred!');
}
```

### Method 3: With Promise Finally
```javascript
fetch('/api/endpoint', {
  method: 'POST',
  body: formData
})
.then(response => {
  // Handle response
})
.catch(error => {
  // Handle error
})
.finally(() => {
  hideAjaxLoader(); // Always hide loader
});
```

### Method 4: Using Helper Function (ajaxWithLoader)
```javascript
const promise = fetch('/api/endpoint');

ajaxWithLoader(promise, 'Memuat Data', 'Mengambil data...')
  .then(response => response.json())
  .then(data => {
    // Handle success
  })
  .catch(error => {
    // Handle error
  });
```

### Method 5: Using fetchWithLoader Helper
```javascript
fetchWithLoader('/api/endpoint', {
  method: 'POST',
  body: formData
}, 'Menyimpan Data', 'Sedang menyimpan...')
  .then(response => response.json())
  .then(data => {
    // Handle success
  })
  .catch(error => {
    // Handle error
  });
```

## 🎯 Real Implementation Examples

### Example 1: Form Submit (dari cards.blade.php)
```javascript
document.getElementById('cardForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  
  const formData = new FormData(e.target);
  
  try {
    showAjaxLoader('Menyimpan Card', 'Sedang menyimpan data card...');
    
    const response = await fetch('/cards', {
      method: 'POST',
      body: formData
    });
    
    const data = await response.json();
    
    hideAjaxLoader();
    
    if (response.ok) {
      alert('Card berhasil disimpan!');
    } else {
      alert('Gagal menyimpan card');
    }
  } catch (error) {
    hideAjaxLoader();
    alert('Terjadi kesalahan');
  }
});
```

### Example 2: Edit/Load Data (dari cards.blade.php)
```javascript
function editCard(cardId) {
  showAjaxLoader('Memuat Data', 'Mengambil data card...');
  
  fetch(`/cards/${cardId}/edit`)
    .then(response => response.json())
    .then(data => {
      // Populate form with data
      document.getElementById('title').value = data.card.title;
      // ... etc
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Gagal memuat data');
    })
    .finally(() => {
      hideAjaxLoader();
    });
}
```

### Example 3: Delete Operation (dari cards.blade.php)
```javascript
function deleteCard(cardId) {
  if (confirm('Yakin ingin menghapus?')) {
    showAjaxLoader('Menghapus Card', 'Sedang menghapus card...');
    
    fetch(`/cards/${cardId}`, {
      method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
      hideAjaxLoader();
      alert('Card berhasil dihapus!');
      location.reload();
    })
    .catch(error => {
      hideAjaxLoader();
      alert('Gagal menghapus card');
    });
  }
}
```

## 🎨 Customization

### Default Messages
```javascript
showAjaxLoader(); 
// Title: "Memproses..."
// Message: "Mohon tunggu sebentar"
```

### Custom Messages
```javascript
showAjaxLoader('Mengunggah File', 'Sedang mengunggah file ke server...');
showAjaxLoader('Memproses Pembayaran', 'Mohon jangan tutup halaman ini...');
showAjaxLoader('Mengirim Email', 'Sedang mengirim email verifikasi...');
```

## 🎯 Common Use Cases

### 1. Login/Authentication
```javascript
showAjaxLoader('Masuk', 'Memverifikasi kredensial...');
```

### 2. File Upload
```javascript
showAjaxLoader('Mengunggah', 'Sedang mengunggah gambar...');
```

### 3. Data Fetching
```javascript
showAjaxLoader('Memuat Data', 'Mengambil data dari server...');
```

### 4. Form Submission
```javascript
showAjaxLoader('Menyimpan', 'Sedang menyimpan perubahan...');
```

### 5. Delete Operation
```javascript
showAjaxLoader('Menghapus', 'Sedang menghapus data...');
```

### 6. Update Operation
```javascript
showAjaxLoader('Memperbarui', 'Sedang memperbarui data...');
```

## ⚠️ Best Practices

### ✅ DO:
- Always hide loader in `finally()` block or catch
- Use descriptive messages
- Show loader before async operation
- Handle errors properly
- Use try-catch or .finally() for cleanup

### ❌ DON'T:
- Don't forget to hide loader
- Don't use loader for synchronous operations
- Don't show multiple loaders simultaneously
- Don't use very long messages

## 🐛 Troubleshooting

### Loader tidak muncul?
1. Pastikan `@include('partials.ajax_loader')` sudah ditambahkan
2. Cek console untuk error JavaScript
3. Pastikan memanggil `showAjaxLoader()` dengan benar

### Loader tidak hilang?
1. Pastikan memanggil `hideAjaxLoader()` di `finally()` atau `catch()`
2. Cek apakah ada error yang tidak tertangani
3. Gunakan browser DevTools untuk debug

### Scrolling masih bisa saat loader muncul?
- Ini sudah dihandle otomatis, tapi pastikan tidak ada CSS custom yang override

## 🔄 Updates & Maintenance

File ini bersifat reusable dan tidak perlu diubah kecuali untuk:
- Perubahan styling global
- Perubahan animasi
- Penambahan fitur baru

Jika perlu update, edit file:
```
resources/views/partials/ajax_loader.blade.php
```

## 📞 Support

Jika ada pertanyaan atau issue, silakan buat issue di repository atau hubungi developer.

---

**Last Updated:** October 27, 2025
**Version:** 1.0.0
