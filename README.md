# Admin Panel for CodeIgniter 4

Proyek master admin panel berbasis CodeIgniter 4 yang dapat digunakan sebagai basis untuk berbagai proyek.

## Fitur

- Dashboard dengan statistik pengguna
- Manajemen Pengguna (CRUD)
  - Pencarian
  - Pagination
  - Sorting
  - Filter berdasarkan Role
- Implementasi AJAX jQuery untuk operasi CRUD
- Responsive Design dengan Bootstrap 5
- DataTables untuk tampilan tabel

## Persyaratan

- PHP 7.4 atau lebih tinggi
- MySQL/MariaDB
- Composer

## Instalasi

1. Clone repositori ini
   ```bash
   git clone https://github.com/username/app-master-ci4.git
   cd app-master-ci4
   ```

2. Install dependensi PHP
   ```bash
   composer install
   ```

3. Buat database baru

4. Salin `.env.example` menjadi `.env` dan sesuaikan konfigurasi database
   ```bash
   cp env .env
   ```

5. Update konfigurasi database di file `.env`
   ```
   database.default.hostname = localhost
   database.default.database = nama_database
   database.default.username = username
   database.default.password = password
   database.default.DBDriver = MySQLi
   ```

6. Jalankan migrasi untuk membuat tabel
   ```bash
   php spark migrate
   ```

7. Jalankan aplikasi
   ```bash
   php spark serve
   ```

8. Buka aplikasi di browser: `http://localhost:8080/admin`

## Login Admin Default

- Username: `admin`
- Password: `admin123`

## Struktur Proyek

- `app/Controllers/Admin.php` - Controller untuk admin panel
- `app/Models/UserModel.php` - Model untuk pengguna
- `app/Views/admin/*` - Template tampilan admin
- `app/Database/Migrations/*` - Migrasi database

## Customisasi

Proyek ini dirancang untuk menjadi basis yang dapat dikustomisasi sesuai kebutuhan proyek spesifik:

1. Tambahkan model dan controller baru untuk fitur tambahan
2. Sesuaikan tampilan dengan mengedit file di `app/Views/admin/*`
3. Tambahkan role pengguna baru di `Admin::getRoles()`
4. Sesuaikan migrasi untuk menambahkan tabel lain yang diperlukan

## Lisensi

MIT License
