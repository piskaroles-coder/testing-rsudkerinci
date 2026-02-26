# 🚀 QUICK START GUIDE - Setup Database RSUD Kabupaten Kerinci

## ✅ Langkah-Langkah Cepat Setup

### Step 1: Pastikan XAMPP Berjalan
- Buka XAMPP Control Panel
- Start Apache dan MySQL
- Pastikan kedua berwarna hijau ✓

### Step 2: Setup Database (Pilih Salah Satu)

#### OPSI A: Automatic Setup (Rekomendasi)
1. Buka browser
2. Kunjungi: `http://localhost/rsudkabkerinci.go.id/config/setup_database.php`
3. Tunggu hingga muncul pesan "Database setup selesai!"
4. **HAPUS** file `config/setup_database.php` setelah setup
5. ✓ Selesai!

#### OPSI B: Manual Setup via phpMyAdmin
1. Buka: `http://localhost/phpmyadmin`
2. Login dengan username: `root` (password kosong)
3. Klik tab "Import"
4. Pilih file: `config/database.sql`
5. Klik "Go"
6. ✓ Database berhasil dibuat

#### OPSI C: MySQL Command Line
```bash
mysql -u root < "C:\xampp\htdocs\rsudkabkerinci.go.id\config\database.sql"
```

### Step 3: Verifikasi Setup

1. **Cek Database di phpMyAdmin**
   - Akses: `http://localhost/phpmyadmin`
   - Lihat database `rsud_kerinci` di sebelah kiri
   - Klik database untuk lihat tabel-tabelnya
   - Pastikan ada 5 tabel: pasien, kontak, jadwal_dokter, berita, pengumuman

2. **Test Website**
   - Buka: `http://localhost/rsudkabkerinci.go.id`
   - Coba isi form pendaftaran
   - Data harus masuk ke database

3. **Lihat Data di Admin Panel**
   - Buka: `http://localhost/rsudkabkerinci.go.id/admin_data.php`
   - Pilih tabel untuk lihat data yang sudah diinput

### Step 4: Selesai! 🎉

Website sekarang fully functional dengan database. Anda dapat:
- Menerima pendaftaran pasien
- Menerima pesan contact
- Melihat data di admin panel
- Manage data di phpMyAdmin

---

## 📚 File-File Penting

| File | Fungsi |
|------|--------|
| `config/database.php` | Koneksi database (jangan dihapus) |
| `config/setup_database.php` | Setup script (HAPUS setelah setup) |
| `config/database.sql` | Backup SQL |
| `api/handle_pendaftaran.php` | Proses form pendaftaran |
| `api/handle_kontak.php` | Proses form contact |
| `admin_data.php` | Panel lihat data |

---

## 🔧 Troubleshooting

### Error: "Connection failed"
```
✗ Solusi:
1. Pastikan MySQL berjalan (XAMPP Control Panel)
2. Cek config/database.php - pastikan sesuai setting MySQL Anda
3. Username: root, Password: (kosong) - sesuaikan jika berbeda
```

### Error: "Database already exists"
```
✗ Solusi:
Setup script mendeteksi database sudah ada, tidak apa-apa.
Database dan tabel akan tetap valid.
Jalankan setup script lagi jika ingin fresh start.
```

### Form tidak submit / error 404
```
✗ Solusi:
1. Pastikan folder 'api' ada dan berisi file PHP
2. Pastikan folder 'config' ada dengan file database.php
3. Cek path di form action (harus sesuai lokasi file)
4. Periksa console browser (F12 > Console) untuk error
```

### Tidak bisa akses admin_data.php
```
✗ Solusi:
1. Pastikan PHP berjalan (XAMPP Apache hidup)
2. Database sudah berhasil dibuat
3. Refresh page atau clear browser cache (Ctrl+Shift+R)
```

---

## 🔒 Keamanan

Sebelum deploy ke production:

- [ ] Ubah password MySQL (bukan default)
- [ ] Update `config/database.php` dengan password baru
- [ ] Hapus file `config/setup_database.php`
- [ ] Hapus file `admin_data.php` (atau proteksi dengan password)
- [ ] Setup HTTPS/SSL certificate
- [ ] Backup database secara berkala
- [ ] Set file permissions yang tepat (755 untuk folder, 644 untuk file)

---

## 📊 Database Schema

### Table: pasien
```
- id (PK)
- nama, noktp (UNIQUE), tgllahir, jeniskelamin
- goldarah, agama, alamat, kota, kodepos
- telepon, email
- penyakit, alergi
- namapj, hubungan, teleponpj, alamatpj
- asuransi, nomorasuransi
- created_at, updated_at, status
```

### Table: kontak
```
- id (PK)
- nama, email, pesan
- created_at
- status (baru/dibaca/ditanggapi)
```

### Table: jadwal_dokter
```
- id (PK)
- nama_dokter, spesialisasi, hari
- jam_mulai, jam_selesai
- lokasi
- created_at, updated_at
```

### Table: berita
```
- id (PK)
- judul, konten, gambar
- tanggal_publikasi, penulis
- created_at, updated_at
- status (publikasi/draft)
```

### Table: pengumuman
```
- id (PK)
- judul, isi
- tanggal_publikasi, penulis
- created_at, updated_at
- status (aktif/arsip)
```

---

## 📞 Support

Jika ada masalah atau pertanyaan:
- Email: rsudkabkerinci@gmail.com
- WhatsApp: 081371740005
- Telepon: (0741) 123456

---

✅ Setup Complete! Happy coding! 🎉
