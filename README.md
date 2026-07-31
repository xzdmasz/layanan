# 🏛️ Sistem Pusat Layanan & Pengaduan Warga Desa Sungai Bakau Kecil

Platform layanan digital desa berbasis web modern yang dirancang untuk memudahkan warga **Desa Sungai Bakau Kecil, Kabupaten Mempawah** dalam melakukan pengaduan penyakit/kesehatan lingkungan dan permohonan konsul/layanan hukum secara online, praktis, dan transparan.

---

## ✨ Fitur Utama

### 📱 Navigation & Responsive Design System
- **Desktop Navbar & Profile Pill**: Menampilkan menu navigasi presisi dengan status login profil pengguna `[ 👤 Nama Depan ▾ ]` dalam bentuk kapsul (*pill shape*).
- **Mobile Bottom Navigation Bar**: Navigasi ergonomis khusus perangkat HP (≤768px) dengan 3 tab utama:
  - 🏠 **Beranda**: Halaman utama informasi & galeri desa.
  - 🟢 **Layanan Floating Speed Dial**: Tombol lingkar mengambang di tengah yang dapat membuka *speed dial bubbles* animasi melayang untuk memilih antara **Layanan Kesehatan** & **Layanan Hukum**.
  - 📋 **Riwayat**: Akses cepat lacak status pengaduan warga.

### 🏥 Layanan & Pengaduan Kesehatan
- Form pengaduan penyakit / masalah kesehatan lingkungan warga desa.
- Auto-fill otomatis data pengguna (Nama & Nomor HP) saat warga telah login.
- Lampiran foto pendukung bukti kejadian/penyakit.
- Sistem pelacakan status laporan (Diproses / Selesai / Ditolak).

### ⚖️ Layanan Hukum Desa
- Form pengajuan konsultasi & bantuan hukum bagi warga desa.
- Pengkategorian masalah hukum (Perdata, Pidana, Pertanahan/Agraria, Keluarga/Wariss, Dll).
- Kerahasiaan data warga terjamin.

### 👤 Autentikasi & Manajemen Akun Warga
- **Pendaftaran Praktis**: Pendaftaran warga hanya memerlukan Nama Lengkap (sesuai KTP), Nomor HP / WhatsApp, dan Password.
- **Eye Toggle Password**: Icon mata (`👁️`) interaktif untuk menampilkan/menyembunyikan password saat input.
- **Kelola Profil & Ubah Password**: Fitur ubah data diri & kata sandi akun di halaman `akun.php`.
- **Riwayat Pengaduan**: Pelacakan status pengaduan kesehatan & hukum lengkap dengan balasan/catatan dari pihak admin desa.

---

## 🛠️ Teknologi & Stack

- **PHP 8.x** (Native Clean Code Architecture)
- **MySQL / MariaDB** (Database Relasional)
- **Tailwind CSS & Vanilla CSS** (Custom Responsive Utility & Glassmorphism Design)
- **JavaScript ES6+** (Interaktivitas Speed Dial, Eye Toggle, Modal, Scroll Animations)
- **Feather / Custom Vector SVGs** (Ikonografi Presisi)

---

## 🗄️ Panduan Instalasi Database

1. Buka **phpMyAdmin** atau MySQL CLI di server lokal (Laragon / XAMPP).
2. Buat database baru dengan nama `desa_sungaibakaukecil`:
   ```sql
   CREATE DATABASE desa_sungaibakaukecil CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. Import file `setup.sql` yang tersedia di direktori utama project:
   ```bash
   mysql -u root -p desa_sungaibakaukecil < setup.sql
   ```

---

## 🚀 Cara Menjalankan

1. Pastikan web server (Apache/Nginx) dan MySQL aktif di server lokal Anda (misal: Laragon di `C:\laragon\www\desa_sungaibakaukecil`).
2. Buka browser dan akses alamat:
   ```http
   http://localhost/desa_sungaibakaukecil/
   ```

---

## 📂 Struktur Direktori Project

```
desa_sungaibakaukecil/
├── assets/
│   ├── css/
│   │   └── style.css            # Custom Design Tokens & Mobile Responsive Styles
│   ├── js/
│   │   └── main.js             # Speed Dial, Modal & Form Interactivity
│   └── images/                 # Assets Gambar & Logo
├── includes/
│   ├── auth.php                # Session & Authentication Helper Functions
│   ├── db.php                  # Database Connection (PDO)
│   ├── header.php              # Responsive Top Navbar & Mobile Bottom Nav
│   └── footer.php              # Footer Component & Back to Top Button
├── uploads/                    # Folder Penyimpanan Foto Bukti Pengaduan
├── akun.php                    # Halaman Kelola Profil & Ubah Password
├── index.php                   # Landing Page (Beranda Desa)
├── layanan-hukum.php           # Form Layanan Hukum Desa
├── layanan-pengaduan.php       # Form Pengaduan Penyakit/Kesehatan
├── login.php                   # Form Login Warga
├── logout.php                  # Script Logout Session
├── register.php                # Form Pendaftaran Warga Baru
├── riwayat.php                 # Halaman Riwayat & Status Pengaduan Warga
├── setup.sql                   # Skema Database & Tabel Initial
└── README.md                   # Dokumentasi Project
```

---

© 2026 **Pemerintah Desa Sungai Bakau Kecil** — Kabupaten Mempawah, Kalimantan Barat.
