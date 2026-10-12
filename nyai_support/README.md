# 🤖 S-Bot Agentic AI Studio & Embedded Support Widget
**Pemerintah Kabupaten Sleman (Multi-Tenant & Local RAGFlow)**

Sistem platform **Agentic AI (S-Bot)** mandiri yang disesuaikan untuk implementasi layanan publik Pemkab Sleman (Dukcapil, Pariwisata, dan Diskominfo).

---

## 🌟 Fitur Utama (S-Bot Platform)

### 1. 🧠 Arsitektur Routing Semantik
- **Super Agent 🧠**: Bertugas menangani pertanyaan regulasi dan informasi umum berbasis *Local Knowledge Base / RAGFlow* secara *air-gapped*.
- **Specialized Agents 🎯**: Agen kustom bertingkat yang menjalankan *multi-step workflow* otomatis:
  1. `dukcapil-layanan`: Permohonan KTP-el pemula, KK baru, KIA, dan penjadwalan antrean.
  2. `wisata-sleman`: Rekomendasi destinasi Candi, reservasi Jeep Merapi Lava Tour, dan desa wisata.
  3. `pengaduan-layanan`: Penampungan aduan masyarakat (wifi padukuhan, fasilitas publik) terintegrasi ke sistem tiket.
- **AI Router 🔀**: Klasifikasi intensi semantik (bukan sekadar kata kunci) dengan perhitungan *confidence score*.

### 2. ⚡ Step-Based Conversation Flow Builder
Mendukung 5 jenis *step* interaktif:
- `💬 Prompt`: Pesan bot dengan opsi tombol *Quick Replies*.
- `📥 Collect Input`: Pengumpulan data dengan validasi tipe (`text`, `number`, `phone`, `date`, `email`, `option`).
- `🔀 Condition`: Percabangan logika alur berdasarkan nilai variabel.
- `⚡ Workflow`: Panggilan ke API Connector / webhook tiket backend.
- `✅ Response`: Pesan penutup dengan *variable placeholder* (`{nama_pemohon}`, `{ticket_code}`, dll.).

### 3. 👔 Persona Agent
Mendukung 4 gaya persona:
- 👔 **Professional** (Dukcapil / Urusan Kependudukan)
- 😊 **Friendly** (Pariwisata & Destinasi Wisata)
- 🎭 **Witty** (Edukasi & Brand Kreatif)
- 💚 **Empathetic** (Layanan Pengaduan & Aspirasi Warga)

### 4. 🔌 API Connectors
- Pemetaan parameter percakapan ke parameter API (`{nama_pemohon}` ➔ `nama`).
- Ekstraksi respon JSON (`$.data.ticket_code` ➔ `ticket_code`).
- Fitur *Test Ping* untuk memverifikasi koneksi API eksternal.

### 5. 🧪 Test Console dengan Real-Time Debug Panel
- Dual-pane simulator percakapan.
- Panel debug menampilkan:
  - **AI Router Decision** & skor keyakinan (*confidence*).
  - **Current Step** yang sedang dieksekusi.
  - **Collected Variables** secara *live* dalam format JSON.
  - **Step Flow History** dan tombol 🔄 *Reset Session*.

### 6. 📦 Standalone Embed Widget (`nyai-widget.js`)
Widget *live chat* mengambang di pojok kanan bawah yang dapat disematkan ke website mana pun hanya dengan satu baris kode:
```html
<script src="http://localhost:3000/widget/dist/nyai-widget.js" data-agent="dukcapil-layanan"></script>
```
Atau tampilan satu layar penuh (*Fullscreen Iframe*):
```html
<iframe src="http://localhost:3000/widget/fullscreen.html?tenant=sleman-default&lang=id" width="100%" height="650px" frameborder="0"></iframe>
```

---

## 🚀 Cara Menjalankan Aplikasi

1. Masuk ke direktori:
   ```bash
   cd "d:\SEMESTER 6\Chatbot\nyai_support"
   ```
2. Jalankan server:
   ```bash
   npm start
   # atau untuk mode auto-reload dev:
   npm run dev
   ```
3. Akses antarmuka web:
   - **Admin Console & Studio**: [http://localhost:3000](http://localhost:3000)
   - **Simulasi Portal Pemkab Sleman (Embedded Widget)**: [http://localhost:3000/demo](http://localhost:3000/demo)
   - **Fullscreen Widget Mode**: [http://localhost:3000/widget/fullscreen.html](http://localhost:3000/widget/fullscreen.html)
