# 🤖 Chatbot Multi-Tenant Pemkab Sleman

Project chatbot untuk Pemerintah Kabupaten Sleman dengan arsitektur multi-tenant, mendukung berbagai layanan seperti Dukcapil, Pariwisata, dan lainnya.

## 📋 Daftar Isi
- [Komponen Project](#komponen-project)
- [Prasyarat](#prasyarat)
- [Instalasi](#instalasi)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Struktur Folder](#struktur-folder)
- [Dokumentasi](#dokumentasi)

---

## 🧩 Komponen Project

Project ini terdiri dari 2 komponen utama:

1. **Admin Dashboard** (Laravel) - Dashboard untuk manajemen chatbot
2. **Nyai Support** (Node.js) - S-Bot Agentic Studio & Widget chatbot

---

## 📦 Prasyarat

Sebelum menjalankan project ini, pastikan sudah terinstall:

### Software Wajib:
- **PHP** >= 8.1 ([Download](https://www.php.net/downloads))
- **Composer** ([Download](https://getcomposer.org/download/))
- **Node.js** >= 18.x ([Download](https://nodejs.org/))
- **MySQL** atau **MariaDB** ([XAMPP](https://www.apachefriends.org/) recommended)
- **Git** ([Download](https://git-scm.com/downloads))

### Ekstensi PHP yang Diperlukan:
- OpenSSL
- PDO
- Mbstring
- Tokenizer
- XML
- Ctype
- JSON
- BCMath

---

## 🚀 Instalasi

### 1️⃣ Clone Repository

```bash
git clone https://github.com/USERNAME-ANDA/chatbot-pemkab-sleman.git
cd chatbot-pemkab-sleman
```

### 2️⃣ Setup Admin Dashboard (Laravel)

```bash
# Masuk ke folder admin dashboard
cd admin_dashboard

# Install dependencies PHP
composer install

# Install dependencies Node.js (untuk asset compilation)
npm install

# Copy file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

**Setup Database:**
1. Buka file `.env`
2. Sesuaikan konfigurasi database:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=chatbot_sleman
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. Buat database baru bernama `chatbot_sleman` di MySQL
4. Jalankan migrasi:
   ```bash
   php artisan migrate
   ```

### 3️⃣ Setup Nyai Support (Node.js)

```bash
# Kembali ke folder root, lalu masuk ke nyai_support
cd ../nyai_support

# Install dependencies Node.js
npm install

# Copy file environment (jika ada)
cp .env.example .env
```

Edit file `.env` di `nyai_support` sesuai kebutuhan (API keys, database config, dll).

---

## ▶️ Menjalankan Aplikasi

### Jalankan Admin Dashboard (Laravel)

```bash
cd admin_dashboard
php artisan serve
```

Dashboard akan berjalan di: `http://localhost:8000`

### Jalankan Nyai Support Widget (Node.js)

Buka terminal baru:

```bash
cd nyai_support
node server.js
```

atau jika menggunakan npm script:

```bash
npm start
```

Widget akan berjalan di port yang ditentukan di `.env` atau `server.js`.

---

## 📁 Struktur Folder

```
chatbot-pemkab-sleman/
├── admin_dashboard/          # Laravel Dashboard
│   ├── app/
│   ├── config/
│   ├── database/
│   ├── public/
│   ├── resources/
│   └── routes/
├── nyai_support/            # Node.js S-Bot Studio & Widget
│   ├── src/
│   ├── public/
│   └── server.js
├── knowledge_base/          # Dokumen knowledge base
├── aturan_dukcapil_sleman.txt
├── pariwisata_sleman.txt
├── IMPLEMENTATION_PLAN.md   # Rencana implementasi
├── laporan_implementasi_ai.md
└── README.md               # File ini
```

---

## 📚 Dokumentasi

- **IMPLEMENTATION_PLAN.md** - Rencana implementasi lengkap project
- **laporan_implementasi_ai.md** - Laporan implementasi AI
- **BAB_4_PEMBAHASAN.md** - Pembahasan detail

### Knowledge Base:
- `aturan_dukcapil_sleman.txt` - Aturan layanan Dukcapil
- `pariwisata_sleman.txt` - Informasi pariwisata Sleman
- `knowledge_base/` - Folder berisi dokumen pendukung lainnya

---

## ⚠️ Catatan Penting

### File yang TIDAK Di-upload:
Project ini **TIDAK** menyertakan folder berikut (karena terlalu besar):
- `dify/` - Clone dari repo Dify (install sendiri jika diperlukan)
- `ragflow/` - Clone dari repo RagFlow (install sendiri jika diperlukan)
- `flowise_d_drive/` - Clone dari repo Flowise (install sendiri jika diperlukan)
- `node_modules/` - Dependencies Node.js (install via `npm install`)
- `vendor/` - Dependencies PHP (install via `composer install`)

### File Kredensial:
File `.env` berisi kredensial rahasia (API keys, database password) yang **TIDAK** di-upload ke GitHub. Pastikan untuk:
1. Copy dari `.env.example`
2. Isi dengan kredensial Anda sendiri
3. **JANGAN** commit file `.env` ke Git

---

## 🆘 Troubleshooting

### Error: "Class not found" di Laravel
```bash
composer dump-autoload
```

### Error: "Permission denied" di Laravel
```bash
chmod -R 775 storage bootstrap/cache  # Linux/Mac
# Atau di Windows, pastikan folder storage/ dan bootstrap/cache/ tidak read-only
```

### Port sudah digunakan
Ubah port Laravel:
```bash
php artisan serve --port=8001
```

---

## 👥 Kontributor

Project ini dikembangkan untuk Pemerintah Kabupaten Sleman sebagai bagian dari program magang.

---

## 📞 Kontak & Support

Jika ada pertanyaan atau masalah, silakan buka **Issue** di GitHub repository ini.

---

**Selamat mencoba! 🎉**
