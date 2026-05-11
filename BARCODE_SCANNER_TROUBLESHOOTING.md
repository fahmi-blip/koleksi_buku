# 🎫 Barcode Scanner - Troubleshooting Guide

## ✅ Perbaikan yang sudah dilakukan:

### 1. **Decode Loop Improvements**

- Menambahkan console logging untuk debugging
- Mengurangi timeout antara retry (120ms → 100ms) untuk response lebih cepat
- Set `isScanning` flag dengan benar di stopScanner()

### 2. **Video Initialization**

- Menambahkan wait untuk video `HAVE_ENOUGH_DATA` status
- Memastikan stream fully loaded sebelum mulai decode
- Proper error handling untuk camera access

### 3. **File Upload Handler**

- Menambahkan timeout protection untuk image loading
- Better error messages
- Retry logic dengan preprocessing (grayscale + threshold)

### 4. **Auto-scroll & Modal**

- Modal otomatis menutup setelah barcode berhasil di-scan
- Auto-scroll ke `tokoDbBox` untuk menampilkan data hasil scan

---

## 🔍 Debugging Steps:

### Step 1: Check Browser Console

Buka DevTools (F12) dan lihat Console tab:

```
✓ Output yang diharapkan:
═══════════════════════════════════════════
🎫 Kunjungan Toko Scanner - Initialized
═══════════════════════════════════════════
✓ Event listeners attached
═══════════════════════════════════════════
```

### Step 2: Saat Scan Barcode via Camera

Lihat log messages:

```
→ Memulai kamera…
✓ Scanner started
✓ Barcode detected: [BARCODE_VALUE]
📡 Sending request to lookup API...
📬 Response status: 200
📦 Response data: {...}
✓ Toko found: {...}
```

Jika error:

```
❌ Error: Toko tidak ditemukan
🔴 Fetch error: [ERROR_MESSAGE]
```

### Step 3: Saat Upload File Gambar

```
📸 Processing file: [FILENAME] [SIZE] bytes
✓ Image loaded: [WIDTH] x [HEIGHT]
→ Attempting direct decode...
✓ Barcode detected from file: [BARCODE_VALUE]
```

---

## 🚨 Common Issues & Solutions:

### ❌ Issue: "Barcode tidak terbaca saat scan"

**Solusi:**

1. Pastikan **lighting cukup terang** - barcode perlu cahaya yang jelas
2. Arahkan kamera **tegak lurus ke barcode** (bukan sudut)
3. Jarak optimal: **10-15 cm** dari kamera
4. Gerakkan perlahan dan stabil

**Debug:**

- Buka Console (F12) saat scan
- Lihat apakah ada message "Barcode detected"
- Jika tidak ada message, barcode tidak terdeteksi oleh camera

### ❌ Issue: "Barcode terdeteksi tapi data toko tidak muncul"

**Kemungkinan:**

- Barcode ada di system tetapi toko tidak ada di database
- API endpoint error (check Network tab di DevTools)

**Solusi:**

1. Buka DevTools → Network tab
2. Scan barcode
3. Lihat request ke `/kunjungan-toko/lookup`
4. Check Response - apakah `success: false`?

Jika response:

```json
{
    "success": false,
    "message": "Toko tidak ditemukan"
}
```

**→ Toko dengan barcode ini belum ditambahkan ke database**

### ❌ Issue: "Kamera ditolak"

**Solusi:**

1. Check browser permissions untuk camera access
2. Chrome: Click lock icon → Site Settings → Camera → Allow
3. Firefox: Preferences → Privacy → Permissions → Camera → Allow
4. Reload halaman setelah izin diberikan

### ❌ Issue: "File upload tidak membaca barcode"

**Solusi:**

1. Pastikan gambar jelas dan kontras tinggi
2. Barcode harus fully visible dalam frame
3. Hindari refleksi/glare pada barcode
4. Coba dengan file yang berbeda

**Debug:**

- Check console untuk error messages
- Lihat apakah preprocessing berhasil: `→ Attempting decode with preprocessing...`

---

## 📝 Testing Barcode:

Sebelum test scanning, pastikan ada data Toko di database:

```php
// Jalankan di Laravel Tinker
php artisan tinker
```

```php
// Check jumlah toko
Toko::count()

// Lihat semua toko
Toko::all()

// Lihat barcode dari toko pertama
Toko::first()->barcode

// Jika belum ada, buat toko baru
Toko::create([
    'barcode' => 'TEST0001',
    'name' => 'Toko Test',
    'latitude' => -6.123456,
    'longitude' => 106.654321,
    'accuracy' => 10.5,
])
```

---

## 🎯 Test Scenarios:

### Test 1: Manual Barcode Input

- Buka form
- Ketik barcode langsung di field `visit_barcode`
- Klik tombol "Cek Kunjungan"
- Data harus muncul di `tokoDbBox`

### Test 2: Camera Scan

- Click tombol "Scanner"
- Pilih camera (jika ada multiple cameras)
- Arahkan ke barcode
- Tunggu detection

### Test 3: File Upload

- Click tombol "Scanner"
- Click field "Unggah Foto Barcode"
- Pilih file gambar yang contain barcode
- Tunggu processing

---

## 📊 Console Log Levels:

- `✓` = Success/Complete
- `🔍` = Processing/Lookup
- `📡` = API Request
- `📬` = API Response
- `🚨` = Error/Problem
- `⚠` = Warning
- `→` = Step/Progress

---

## 🔧 Advanced Debug Mode:

Tambahkan ini di Console untuk extra logging:

```javascript
// Enable verbose mode
window.DEBUG_BARCODE = true;

// Check current state
console.log("isScanning:", isScanning);
console.log("codeReader:", codeReader);
console.log(
    "visitBarcode value:",
    document.getElementById("visit_barcode").value,
);
```

---

## 📞 Report Issues:

Jika masalah persist, collect:

1. Screenshot dari Console output
2. Network request/response dari barcode lookup
3. Device/browser information
4. Barcode sample (gambar)
5. Database check: berapa jumlah toko yang ada
