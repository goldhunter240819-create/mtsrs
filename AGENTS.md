# Panduan Tema & Layout UI: MTs RS (Zamrud Theme / Royal Sapphire)

File ini digunakan oleh AI (seperti saya) sebagai referensi aturan wajib ketika membuat modul baru atau mengubah tampilan halaman (layout) di aplikasi web MTs Roudlotus Sholihin.

## 1. Aturan Struktur Layout Utama
Aplikasi ini menggunakan **satu file layout sentral** untuk hampir semua modul utamanya, yaitu: `resources/views/layout.php`.

Ketika diminta membuat "modul baru" (misalnya `modulX`), **JANGAN** membuat file layout mandiri (seperti `modulX_layout.php`) yang menduplikasi sidebar dan header, KECUALI secara eksplisit diminta (seperti pada Absen V2).
Gunakan `layout.php` dengan cara berikut:
- Buka `resources/views/layout.php`.
- Tambahkan pendeteksi module di bagian atas (misal: `$isModulX = (strpos($activeMenu, 'modulx') === 0);`).
- Tambahkan menu sidebar untuk modul tersebut di dalam blok pengecekan `$isModulX` (di sekitar baris 250-280).
- Di dalam Controller modul baru tersebut, saat nge-render view, cukup set variabel `$activeMenu = 'modulx_nama_halaman'` lalu `include __DIR__ . '/../../resources/views/layout.php';`.

## 2. Aturan File View (Konten)
File-file view (seperti `dashboard.php`, `daftar_siswa.php` di dalam folder modul) **TIDAK BOLEH** mengandung:
- Tag `<html>`, `<head>`, `<body>`.
- Wrapper `<div class="siakad-container">` atau `<div class="z-main">`.
- Tag `<header class="z-header">` (header sudah di-*handle* oleh `layout.php`).
- Kode `include 'sidebar.php'`.

File view hanya boleh berisi komponen isi halamannya, contoh struktur yang benar:
```html
<div class="modern-page-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
    <div>
        <h1 class="mph-title" style="color: white;">
            <i data-lucide="icon-name" style="color: rgba(255,255,255,0.8);"></i> Judul Halaman
        </h1>
        <p class="mph-subtitle" style="color: rgba(255,255,255,0.9);">Deskripsi singkat halaman.</p>
    </div>
</div>

<div class="z-card">
    <!-- Isi Konten di sini -->
</div>
```

## 3. Komponen CSS & Iconography
- **Icons:** Selalu gunakan **Lucide Icons** dengan sintaks `<i data-lucide="nama-icon"></i>`.
- **Tombol (Button):** Jangan mengarang class sendiri seperti `.z-btn`. Gunakan class bawaan Zamrud yaitu `.btn` sebagai base class. Tambahkan variant seperti `.btn-primary`, `.btn-outline`, `.btn-danger`, atau `.btn-ghost` sesuai kebutuhan. Untuk ukuran kecil gunakan `.btn-sm`.
- **Card:** Gunakan class `.z-card` untuk membungkus form, tabel, atau blok konten agar memiliki bayangan halus khas Zamrud. Jika Anda memanipulasi lebar form/card di tengah layar dengan `margin: 0 auto;`, pastikan Anda **selalu memberikan jarak atas dan bawah** (misalnya `margin: 2rem auto;`) agar tidak menempel "mepet" dengan elemen *header* di atasnya. Selain itu, pastikan menambahkan **padding** (misal `padding: 2rem;`) di dalam `.z-card` jika isinya berupa form/teks agar tulisan tidak menabrak garis pinggir card.
- **Tabel:** Gunakan class `.z-table` untuk tabel data.
- **Scroll:** Jika ada area dengan konten panjang, gunakan `.z-scroll`.
- **Warna Identitas Modul:** Gunakan palet warna yang konsisten sesuai dengan modulnya (contoh: Siakad = Biru `#2563eb`, Kurikulum = Cyan `#0ea5e9`, Keuangan = Ungu `#7c3aed`, Manajemen Berkas = Merah `#be123c`).
- **Dashboard Stat Box:** Gunakan format layout *grid* dengan perpaduan warna *background* cerah dan *icon box* (contoh bisa dilihat di `siakad/dashboard.php`).
- **Filter Box (Form Pencarian/Filter):** Jika membuat form filter di atas tabel, berikan padding pada elemen select dan tombol agar tidak terlihat sempit ("mepet"). Gunakan inline CSS berikut pada `<select>`: `style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--z-border); background: var(--z-bg); color: var(--z-text); outline: none;"` dan pada `<button>` submit: `style="padding: 10px 20px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 8px;"`. Jangan hanya mengandalkan class `.z-input`.
- **Sidebar Menu:** Pastikan ukuran icon pada sidebar seimbang/sama dengan teks (misalnya icon 14px dan font teks 0.85rem) agar tidak lebih besar dan terlihat minimalis. Untuk *active state* menu sidebar, gunakan desain minimalis dengan *background* transparan tipis dan hilangkan *box-shadow* yang tebal agar tidak terlihat penuh/sesak.

## 4. Standar Notifikasi & Pop-up (SweetAlert2)
- **Library Default:** Aplikasi ini sudah menggunakan **SweetAlert2** secara global (di-load di `layout.php`).
- **Penggunaan:** **JANGAN** pernah menggunakan `alert()` bawaan browser. Selalu gunakan `Swal.fire()` untuk menampilkan notifikasi sukses, error, atau konfirmasi (terutama pada respons AJAX / Fetch).
- **Contoh Notifikasi Sukses:** 
  `Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Pesan sukses', timer: 1500, showConfirmButton: false })`
- **Contoh Notifikasi Error:** 
  `Swal.fire({ icon: 'error', title: 'Gagal', text: 'Pesan error' })`

## 5. Pembuatan Halaman Cetak (Print)
Jika membuat halaman untuk fitur "Cetak / Print":
- Jangan gunakan `layout.php`.
- Buat halaman HTML mandiri yang utuh.
- Untuk **Cetak 1 Halaman / Single Page** (misal Kartu, Sertifikat, Grafik Statistik), gunakan CSS untuk *fit page*: `@media print { @page { size: A4; margin: 10mm; } body { width: 100%; height: 100%; } }`.
- Untuk **Cetak Banyak Halaman / Laporan Multi-page** (misal RPP, Jurnal, Program Kerja), **JANGAN** memaksakan *fit 1 halaman*. Biarkan mengalir natural, tapi pastikan ada `@media print { page-break-after: always; }` jika butuh pindah halaman secara spesifik tiap *section*.
- Untuk **Kop Surat**, ambil format baku yang ada di `resources/views/siakad/kop_surat.php` (Gunakan font Times New Roman, huruf kapital tebal untuk Yayasan & Institusi, serta alamat lengkap yang dinamis ditarik dari `$institusi`).
- **Data Kop Surat** WAJIB diambil dari tabel `institusi` pada *database* `siakad` (misalnya: `$dbSiakad = Database::connect(); $institusi = $dbSiakad->query("SELECT * FROM institusi LIMIT 1")->fetch();`).
- Jangan lupa tambahkan **Favicon (Icon Tab)** di bagian `<head>` halaman cetak yang ditarik dari logo sekolah (misalnya: `<link rel="icon" type="image/png" href="/public/uploads/logo/<?= $institusi['logo'] ?>">`).
