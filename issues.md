# Issues - UX Improvement Chat Confirmation

## Issue: Tombol CONFIRM dan EDIT Masih Muncul Setelah Transaksi Berhasil Disimpan

**File:** `application/views/chat/index.php`

**Deskripsi:**
Saat user melakukan chat dengan AI dan berhasil mengonfirmasi pencatatan transaksi (tombol CONFIRM ditekan), transaksi tersimpan ke database. Namun, tombol **CONFIRM** dan **EDIT** pada draft tersebut masih tetap muncul. Seharusnya setelah berhasil confirm, tombol-tombol tersebut hilang/berubah menjadi status "SUCCESS" atau "SAVED".

**Masalah Saat Ini:**
- Halaman melakukan `window.location.reload()` setelah confirm sukses (baris 148)
- Tapi draft tetap ditampilkan dengan tombol CONFIRM dan EDIT karena tidak ada pengecekan status konfirmasi
- User bisa menekan tombol CONFIRM berkali-kali (meskipun sudah disabled sementara)

**Yang Perlu Diperbaiki:**

1. **Hide tombol setelah confirm sukses (Frontend)**
   - Setelah AJAX `confirmDraft` sukses, sembunyikan tombol CONFIRM dan EDIT pada draft tersebut
   - Tampilkan badge/teks "✓ SAVED" sebagai gantinya
   - Jangan langsung reload halaman, biarkan user melihat feedback sukses dulu

2. **Tandai draft yang sudah dikonfirmasi (Backend/View)**
   - Tambahkan flag di meta_json (misal: `'confirmed' => true`) ketika transaksi sudah tersimpan
   - Di view, cek flag tersebut sebelum menampilkan tombol CONFIRM/EDIT
   - Jika `confirmed = true`, tampilkan status "SAVED" saja tanpa tombol

3. **UX Improvement:**
   - Tambahkan animasi/transisi saat tombol berubah menjadi status saved
   - Disable tombol EDIT jika sudah confirmed (atau hilangkan sama sekali)
   - Pastikan user tahu bahwa transaksi sudah tersimpan tanpa perlu reload otomatis

**Referensi Kode:**
- Baris 37-47: Tempat tombol CONFIRM dan EDIT dirender
- Baris 126-157: Fungsi `confirmDraft()` yang menangani AJAX confirm
- Baris 160-172: Event handler untuk tombol CONFIRM

**Hint untuk Junior:**
- Cek `$meta` di baris 16-18, tambahkan field `confirmed` ketika sukses menyimpan
- Gunakan jQuery `.closest()` atau `.parent()` untuk hide tombol setelah success
- Bisa juga tambahkan class CSS seperti `.draft-confirmed` untuk styling

**Priority:** Medium
**Difficulty:** Easy - Medium
