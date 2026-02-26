📰 FORM INPUT BERITA & PENGUMUMAN - QUICK START GUIDE

═══════════════════════════════════════════════════════════════

✅ APA YANG SUDAH DIBUAT:

1. Form Input Berita (input_berita.php)
   - Tambah/Edit berita dengan rich text editor
   - Upload gambar otomatis
   - Pilih status: Publikasi atau Draft

2. Form Input Pengumuman (input_pengumuman.php)
   - Tambah/Edit pengumuman dengan rich text editor
   - Pilih status: Aktif atau Arsip

3. API Handlers
   - api/handle_berita.php - Simpan berita
   - api/handle_pengumuman.php - Simpan pengumuman

4. Database Updates
   - Tabel berita & pengumuman sudah ada
   - Menambah indexes untuk performa
   - Folder uploads/berita/ untuk gambar

═══════════════════════════════════════════════════════════════

🚀 CARA MULAI:

1. Login Admin
   → http://localhost/rsudkabkerinci.go.id/
   → Username & Password sesuai setting

2. Dari Dashboard:
   → Klik "Input Berita" untuk tambah berita
   → Klik "Input Pengumuman" untuk tambah pengumuman

3. Atau Akses Langsung:
   → Berita: http://localhost/rsudkabkerinci.go.id/input_berita.php
   → Pengumuman: http://localhost/rsudkabkerinci.go.id/input_pengumuman.php

═══════════════════════════════════════════════════════════════

📋 FIELD FORM BERITA:

✓ Judul Berita (wajib)
✓ Konten Berita (wajib) - pake editor untuk formatting
✓ Gambar (opsional) - max 2MB, JPG/PNG/GIF
✓ Tanggal Publikasi (wajib)
✓ Penulis (opsional)
✓ Status (wajib) - Publikasi atau Draft

═══════════════════════════════════════════════════════════════

📋 FIELD FORM PENGUMUMAN:

✓ Judul Pengumuman (wajib)
✓ Isi Pengumuman (wajib) - pake editor untuk formatting
✓ Tanggal Publikasi (wajib)
✓ Penulis (opsional)
✓ Status (wajib) - Aktif atau Arsip

═══════════════════════════════════════════════════════════════

🔍 LIHAT & EDIT DATA:

1. Buka Admin Panel → Kelola Data
   → http://localhost/rsudkabkerinci.go.id/admin_data.php

2. Pilih Tab "Berita" atau "Pengumuman"

3. Klik ID untuk edit data

═══════════════════════════════════════════════════════════════

📂 FILE YANG DIBUAT:

✅ input_berita.php
✅ input_pengumuman.php
✅ api/handle_berita.php
✅ api/handle_pengumuman.php
✅ config/database_berita_pengumuman.sql
✅ FORM_BERITA_PENGUMUMAN.md (dokumentasi lengkap)
✅ FORM_BERITA_PENGUMUMAN_SUMMARY.md (ringkasan)

═══════════════════════════════════════════════════════════════

⚡ FITUR:

✓ Rich Text Editor (Summernote) untuk formatting
✓ Upload gambar otomatis (berita)
✓ Mode Create & Edit
✓ Validasi form lengkap
✓ Database indexes untuk performa
✓ Security: session check, sanitize, prepared statements
✓ Responsive design (mobile-friendly)
✓ Integration dengan admin panel

═══════════════════════════════════════════════════════════════

💾 DATABASE:

Tabel BERITA:
- id, judul, konten, gambar, tanggal_publikasi, penulis
- created_at, updated_at, status

Tabel PENGUMUMAN:
- id, judul, isi, tanggal_publikasi, penulis
- created_at, updated_at, status

Indexes:
- idx_status_berita, idx_tanggal_berita
- idx_status_pengumuman, idx_tanggal_pengumuman

═══════════════════════════════════════════════════════════════

🔐 SECURITY:

✓ Admin login wajib
✓ Input sanitization
✓ Prepared statements (prevent SQL injection)
✓ File validation
✓ MIME type checking

═══════════════════════════════════════════════════════════════

📚 DOKUMENTASI LENGKAP:

Baca: FORM_BERITA_PENGUMUMAN.md

═══════════════════════════════════════════════════════════════

✨ SELESAI! SIAP DIGUNAKAN ✨

Untuk pertanyaan atau bantuan, hubungi admin RSUD.

═══════════════════════════════════════════════════════════════
