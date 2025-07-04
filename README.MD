# Proyek LMS Rithub Batch 1

Repository ini berisi kode sumber untuk Proyek Learning Management System (LMS) yang dikembangkan menggunakan TALL Stack (Tailwind CSS, Alpine.js, Laravel, Livewire) dan Filament.

## Teknologi yang Digunakan

-   **Backend:** Laravel 11
-   **Admin Panel:** Filament 3
-   **Frontend:** Blade, Tailwind CSS, Alpine.js
-   **Database:** SQLite (default), dapat dikonfigurasi ke MySQL/PostgreSQL
-   **Web Server:** Nginx (via Docker)
-   **Containerization:** Docker

## Prasyarat

Pastikan perangkat Anda telah terinstal perangkat lunak berikut:

-   [PHP](https://www.php.net/downloads.php) (version 8.2 or newer)
-   [MySQL](https://dev.mysql.com/downloads/) (or install [Laragon](https://laragon.org/download/))
-   [Composer](https://getcomposer.org/download/)
-   [Node.js & NPM](https://nodejs.org/en/download/)
-   [Git CLI](https://git-scm.com/downloads)

## Langkah-langkah Instalasi

Berikut adalah panduan untuk menginstal dan menjalankan proyek ini di lingkungan lokal Anda.

### 1. Clone Repository

Clone repository ini ke mesin lokal Anda menggunakan Git:

```bash
git clone https://github.com/username/project-lms-rithub-batch1.git
cd project-lms-rithub-batch1
```

### 2. Instalasi Dependensi

Instal dependensi PHP menggunakan Composer dan dependensi JavaScript menggunakan NPM:

```bash
composer install
npm install
```

### 3. Konfigurasi Lingkungan

Salin file `.env.example` menjadi `.env`. File ini akan digunakan untuk menyimpan konfigurasi lingkungan proyek.

```bash
cp .env.example .env
```

Setelah itu, buka file `.env` dan sesuaikan konfigurasi database dan variabel lainnya jika diperlukan. Secara default, proyek ini dikonfigurasi untuk menggunakan SQLite.

### 4. Generate Application Key

Buat kunci aplikasi unik untuk proyek Laravel Anda:

```bash
php artisan key:generate
```

### 5. Migrasi dan Seeding Database

Jalankan migrasi untuk membuat skema database dan jalankan seeder untuk mengisi data awal (jika ada):

```bash
php artisan migrate --seed
```

Perintah ini juga akan membuat file `database/database.sqlite` jika belum ada.

### 6. Build Aset Frontend

Compile aset frontend seperti CSS dan JavaScript menggunakan Vite:

```bash
npm run build
```

## Menjalankan Aplikasi

Anda dapat menjalankan aplikasi menggunakan Docker (direkomendasikan) atau server pengembangan lokal Laravel.

### Menggunakan Docker

Cara ini direkomendasikan untuk memastikan konsistensi lingkungan pengembangan.

1.  **Build dan Jalankan Container:**
    Gunakan Docker Compose untuk membangun image dan menjalankan container di background.

    ```bash
    docker-compose up -d --build
    ```

2.  **Akses Aplikasi:**
    Setelah container berjalan, aplikasi dapat diakses melalui browser di alamat:
    [http://localhost:8000](http://localhost:8000)

### Menggunakan Server Lokal (Artisan)

Jika Anda tidak ingin menggunakan Docker, Anda bisa menjalankan server pengembangan bawaan Laravel.

1.  **Jalankan Server:**

    ```bash
    php artisan serve
    ```

2.  **Jalankan Vite (untuk Frontend):**
    Buka terminal baru dan jalankan Vite untuk melayani aset frontend secara real-time.

    ```bash
    npm run dev
    ```

3.  **Akses Aplikasi:**
    Aplikasi akan tersedia di alamat yang ditampilkan oleh `php artisan serve` (biasanya [http://127.0.0.1:8000](http://127.0.0.1:8000)).

## Perintah yang Berguna

-   **Masuk ke dalam container Docker:**
    ```bash
    docker-compose exec app bash
    ```

-   **Menjalankan tes:**
    ```bash
    php artisan test
    ```

-   **Menghentikan container Docker:**
    ```bash
    docker-compose down
    ```

-   **Membuat user admin Filament:**
    ```bash
    php artisan make:filament-user
    ```
    Ikuti instruksi untuk membuat akun admin, lalu akses panel admin di `/admin`.
