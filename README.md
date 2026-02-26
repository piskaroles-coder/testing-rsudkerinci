# RSUD Kabupaten Kerinci - Website Documentation

## 📋 Struktur Proyek

```
rsudkabkerinci.go.id/
├── index.html                    # Halaman utama
├── pendaftaran.html              # Halaman pendaftaran pasien
├── sejarah.html                  # Halaman sejarah singkat
├── visi-misi.html                # Halaman visi dan misi
├── struktur-organisasi.html      # Halaman struktur organisasi
├── style.css                     # Stylesheet utama
├── script.js                     # JavaScript utama
│
├── api/                          # Folder API handlers
│   ├── handle_pendaftaran.php    # Handler form pendaftaran
│   └── handle_kontak.php         # Handler form contact
│
├── config/                       # Folder konfigurasi
│   ├── database.php              # Koneksi database
│   ├── setup_database.php        # Setup script (hapus setelah setup)
│   └── database.sql              # SQL backup
│
├── logo/                         # Folder logo
│   └── Logo RSUD Kerkinci.png    # Logo RSUD
│
├── DATABASE_SETUP.md             # Panduan setup database
└── README.md                     # File ini
```

## 🚀 Fitur Website

### 1. Halaman Utama (index.html)
- Header dengan logo dan informasi RSUD
- Navbar dengan menu dropdown
- Waktu real-time di sudut kanan atas
- Section Berita Terbaru
- Galeri Kegiatan Terbaru
- Profil RSUD (Visi & Misi singkat)
- Layanan Kami
- Daftar Dokter dan Jadwal
- Informasi Publik
- Galeri
- Contact Us dengan peta
- Footer

### 2. Halaman Pendaftaran (pendaftaran.html)
- Form pendaftaran lengkap dengan validasi
- Input: Data pribadi, kontak, riwayat kesehatan, data penanggung jawab, asuransi
- Integrasi dengan database MySQL
- Notifikasi submit status
- Info cards tentang keamanan data

### 3. Halaman Sejarah (sejarah.html)
- Sejarah pendirian RSUD
- Timeline milestone perkembangan
- Gambar bangunan RSUD

### 4. Halaman Visi Misi (visi-misi.html)
- Visi dan misi RSUD
- 4 poin misi dengan penjelasan
- Nilai-nilai inti (Kepedulian, Integritas, Inovasi, Kolaborasi)

### 5. Halaman Struktur Organisasi (struktur-organisasi.html)
- Diagram organisasi 3 level
- Daftar departemen dan divisi
- Informasi kontak pimpinan

## 🗄️ Database

### Tabel yang Dibuat:
1. **pasien** - Data pendaftaran pasien
2. **kontak** - Pesan dari contact form
3. **jadwal_dokter** - Jadwal praktik dokter
4. **berita** - Berita dan artikel
5. **pengumuman** - Pengumuman penting

Lihat `DATABASE_SETUP.md` untuk detail lebih lanjut.

## 🔧 Cara Instalasi

### 1. Persiapan
- Pastikan XAMPP sudah terinstall dan berjalan
- MySQL Server aktif
- PHP 7.4 atau lebih tinggi

### 2. Setup Database
- Pilih salah satu metode di `DATABASE_SETUP.md`:
  - Metode 1: Akses `http://localhost/rsudkabkerinci.go.id/config/setup_database.php`
  - Metode 2: Import via phpMyAdmin
  - Metode 3: Command line MySQL

### 3. Mulai Menggunakan
- Buka `http://localhost/rsudkabkerinci.go.id`
- Website siap digunakan

## 📝 Teknologi yang Digunakan

### Frontend:
- HTML5
- CSS3 (dengan Bootstrap 5.3.0)
- JavaScript (Vanilla)
- Font Awesome Icons 6.0.0

### Backend:
- PHP 7.4+
- MySQL/MariaDB
- jQuery (via Bootstrap Bundle)

### Tools:
- VS Code
- XAMPP
- phpMyAdmin

## 🎨 Desain

### Warna Tema:
- Primary: #0047AB (Biru Gelap)
- Secondary: #4A90E2 (Biru Muda)
- Gradient: #003D99 ke #0047AB

### Fitur Desain:
- Responsive Design (Mobile-First)
- Smooth Animations
- Hover Effects
- Gradient Backgrounds
- Modern Card Layouts

## 📊 API Endpoints

### POST /api/handle_pendaftaran.php
Menerima form pendaftaran pasien
- Input: Form data dari pendaftaran.html
- Output: JSON response
- Validasi: NoKTP unik, email valid

### POST /api/handle_kontak.php
Menerima pesan contact form
- Input: Form data dari index.html
- Output: JSON response
- Validasi: Semua field diperlukan

## 🔒 Keamanan

- Input sanitization dengan `htmlspecialchars()`
- Email validation dengan `filter_var()`
- Prepared statements untuk SQL queries
- CORS-safe responses
- UTF-8 encoding untuk semua data

## 📱 Responsif

Website fully responsive untuk:
- Desktop (1024px+)
- Tablet (768px - 1023px)
- Mobile (< 768px)

## 📞 Kontak

- WhatsApp: 081371740005
- Email: rsudkabkerinci@gmail.com
- Telepon: (0741) 123456
- Alamat: Jln. Jalur 2 Bukit Tengah, Desa Sungai Pegeh, Siulak, Kerinci - Jambi 37162

## 📄 Lisensi

© 2023 RSUD Kabupaten Kerinci. Semua Hak Dilindungi.

## ✅ Checklist Maintenance

- [ ] Update content di halaman berita
- [ ] Update jadwal dokter di database
- [ ] Check form submissions di database
- [ ] Backup database secara berkala
- [ ] Update informasi kontak jika ada perubahan
- [ ] Monitor uptime website
- [ ] Update SSL certificate (jika menggunakan HTTPS)

## 🐛 Issues & Support

Untuk melaporkan bug atau meminta fitur tambahan:
1. Hubungi pihak IT RSUD
2. Sertakan screenshot error
3. Jelaskan langkah reproduksi

---

Terakhir diupdate: 29 Januari 2026
