# Database Setup Guide - RSUD Kabupaten Kerinci

## Persyaratan
- XAMPP atau PHP 7.4+
- MySQL Server
- Browser modern

## Langkah-Langkah Setup Database

### Metode 1: Menggunakan Setup Script PHP (Rekomendasi)

1. **Akses Setup Page**
   - Buka browser dan kunjungi: `http://localhost/rsudkabkerinci.go.id/config/setup_database.php`
   - Halaman akan membuat database dan tabel secara otomatis
   - Anda akan melihat pesan "Database setup selesai!"

2. **Hapus File Setup**
   - Setelah setup selesai, hapus file `config/setup_database.php`
   - Ini untuk keamanan dan mencegah setup dijalankan kembali

### Metode 2: Menggunakan phpMyAdmin

1. **Buka phpMyAdmin**
   - Kunjungi: `http://localhost/phpmyadmin`
   - Login dengan username `root` (tanpa password)

2. **Import SQL**
   - Klik pada "Import" di menu atas
   - Pilih file `config/database.sql`
   - Klik "Go" untuk mengimport database

3. **Verifikasi Database**
   - Database `rsud_kerinci` akan dibuat dengan semua tabel

### Metode 3: Menggunakan MySQL Command Line

```bash
mysql -u root -p < C:\xampp\htdocs\rsudkabkerinci.go.id\config\database.sql
```

## Struktur Database

### Tabel: `pasien`
Menyimpan data pendaftaran pasien baru
- Kolom utama: id, nama, noktp, tgllahir, jeniskelamin, alamat, telepon, email
- Kolom kesehatan: goldarah, agama, penyakit, alergi
- Kolom penanggung jawab: namapj, hubungan, teleponpj, alamatpj
- Kolom asuransi: asuransi, nomorasuransi

### Tabel: `kontak`
Menyimpan pesan dari form contact us
- Kolom: id, nama, email, pesan, created_at, status

### Tabel: `jadwal_dokter`
Menyimpan jadwal dokter
- Kolom: id, nama_dokter, spesialisasi, hari, jam_mulai, jam_selesai, lokasi

### Tabel: `berita`
Menyimpan berita/artikel
- Kolom: id, judul, konten, gambar, tanggal_publikasi, penulis, status

### Tabel: `pengumuman`
Menyimpan pengumuman penting
- Kolom: id, judul, isi, tanggal_publikasi, penulis, status

## File Konfigurasi

### `config/database.php`
File koneksi database yang digunakan oleh semua API handler
- Host: localhost
- User: root
- Password: (kosong)
- Database: rsud_kerinci

### API Handlers

#### `api/handle_pendaftaran.php`
- Menangani form pendaftaran pasien
- Validasi data input
- Insert data ke tabel `pasien`
- Response JSON

#### `api/handle_kontak.php`
- Menangani form contact us
- Validasi email dan input
- Insert data ke tabel `kontak`
- Response JSON

## Testing API

### Test Form Pendaftaran
1. Buka: `http://localhost/rsudkabkerinci.go.id/pendaftaran.html`
2. Isi form dengan data lengkap
3. Klik "Daftar Sekarang"
4. Cek di phpMyAdmin untuk verifikasi data

### Test Form Contact
1. Buka: `http://localhost/rsudkabkerinci.go.id/index.html`
2. Scroll ke bagian "Contact Us"
3. Isi form dengan nama, email, dan pesan
4. Klik "Kirim"
5. Cek di phpMyAdmin untuk verifikasi data

## Troubleshooting

### Error: "Connection failed"
- Pastikan MySQL Server berjalan
- Periksa konfigurasi di `config/database.php`
- Username dan password harus sesuai dengan konfigurasi MySQL Anda

### Error: "Database already exists"
- Database sudah dibuat, setup script akan melewati pembuatan database
- Jika ingin reset, hapus database `rsud_kerinci` di phpMyAdmin

### Form tidak ter-submit
- Pastikan file `api/handle_pendaftaran.php` dan `api/handle_kontak.php` ada
- Periksa console browser untuk error message
- Pastikan folder `api` dan `config` sudah dibuat

## Keamanan

- Semua input di-sanitasi dengan `htmlspecialchars()` dan `filter_var()`
- Menggunakan prepared statements untuk mencegah SQL injection
- Data sensitif tidak dikirim via GET
- Password harus diganti setelah setup (opsional untuk development)

## Informasi Tambahan

Untuk pertanyaan atau masalah, hubungi:
- Email: rsudkabkerinci@gmail.com
- WhatsApp: 081371740005
- Telepon: (0741) 123456
