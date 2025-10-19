# Cahaya Perempuan — Sistem Pengaduan & Informasi

Aplikasi **Laravel** untuk pengaduan/layanan dengan desain berperspektif korban.  
Berisi halaman **Dashboard**, **Pengaduan**, **Cara Melapor**, **FAQ**, **Kontak Kami**, dan **Profil Lembaga**.

---

## 🚀 Tech Stack

- **Laravel** 10/11 + Blade + Vite  
- **MySQL/MariaDB**  
- **Tailwind CSS**  
- **Node.js**

---

## ✅ Prasyarat

- **PHP** ≥ 8.1 (extensions: `openssl`, `pdo`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `curl`, `bcmath`)
- **Composer**
- **Node.js** ≥ 18 & **npm**
- **MySQL/MariaDB**
- (Disarankan) **Laragon/XAMPP** untuk Windows

---

## ⚡ Setup Singkat (Windows/Laragon)

```bash
git clone https://github.com/ryndei/Cahaya-Perempuan.git
cd Cahaya-Perempuan

copy .env.example .env
composer install
npm install

php artisan key:generate
# sesuaikan konfigurasi DB di file .env kemudian:
php artisan migrate --seed

# build aset (pilih salah satu)
npm run dev   # saat pengembangan (hot reload)
npm run build # produksi

php artisan serve
