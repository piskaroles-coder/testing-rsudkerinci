# Input Form & Database Setup untuk Berita dan Pengumuman

## 📋 Deskripsi
Sistem form input untuk menambah dan mengedit berita (news) dan pengumuman (announcements) di RSUD Kabupaten Kerinci.

## 📁 File yang Dibuat

### 1. **input_berita.php** - Form Input Berita
Lokasi: `/input_berita.php`

**Fitur:**
- Form input berita dengan WYSIWYG editor (Summernote)
- Upload gambar otomatis
- Mode create dan edit
- Validasi form client-side dan server-side
- Status: Publikasi/Draft

**Field:**
- Judul Berita (required)
- Konten Berita (required) - dengan rich text editor
- Gambar (optional) - max 2MB (JPG, PNG, GIF)
- Tanggal Publikasi (required)
- Penulis (optional)
- Status (required) - Publikasi atau Draft

**Cara Akses:**
```
Admin Panel → Berita → Tambah Berita Baru
```

---

### 2. **input_pengumuman.php** - Form Input Pengumuman
Lokasi: `/input_pengumuman.php`

**Fitur:**
- Form input pengumuman dengan WYSIWYG editor
- Mode create dan edit
- Validasi form lengkap
- Status: Aktif/Arsip

**Field:**
- Judul Pengumuman (required)
- Isi Pengumuman (required) - dengan rich text editor
- Tanggal Publikasi (required)
- Penulis (optional)
- Status (required) - Aktif atau Arsip

**Cara Akses:**
```
Admin Panel → Pengumuman → Tambah Pengumuman Baru
```

---

### 3. **api/handle_berita.php** - Handler API Berita
Lokasi: `/api/handle_berita.php`

**Fungsi:**
- Memproses form submission untuk create/update berita
- Upload dan validasi gambar
- Menyimpan data ke database
- Return JSON response

**Validasi:**
- Judul tidak boleh kosong
- Konten tidak boleh kosong
- Tanggal publikasi harus diisi
- Status harus valid (publikasi/draft)
- Gambar: maksimal 2MB, format JPG/PNG/GIF

---

### 4. **api/handle_pengumuman.php** - Handler API Pengumuman
Lokasi: `/api/handle_pengumuman.php`

**Fungsi:**
- Memproses form submission untuk create/update pengumuman
- Validasi data
- Menyimpan ke database
- Return JSON response

**Validasi:**
- Judul tidak boleh kosong
- Isi tidak boleh kosong
- Tanggal publikasi harus diisi
- Status harus valid (aktif/arsip)

---

## 🗄️ Database Tables

### Tabel `berita`
```sql
CREATE TABLE IF NOT EXISTS berita (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    konten TEXT NOT NULL,
    gambar VARCHAR(255),
    tanggal_publikasi DATETIME NOT NULL,
    penulis VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('publikasi', 'draft') DEFAULT 'draft'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Indexes
CREATE INDEX idx_status_berita ON berita(status);
CREATE INDEX idx_tanggal_berita ON berita(tanggal_publikasi);
```

**Field Penjelasan:**
| Field | Type | Deskripsi |
|-------|------|-----------|
| id | INT | Primary key, auto increment |
| judul | VARCHAR(255) | Judul berita |
| konten | TEXT | Isi konten berita |
| gambar | VARCHAR(255) | Path file gambar |
| tanggal_publikasi | DATETIME | Tanggal publikasi berita |
| penulis | VARCHAR(100) | Nama penulis |
| created_at | TIMESTAMP | Waktu dibuat otomatis |
| updated_at | TIMESTAMP | Waktu diubah terakhir |
| status | ENUM | Status: publikasi atau draft |

---

### Tabel `pengumuman`
```sql
CREATE TABLE IF NOT EXISTS pengumuman (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    isi TEXT NOT NULL,
    tanggal_publikasi DATETIME NOT NULL,
    penulis VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('aktif', 'arsip') DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Indexes
CREATE INDEX idx_status_pengumuman ON pengumuman(status);
CREATE INDEX idx_tanggal_pengumuman ON pengumuman(tanggal_publikasi);
```

