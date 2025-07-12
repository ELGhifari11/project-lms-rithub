# Rumah IT Hub - Backend API

![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php)
![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel)
![Filament](https://img.shields.io/badge/Filament-3.x-F59E0B?style=for-the-badge)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql)
![GitHub Actions](https://img.shields.io/badge/GitHub_Actions-CI/CD-2088FF?style=for-the-badge&logo=github-actions)

Backend service untuk platform e-learning Rumah IT Hub, dibangun menggunakan Laravel 11 dengan admin panel Filament. Proyek ini menyediakan REST API untuk aplikasi client dan dilengkapi dengan setup Docker untuk kemudahan development serta alur kerja CI/CD untuk deployment otomatis.

---

## Daftar Isi
1. [Fitur Utama](#fitur-utama)
2. [Teknologi yang Digunakan](#teknologi-yang-digunakan)
3. [Struktur Proyek](#struktur-proyek)
4. [Instalasi & Setup (Development)](#-instalasi--setup-development)
5. [Database (ERD)](#database-erd)
6. [Dokumentasi API](#dokumentasi-api)
7. [Standar Kualitas Kode](#-standar-kualitas-kode)
8. [Deployment (CI/CD)](#-deployment-cicd)
9. [Perintah Penting Lainnya](#perintah-penting-lainnya)

---

## Fitur Utama

-   **Manajemen Kursus:** Pengelolaan kelas, modul, dan konten edukasi.
-   **Sistem Pengguna & Mentor:** Otentikasi, profil pengguna, dan manajemen mentor.
-   **Transaksi & Pembayaran:** Proses checkout, pembayaran, dan manajemen order terintegrasi dengan payment gateway.
-   **API Dokumentasi Otomatis:** Dokumentasi API interaktif yang di-generate secara otomatis menggunakan Scramble.
-   **Admin Panel Lengkap:** Panel admin yang powerful dibangun dengan Filament.
-   **Development Ready:** Dilengkapi dengan konfigurasi Docker untuk setup lingkungan development yang cepat dan konsisten.
-   **Deployment Otomatis:** Alur kerja CI/CD dengan GitHub Actions untuk deployment ke CPanel dan VPS.

---

## Teknologi yang Digunakan

-   **Backend:** PHP 8.2, Laravel 11
-   **Admin Panel:** Filament 3.x
-   **Database:** MySQL 8.0
-   **Web Server:** Nginx
-   **Containerization:** Docker & Docker Compose
-   **Frontend Assets:** Vite
-   **API Documentation:** Scramble
-   **CI/CD:** GitHub Actions

---

## Struktur Proyek

Berikut adalah gambaran umum struktur direktori penting dalam proyek ini.

```
.
├── app/
│   ├── Filament/      # Semua Resources, Pages, dan Widget untuk Admin Panel Filament
│   ├── Http/
│   │   ├── Controllers/ # Controller untuk menangani request HTTP (termasuk API)
│   │   └── Resources/   # Transformasi data model (API Resources)
│   ├── Models/        # Model Eloquent yang merepresentasikan tabel database
│   ├── Jobs/          # Class Job yang berjalan di background (queue)
│   └── Providers/     # Service provider Laravel
├── config/            # File-file konfigurasi aplikasi
├── database/
│   ├── migrations/    # Skema database
│   └── seeders/       # Data awal untuk database
├── routes/
│   ├── api.php        # Rute untuk REST API
│   └── web.php        # Rute untuk web (termasuk admin panel)
├── .github/
│   └── workflows/     # Alur kerja CI/CD (GitHub Actions)
└── README.md          # Anda sedang membacanya :)
```

---

## 🚀 Instalasi & Setup (Development)

Berikut adalah panduan untuk menjalankan proyek ini di lingkungan development menggunakan Docker.

### 1. Prasyarat

-   [Docker](https://www.docker.com/products/docker-desktop/)
-   [Composer](https://getcomposer.org/)
-   [Node.js & NPM](https://nodejs.org/en/)

### 2. Clone Repository

```bash
git clone https://github.com/your-username/rumah-it-hub-backend.git
cd rumah-it-hub-backend
```

### 3. Konfigurasi Environment

Proyek ini membutuhkan file `.env` untuk menyimpan konfigurasi dan kredensial.

```bash
# Salin dari contoh yang sudah disediakan
cp .env.example .env
```

Buka file `.env` dan sesuaikan variabel yang diperlukan. Untuk setup Docker bawaan, konfigurasi database sudah sesuai.

### 4. Instalasi Dependensi

Install semua dependensi PHP (Laravel) dan JavaScript.

```bash
# Install dependensi PHP
composer install

# Install dependensi Node.js
npm install
```

### 5. Jalankan Aplikasi dengan Docker

Gunakan Docker Compose untuk membangun image dan menjalankan semua service (aplikasi, database, web server).

```bash
# Build dan jalankan container di background
docker-compose up -d --build
```

### 6. Finalisasi Setup Laravel

Setelah container berjalan, jalankan perintah-perintah berikut untuk menyelesaikan instalasi Laravel.

```bash
# Generate application key
docker-compose exec app php artisan key:generate

# Jalankan migrasi database dan seeder
docker-compose exec app php artisan migrate --seed

# Buat symbolic link untuk storage
docker-compose exec app php artisan storage:link

# Build aset frontend
npm run dev
```

### 7. Akses Aplikasi

Setelah semua langkah selesai, aplikasi Anda akan dapat diakses di:

-   **Website:** [http://localhost:8000](http://localhost:8000)
-   **Admin Panel:** [http://localhost:8000/admin](http://localhost:8000/admin)
-   **Dokumentasi API:** [http://localhost:8000/docs/api](http://localhost:8000/docs/api)

---

## Dokumentasi API

Dokumentasi API untuk proyek ini di-generate secara otomatis menggunakan **Scramble**. Anda bisa mengaksesnya melalui browser untuk melihat semua endpoint yang tersedia, parameter yang dibutuhkan, dan contoh respons.

-   **URL Dokumentasi API:** [http://localhost:8000/docs/api](http://localhost:8000/docs/api)

---
