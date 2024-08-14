# Lumen Auth - Swagger API Starter

Ini adalah proyek API yang dikembangkan menggunakan Lumen dan didokumentasikan dengan Swagger. Proyek ini mencakup berbagai endpoint API yang dapat digunakan untuk melakukan operasi CRUD pada pengguna, dengan dokumentasi API yang lengkap melalui Swagger.

## Prerequisites

-   API CRUD untuk **user**
-   Dokumentasi API menggunakan **Swagger**

### Prerequisites

-   PHP >= 7.1
-   Composer
-   MySQL
-   Lumen Framework

## Installation

1. **Clone Repositori**

    Pertama, clone repositori ini ke mesin lokal Anda:

    ```
    git clone https://github.com/kristokeith/lumen-authapi-interntest2024.git
    ```

2. **Masuk ke Direktori Proyek**

    ```
    cd repo-name
    ```

3. **Instal Dependensi**

    Instal semua dependensi yang diperlukan dengan Composer:

    ```
    composer install
    ```

4. **Konfigurasi .env**

    Edit file .env dengan pengaturan database Anda:

    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=nama_database
    DB_USERNAME=nama_pengguna
    DB_PASSWORD=katasandi

    ```

5. **Generate Kunci Aplikasi**

    Generate kunci aplikasi untuk enkripsi:

    ```
    php artisan key:generate
    ```

6. **Migrasi Database**

    Jalankan migrasi untuk membuat tabel yang diperlukan di database:

    ```
    php artisan migrate
    ```

7. **Seeding Database**

    Jika Anda ingin menambahkan data dummy ke database, jalankan seeder:

    ```
    php artisan db:seed

    ```

8. **Jalankan Server**

    Jalankan server Lumen:

    ```
    php artisan swagger-lume:generate
    php artisan serve
    ```

## Dokumentasi API

Dokumentasi API dapat diakses melalui Swagger UI. Setelah Anda menjalankan server Lumen, buka URL berikut di browser Anda:

```
http://127.0.0.1:8000/api/documentation
```

Di sana Anda akan menemukan dokumentasi lengkap mengenai semua endpoint API yang tersedia.

## Kontak
Jika Anda memiliki pertanyaan atau umpan balik, jangan ragu untuk menghubungi saya di:

- GitHub: https://github.com/kristokeith