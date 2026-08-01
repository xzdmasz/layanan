# 🏛️ CLAUDE.md — Blueprint Proyek Web Layanan Desa Sungai Bakau Kecil

> **Dokumen ini adalah panduan utama pengembangan.**
> Setiap fitur harus mengacu ke dokumen ini agar tetap terarah dan konsisten.

---

## 📋 Ringkasan Proyek

| Item | Detail |
|---|---|
| **Nama Proyek** | Web Layanan Hukum & Kesehatan |
| **Target** | Warga Desa Sungai Bakau Kecil, Kab. Mempawah, Kalbar |
| **Tech Stack** | PHP Native + MySQL + Vanilla CSS + JavaScript |
| **Server Lokal** | Laragon (`http://localhost/desa_sungaibakaukecil/`) |
| **Repository** | [github.com/xzdmasz/layanan](https://github.com/xzdmasz/layanan) |

---

## 🔐 Admin Dashboard

### Akses Admin
| Item | Detail |
|---|---|
| **URL Login** | `http://localhost/desa_sungaibakaukecil/admin/login.php` |
| **Username** | `admin` |
| **Password** | `admin123` |
| **Session** | `$_SESSION['admin']` — terpisah dari session warga |

### Fitur Admin
| Halaman | URL | Fungsi |
|---|---|---|
| Login | `admin/login.php` | Login dengan username+password dari tabel `admins` |
| Dashboard | `admin/dashboard.php` | Ringkasan data (pengaduan masuk, warga terdaftar) |
| Kelola Banner | `admin/banner.php` | Upload/ganti 3 gambar slide beranda (JPG/PNG/WEBP, max 5MB) |
| Edit Statistik | `admin/statistik.php` | Edit angka Penduduk, KK, Keseluruhan Layanan |
| Logout | `admin/logout.php` | Hapus session admin |

### Cara Kerja Banner
- Gambar tersimpan di `assets/images/` dengan nama `slide{N}_{timestamp}.{ext}`
- Referensi path disimpan di tabel `banner_slides` (kolom `posisi` 1-3, `filename`)
- `index.php` membaca dari DB, fallback ke `bg3.png`, `bg1.jpg`, `bg2.png` jika DB kosong
- File lama (non-default) otomatis dihapus saat upload baru

### Cara Kerja Statistik
- Data statistik disimpan di tabel `statistik_desa`
- `admin/statistik.php` update via `INSERT ... ON DUPLICATE KEY UPDATE`
- `index.php` membaca 3 kunci: `penduduk`, `kk`, `layanan_bulan`

---

## 🗂️ Struktur Folder (Aktual)

```
desa_sungaibakaukecil/
├── assets/
│   ├── css/
│   │   ├── style.css              ← CSS utama (user-facing)
│   │   └── admin.css              ← [BARU] CSS khusus admin dashboard
│   ├── images/
│   │   ├── bg1.jpg, bg2.png, bg3.png
│   │   ├── kantordesa.png, logo.png
│   │   └── service-kesehatan.png, service-hukum.png
│   └── js/
│       ├── main.js                ← JS utama (user-facing)
│       └── admin.js               ← [BARU] JS khusus admin dashboard
│
├── includes/
│   ├── header.php                 ← Header + navbar (user)
│   ├── footer.php                 ← Footer (user)
│   ├── db.php                     ← [BARU] Koneksi database
│   ├── auth.php                   ← [BARU] Helper autentikasi (session check)
│   └── admin-sidebar.php          ← [BARU] Sidebar admin dashboard
│
├── admin/
│   ├── index.php                  ← [BARU] Dashboard utama admin
│   ├── login.php                  ← [BARU] Halaman login admin
│   ├── pengaduan-kesehatan.php    ← [BARU] Daftar pengaduan kesehatan
│   ├── pengaduan-hukum.php        ← [BARU] Daftar pengaduan hukum
│   ├── kelola-statistik.php       ← [BARU] Edit statistik beranda
│   └── logout.php                 ← [BARU] Proses logout admin
│
├── index.php                      ← Beranda (sudah ada)
├── layanan-pengaduan.php          ← Form pengaduan kesehatan (sudah ada)
├── layanan-hukum.php              ← Form layanan hukum (sudah ada)
├── login.php                      ← [BARU] Halaman login user
├── register.php                   ← [BARU] Halaman register user
├── logout.php                     ← [BARU] Proses logout user
├── setup.sql                      ← [BARU] SQL setup database
└── CLAUDE.md                      ← Dokumen ini
```

---

## 🗄️ Desain Database

### Database: `desa_sungaibakaukecil`

#### Tabel `users` — Data pengguna (warga)
| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | INT AUTO_INCREMENT PK | ID unik |
| `nama_lengkap` | VARCHAR(100) | Nama lengkap warga |
| `nik` | VARCHAR(16) UNIQUE | NIK (16 digit) |
| `no_hp` | VARCHAR(20) | Nomor HP / WhatsApp |
| `alamat` | TEXT | Alamat tinggal |
| `password` | VARCHAR(255) | Password (hashed bcrypt) |
| `created_at` | TIMESTAMP DEFAULT NOW() | Tanggal registrasi |

#### Tabel `admins` — Data admin desa
| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | INT AUTO_INCREMENT PK | ID unik |
| `username` | VARCHAR(50) UNIQUE | Username admin |
| `nama` | VARCHAR(100) | Nama admin |
| `password` | VARCHAR(255) | Password (hashed bcrypt) |
| `role` | ENUM('superadmin','admin') | Level akses |
| `created_at` | TIMESTAMP DEFAULT NOW() | Tanggal dibuat |

#### Tabel `pengaduan_kesehatan` — Pengaduan penyakit dari warga
| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | INT AUTO_INCREMENT PK | ID unik |
| `user_id` | INT FK → users.id | Pelapor |
| `nama_lengkap` | VARCHAR(100) | Nama pelapor |
| `nik` | VARCHAR(16) | NIK pelapor |
| `no_hp` | VARCHAR(20) | No HP / WhatsApp |
| `kategori` | VARCHAR(100) | Kategori pengaduan (Penyakit Menular, dll) |
| `alamat_kejadian` | TEXT | Lokasi kejadian |
| `detail_gejala` | TEXT | Deskripsi gejala / masalah |
| `status` | ENUM('masuk','proses','selesai') DEFAULT 'masuk' | Status tindak lanjut |
| `catatan_admin` | TEXT NULL | Catatan dari admin |
| `created_at` | TIMESTAMP DEFAULT NOW() | Tanggal laporan |
| `updated_at` | TIMESTAMP ON UPDATE NOW() | Terakhir diupdate |

#### Tabel `pengaduan_hukum` — Pengaduan hukum dari warga
| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | INT AUTO_INCREMENT PK | ID unik |
| `user_id` | INT FK → users.id | Pelapor |
| `nama_pemohon` | VARCHAR(100) | Nama pemohon |
| `nik_pemohon` | VARCHAR(16) | NIK pemohon |
| `no_telp` | VARCHAR(20) | No HP / WhatsApp |
| `jenis_masalah` | VARCHAR(100) | Jenis permasalahan hukum |
| `ringkasan` | TEXT | Ringkasan masalah |
| `status` | ENUM('masuk','proses','selesai') DEFAULT 'masuk' | Status tindak lanjut |
| `catatan_admin` | TEXT NULL | Catatan dari admin |
| `created_at` | TIMESTAMP DEFAULT NOW() | Tanggal laporan |
| `updated_at` | TIMESTAMP ON UPDATE NOW() | Terakhir diupdate |

#### Tabel `statistik_desa` — Statistik yang tampil di beranda
| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | INT AUTO_INCREMENT PK | ID unik |
| `kunci` | VARCHAR(50) UNIQUE | Key identifier (penduduk, kk, layanan_bulan, kepuasan) |
| `label` | VARCHAR(100) | Label tampil (Penduduk, Kepala Keluarga, dll) |
| `nilai` | VARCHAR(20) | Nilai tampil (3.847, 98%, dll) |
| `target_angka` | INT | Angka target untuk animasi counter |
| `updated_at` | TIMESTAMP ON UPDATE NOW() | Terakhir diupdate |

---

## 🧩 Fase Pengembangan

### ══════════════════════════════════════════
### FASE 1: Database & Autentikasi
### ══════════════════════════════════════════

**Tujuan:** Setup database, sistem login/register user, dan proteksi halaman form.

#### Task List:
- [ ] Buat file `setup.sql` — semua tabel + data awal admin & statistik
- [ ] Buat file `includes/db.php` — koneksi PDO ke MySQL
- [ ] Buat file `includes/auth.php` — helper cek session login
- [ ] Buat halaman `login.php` — form login user (NIK + password)
- [ ] Buat halaman `register.php` — form registrasi user
- [ ] Buat file `logout.php` — proses logout
- [ ] Update `layanan-pengaduan.php` — cek login sebelum tampil form, redirect ke login jika belum
- [ ] Update `layanan-hukum.php` — sama, cek login dulu
- [ ] Update `includes/header.php` — tampilkan nama user + tombol logout jika sudah login, atau tombol Login jika belum
- [ ] Update `index.php` — ambil statistik dari tabel `statistik_desa` (bukan hardcode)

#### Aturan Login User:
- Login menggunakan **NIK + Password**
- Setelah login, session menyimpan: `user_id`, `nama_lengkap`, `nik`, `no_hp`
- Jika belum login dan klik "Lihat Layanan", diarahkan ke `login.php` dengan `?redirect=layanan-pengaduan.php`
- Setelah login berhasil, redirect kembali ke halaman layanan tujuan

---

### ══════════════════════════════════════════
### FASE 2: Form Pengaduan (User) → Simpan ke Database
### ══════════════════════════════════════════

**Tujuan:** Form pengaduan yang sudah ada dihubungkan ke database agar data tersimpan.

#### Task List:
- [ ] Update `layanan-pengaduan.php`:
  - Auto-fill Nama, NIK, No HP dari session user yang login
  - Field yang user isi: Kategori, Alamat Kejadian, Detail Gejala
  - Submit → INSERT ke tabel `pengaduan_kesehatan`
  - Tampilkan pesan sukses setelah submit
- [ ] Update `layanan-hukum.php`:
  - Auto-fill Nama, NIK, No Telp dari session user yang login
  - Field yang user isi: Jenis Permasalahan, Ringkasan
  - Submit → INSERT ke tabel `pengaduan_hukum`
  - Tampilkan pesan sukses setelah submit

#### Aturan Validasi:
- Server-side validation wajib (PHP)
- Semua input di-sanitize (`htmlspecialchars`, `trim`)
- NIK harus 16 digit angka
- Semua field wajib diisi

---

### ══════════════════════════════════════════
### FASE 3: Admin Login & Dashboard
### ══════════════════════════════════════════

**Tujuan:** Halaman admin terpisah dengan dashboard statistik dan sidebar navigasi.

#### Task List:
- [ ] Buat `admin/login.php` — form login admin (username + password)
- [ ] Buat `admin/logout.php` — proses logout admin
- [ ] Buat `includes/admin-sidebar.php` — komponen sidebar admin
- [ ] Buat `assets/css/admin.css` — styling khusus admin
- [ ] Buat `assets/js/admin.js` — JS khusus admin
- [ ] Buat `admin/index.php` — Dashboard utama

#### Desain Dashboard Admin (`admin/index.php`):

```
┌─────────────────────────────────────────────────────────────┐
│  SIDEBAR (kiri)          │  KONTEN UTAMA (kanan)            │
│                          │                                   │
│  🏠 Dashboard            │  ┌─────────┐ ┌─────────┐ ┌─────────┐
│                          │  │TOTAL     │ │KESEHATAN│ │HUKUM    │
│  📋 Pengaduan Kesehatan  │  │ 45       │ │ 28      │ │ 17      │
│  ⚖️  Pengaduan Hukum      │  │Pengaduan │ │Pengaduan│ │Pengaduan│
│                          │  └─────────┘ └─────────┘ └─────────┘
│  📊 Kelola Statistik     │                                   │
│                          │  ┌─────────┐ ┌─────────┐ ┌─────────┐
│  🚪 Logout               │  │ MASUK   │ │ PROSES  │ │ SELESAI │
│                          │  │  12     │ │  20     │ │  13     │
│                          │  └─────────┘ └─────────┘ └─────────┘
│                          │                                   │
│                          │  📈 Grafik / Tabel Terbaru        │
│                          │  ┌───────────────────────────────┐│
│                          │  │ 5 Pengaduan terbaru (tabel)   ││
│                          │  └───────────────────────────────┘│
└─────────────────────────────────────────────────────────────┘
```

#### Statistik Dashboard (3 kategori):
1. **Keseluruhan** — Total semua pengaduan (kesehatan + hukum digabung)
2. **Kesehatan** — Total pengaduan dari tabel `pengaduan_kesehatan`
3. **Hukum** — Total pengaduan dari tabel `pengaduan_hukum`

#### Status breakdown per kategori:
- 🟡 **Masuk** — Baru masuk, belum ditindaklanjuti
- 🔵 **Proses** — Sedang ditindaklanjuti
- 🟢 **Selesai** — Sudah selesai ditangani

---

### ══════════════════════════════════════════
### FASE 4: Kelola Pengaduan (Admin)
### ══════════════════════════════════════════

**Tujuan:** Admin bisa lihat, filter, ubah status, dan tindak lanjut pengaduan via WhatsApp.

#### Task List:
- [ ] Buat `admin/pengaduan-kesehatan.php`:
  - Tabel daftar semua pengaduan kesehatan
  - Filter berdasarkan status (Semua / Masuk / Proses / Selesai)
  - Setiap baris ada tombol:
    - 📋 **Detail** — popup/modal detail lengkap pengaduan
    - ✏️ **Ubah Status** — dropdown ubah status (masuk → proses → selesai)
    - 💬 **WhatsApp** — buka link `wa.me/{no_hp}?text={pesan_template}` untuk tindak lanjut ke user
  - Catatan admin bisa ditambahkan per pengaduan
- [ ] Buat `admin/pengaduan-hukum.php`:
  - Sama seperti pengaduan kesehatan, tapi data dari tabel `pengaduan_hukum`

#### Template WhatsApp:
```
Assalamualaikum {nama},

Terima kasih atas laporan pengaduan Anda di Pusat Layanan Desa Sungai Bakau Kecil.

📋 Nomor Laporan: #{id}
📌 Status: {status}

Kami akan segera menindaklanjuti laporan Anda.

Salam,
Tim Layanan Desa Sungai Bakau Kecil
```

---

### ══════════════════════════════════════════
### FASE 5: Kelola Statistik Beranda (Admin)
### ══════════════════════════════════════════

**Tujuan:** Admin bisa mengubah 4 angka statistik yang tampil di halaman beranda.

#### Task List:
- [ ] Buat `admin/kelola-statistik.php`:
  - Form edit 4 statistik beranda:
    - Penduduk (3.847)
    - Kepala Keluarga (1.124)
    - Layanan Bulan Ini (247)
    - Kepuasan Warga (98%)
  - Submit → UPDATE tabel `statistik_desa`
- [ ] Update `index.php` — baca statistik dari database, bukan hardcode array PHP

---

## 🎨 Aturan Desain

### User-Facing (Publik)
- **Desain yang sudah ada JANGAN diubah** kecuali diminta
- Warna utama: hitam (#111), putih (#fff), abu (#fafafa)
- Font: Playfair Display (heading) + Inter (body)
- Card layanan: Grayscale → color on hover
- Responsive mobile (≤768px)

### Admin Dashboard
- **Terpisah total** dari tampilan user — pakai `admin.css` sendiri
- Sidebar gelap (dark sidebar) + konten area terang
- Status badge warna:
  - 🟡 Masuk = `#f59e0b` (amber)
  - 🔵 Proses = `#3b82f6` (blue)
  - 🟢 Selesai = `#10b981` (emerald)
- Tabel data rapi dengan pagination sederhana
- Responsif — sidebar collapse di mobile

---

## ⚠️ Aturan Penting

1. **Jangan ubah tampilan user yang sudah ada** tanpa permintaan eksplisit
2. **Semua password** harus di-hash dengan `password_hash()` (bcrypt)
3. **Semua query** harus pakai **Prepared Statement** (PDO) — anti SQL injection
4. **Semua output** harus di-escape dengan `htmlspecialchars()` — anti XSS
5. **Session** harus dimulai di setiap file yang butuh autentikasi
6. **Admin dan User** punya session terpisah (`$_SESSION['user']` vs `$_SESSION['admin']`)
7. **File admin/** harus selalu cek session admin di awal — redirect ke `admin/login.php` jika belum login
8. **Statistik beranda** harus dibaca dari database, bukan hardcode

---

## 🔐 Data Awal (Seed)

### Admin Default:
| Username | Password | Role |
|---|---|---|
| `admin` | `admin123` | superadmin |

### Statistik Default:
| Kunci | Label | Nilai | Target |
|---|---|---|---|
| `penduduk` | Penduduk | 3.847 | 3847 |
| `kk` | Kepala Keluarga | 1.124 | 1124 |
| `layanan_bulan` | Layanan Bulan Ini | 247 | 247 |
| `kepuasan` | Kepuasan Warga | 98% | 98 |

---

## 📌 Status Saat Ini

### ✅ Sudah Selesai:
- [x] Halaman Beranda (`index.php`) — hero slider, stats, layanan cards, tentang desa, CTA
- [x] Halaman Layanan Kesehatan (`layanan-pengaduan.php`) — form + accordion sidebar
- [x] Halaman Layanan Hukum (`layanan-hukum.php`) — form + accordion sidebar
- [x] Header & Navbar responsif (`includes/header.php`)
- [x] Footer (`includes/footer.php`)
- [x] CSS utama (`assets/css/style.css`)
- [x] JavaScript utama (`assets/js/main.js`)
- [x] Back-to-top button
- [x] Mobile menu + dropdown layanan

### 🔲 Belum Dikerjakan:
- [ ] **FASE 1** — Database & Autentikasi user
- [ ] **FASE 2** — Form pengaduan → simpan ke database
- [ ] **FASE 3** — Admin login & dashboard
- [ ] **FASE 4** — Kelola pengaduan (admin)
- [ ] **FASE 5** — Kelola statistik beranda (admin)

---

## 🚀 Urutan Pengerjaan

```
FASE 1 → FASE 2 → FASE 3 → FASE 4 → FASE 5
  │         │         │         │         │
  ▼         ▼         ▼         ▼         ▼
Database  Form →DB  Admin UI  Kelola    Edit
& Login   Connect   Dashboard Pengaduan Stats
```

> **Selalu mulai dari FASE yang belum selesai.**
> **Jangan loncat fase kecuali diminta.**
