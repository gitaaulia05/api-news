
<!-- PROJECT LOGO -->
<br />
<div align="center">
  <a href="https://github.com/othneildrew/Best-README-Template">
    <img src="public/assets/images/logo.png" alt="Logo" width="80" height="80">
  </a>

  <h3 align="center">API NEWS</h3>

  <p align="center">
    API Berita ini dibuat untuk repository portal-berita
    <br />
    <a href="https://github.com/gitaaulia05/portal-berita"><strong>portal-berita »</strong></a>
    <br />
  </p>
</div>


<!-- ABOUT THE PROJECT -->
## About Project
Api Berita ini adalah RESTful API berbasis laravel yang dirancang untuk mengelola data berita, kategori, dan manajemen pengguna yang meliputi admin, jurnalis dan pemaca. API ini Mendukung fitur Autentikasi, filter berita berdasarkan topik serta pencarian.


### Built With

API Berita ini dibuat menggunakan laravel serta untuk optimalisasi Response aplikasi ini didukung oleh franken PHP.
* [![Laravel][Laravel.com]][Laravel-url]
* [![Swagger][Swagger.com]][Swagger-url]
* [![Mysql][Mysql.com]][Mysql-url]


## Fitur Utama
* 🔐 Autentikasi berbasis token (Bearer Token)
* 📝 CRUD Berita
* 🗂️ Manajemen Kategori
* 👥 Role-based access (Admin, Jurnalis, User)
* 🔍 Pencarian & filter berita berdasarkan topik/kategori
* ⚡ Caching (ETag)
* 📅 Berita terbaru & pilihan
* 📦 Support JSON response

<!-- GETTING STARTED -->
## Getting Started
Panduan awal untuk menyiapkan dan menjalankan proyek API Berita di lingkungan lokal. 

### Prerequisites
Hal yang diperlukan untuk mendukung aplikasi ini berjalan, gunakan command dibawah ini di terminal.

* npm
  ```sh
  npm install npm@latest -g
  ```

### Installation

_Ikuti langkah-langkah berikut untuk menginstal dan menjalankan aplikasi secara lokal._

1. Clone repositori
   ```sh
   https://github.com/gitaaulia05/api-news.git
   ```
2. Install NPM packages
   ```sh
   npm install
   ```
3. Generate application key dan jalankan migrasi database beserta seeder:
   ```bash
    php artisan key:generate
    php artisan migrate --seed
   ```

4. Instalasi Franken PHP pastikan anda sudah memiliki WSL di Komputer pribadi anda dan jalankan perinta di bawah ini di dalam WSL

    ``` bash
      sudo apt update
      sudo apt install frankenphp
      php artisan octane:start --server=frankenphp --host=172.23.67.4 --port=8001 --https
    ```

## Library Yang digunakan 

<!-- USAGE EXAMPLES -->
## API DOCS

_Dokumentasi api ini ada di dalam folder yang ada dalam proyek 
<a href="docs/">DOKUMENTASI API</a>_

[Laravel.com]: https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white
[Laravel-url]: https://laravel.com

[Swagger.com]:https://img.shields.io/badge/-Swagger-%23Clojure?style=for-the-badge&logo=swagger&logoColor=white
[Swagger-url]: https://swagger.io

[Mysql.com]:https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white
[Mysql-url]:https://www.mysql.com/