**Field Penjelasan:**
| Field | Type | Deskripsi |
|-------|------|-----------|
| id | INT | Primary key, auto increment |
| judul | VARCHAR(255) | Judul pengumuman |
| isi | TEXT | Isi pengumuman |
| tanggal_publikasi | DATETIME | Tanggal publikasi |
| penulis | VARCHAR(100) | Nama penulis |
| created_at | TIMESTAMP | Waktu dibuat otomatis |
| updated_at | TIMESTAMP | Waktu diubah terakhir |
| status | ENUM | Status: aktif atau arsip |

---

## 📂 Folder Struktur
```
rsudkabkerinci.go.id/
├── input_berita.php              ← Form input berita
├── input_pengumuman.php          ← Form input pengumuman
├── api/
│   ├── handle_berita.php         ← Handler berita
│   └── handle_pengumuman.php     ← Handler pengumuman
├── uploads/
│   └── berita/                   ← Folder penyimpanan gambar berita (auto-created)
├── config/
│   └── database.sql              ← Update dengan indexes baru
```

---

## 🚀 Cara Menggunakan

### 1. Setup Database
Jalankan query SQL di phpMyAdmin:
```bash
# Sudah otomatis ada di database.sql
# Atau jalankan query manual untuk indexes:
CREATE INDEX idx_status_berita ON berita(status);
CREATE INDEX idx_tanggal_berita ON berita(tanggal_publikasi);
CREATE INDEX idx_status_pengumuman ON pengumuman(status);
CREATE INDEX idx_tanggal_pengumuman ON pengumuman(tanggal_publikasi);
```

### 2. Akses Form Input
- **Berita**: `http://localhost/rsudkabkerinci.go.id/input_berita.php`
- **Pengumuman**: `http://localhost/rsudkabkerinci.go.id/input_pengumuman.php`

### 3. Login Required
Kedua form memerlukan login admin terlebih dahulu. Jika belum login akan diarahkan ke `login.html`.

### 4. Form Input
- Isi semua field yang diperlukan
- Gunakan editor rich text untuk formatting konten
- Upload gambar jika perlu (hanya untuk berita)
- Pilih status (publikasi/draft untuk berita, aktif/arsip untuk pengumuman)
- Klik tombol Simpan/Perbarui

### 5. Lihat Data
Admin panel dapat melihat semua berita dan pengumuman di:
- `admin_data.php?table=berita`
- `admin_data.php?table=pengumuman`

---

## 🔒 Security Features

1. **Session Check**: Kedua form dan API mengecek session login
2. **Input Sanitization**: Semua input di-sanitize menggunakan `htmlspecialchars()`
3. **Prepared Statements**: Query menggunakan prepared statements untuk prevent SQL injection
4. **File Validation**: Upload file divalidasi tipe dan ukuran
5. **MIME Type Check**: Hanya gambar yang diizinkan untuk upload

---

## 📝 Sample Data
Sudah tersedia sample data di database.sql:
```sql
INSERT INTO berita (judul, konten, tanggal_publikasi, penulis, status) VALUES
('Program Vaksinasi Gratis', 'RSUD mengadakan program vaksinasi gratis...', NOW(), 'Admin', 'publikasi'),
('Peluncuran Layanan Konsultasi Online', 'RSUD meluncurkan layanan konsultasi dokter online...', NOW(), 'Admin', 'publikasi');
```

---

## 🛠️ Teknologi yang Digunakan

1. **PHP** - Server-side processing
2. **Bootstrap 5** - UI Framework
3. **jQuery** - DOM manipulation
4. **Summernote** - Rich text editor
5. **MySQL** - Database
6. **Font Awesome** - Icons

---

## 📧 Support

Untuk pertanyaan atau masalah, hubungi admin sistem RSUD Kabupaten Kerinci.

---

**Status**: ✅ Siap digunakan
**Update Terakhir**: Januari 2026
