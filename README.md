# SiPayPesantren

Panduan ini dibuat untuk pengguna non-teknis agar bisa menjalankan aplikasi SiPayPesantren di laptop lokal dengan mudah.

## 1. Kebutuhan Awal

Sebelum mulai, pastikan laptop sudah terpasang:

1. Git
2. PHP versi 8.2 atau lebih baru
3. Composer
4. Node.js (disarankan versi 18 ke atas) dan npm

Jika salah satu belum ada, minta tim IT membantu instalasi terlebih dahulu.

## 2. Ambil Project dari GitHub (Clone)

Repository resmi:

- https://github.com/HaekalAlif/SIPT.git

Langkah:

1. Buka Command Prompt (CMD).
2. Pindah ke folder tempat project akan disimpan.
3. Jalankan perintah berikut:

```bash
git clone https://github.com/HaekalAlif/SIPT.git
cd SIPT
git checkout main
```

Catatan:

- Branch utama project adalah `main`.

## 3. Instalasi Aplikasi (Pertama Kali)

Jalankan perintah di bawah ini satu per satu dari folder project `SIPT`.

### Langkah 1: Install library backend

```bash
composer install
```

### Langkah 2: Buat file konfigurasi environment

Jika memakai CMD Windows:

```cmd
copy .env.example .env
```

Jika memakai PowerShell:

```powershell
Copy-Item .env.example .env
```

### Langkah 3: Generate app key

```bash
php artisan key:generate
```

### Langkah 4: Isi konfigurasi DB di file .env

Di tahap ini, cukup atur koneksi database di file .env.

Jika memakai MySQL, isi seperti ini:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sipt
DB_USERNAME=root
DB_PASSWORD=
```

### Langkah 5: Migrasi (membuat struktur database otomatis)

Setelah .env diisi, jalankan:

```bash
php artisan migrate --seed
```

Perintah ini akan:

1. Membuat semua tabel aplikasi (melalui migration).
2. Mengisi data awal (melalui seeder).

### Langkah 6: Install library frontend

```bash
npm install
```

### Langkah 7: Aktifkan link storage (wajib untuk gambar upload)

```bash
php artisan storage:link
```

## 4. Menjalankan Aplikasi

Gunakan cara paling mudah berikut:

```bash
composer dev
```

Setelah itu buka browser:

- http://localhost:8000

## 5. Cara Login (Data Awal)

Password default semua akun seed:

- password

Contoh akun admin:

- admin@example.com

## 6. Cara Update Jika Ada Perubahan dari Tim Developer

Jika developer memberi update terbaru, lakukan langkah ini:

1. Pastikan aplikasi sedang tidak berjalan (stop terminal dengan `Ctrl + C`).
2. Buka terminal di folder project `SIPT`.
3. Jalankan perintah berikut:

```bash
git checkout main
git pull origin main
composer install
npm install
php artisan migrate
php artisan optimize:clear
```

4. Jalankan kembali aplikasi:

```bash
composer dev
```

## 7. Troubleshooting Sederhana

### Aplikasi tidak bisa dibuka

1. Pastikan `composer dev` masih berjalan.
2. Coba akses http://localhost:8000 kembali.

### Gambar/logo upload tidak tampil

Jalankan ulang:

```bash
php artisan storage:link
```

### Port 8000 dipakai aplikasi lain

Jalankan server di port lain:

```bash
php artisan serve --port=8080
```

Lalu buka:

- http://localhost:8080

## 8. Kontak Bantuan

Jika ada kendala, hubungi PIC project:

1. Nama: M. Haekal Alif Putra
2. WhatsApp: 087817555827
