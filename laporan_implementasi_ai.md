# Laporan Implementasi: Sistem Local AI Chatbot Berbasis Multi-Tenant (Siloing)
**Proyek Magang Kedinasan - Pemerintah Kabupaten Sleman**

## 1. Latar Belakang & Tujuan
Implementasi ini ditujukan untuk memenuhi kebutuhan instansi terkait pembuatan asisten virtual AI yang cerdas, aman, dan dapat digunakan oleh banyak divisi tanpa adanya kebocoran data.
Sesuai arahan mentor:
* **"Bisa multi-user yang terkotak-kotak"**: Membutuhkan arsitektur *Multi-Tenant* (Siloing Data) di mana setiap dinas hanya memiliki akses ke datanya masing-masing.
* **"Punya pengetahuan sendiri"**: AI tidak boleh berhalusinasi dan harus menjawab berdasarkan dokumen resmi instansi.

## 2. Arsitektur Sistem (100% Offline & Air-Gapped)
Sistem ini dibangun dengan mengutamakan kedaulatan data (Data Sovereignty) agar data sensitif pemerintahan tidak bocor ke server pihak ketiga (seperti OpenAI/ChatGPT).
* **LLM Engine:** Ollama menjalankan model **Llama 3.2 (3B Parameters)** secara lokal.
* **Orchestrator & UI:** **RAGFlow** di-deploy menggunakan Docker sebagai pusat antarmuka dan manajemen *Knowledge Base* tingkat lanjut berbasis *Deep Document Understanding*.
* **Teknologi AI:** Menggunakan metode **RAG (Retrieval-Augmented Generation)** untuk membaca dan menyimpulkan dokumen tanpa *fine-tuning* yang mahal.

## 3. Mekanisme Kecerdasan Buatan (Under the Hood)
Meskipun antarmuka terlihat sederhana, sistem ini menjalankan *pipeline AI* yang sangat kompleks di latar belakang:
1. **Document Chunking & Parsing:** Saat dokumen dinas diunggah, sistem tidak sekadar menyimpannya, melainkan memotongnya menjadi ratusan teks kecil (*chunks*) dengan ukuran token tertentu agar muat dalam *Context Window* LLM.
2. **Vector Embedding:** Teks tersebut diubah menjadi representasi matematis (vektor matriks) menggunakan model *Embedder* lokal (misalnya Nomic Embed Text).
3. **Semantic Similarity Search:** Saat user bertanya, pertanyaan diubah menjadi vektor. Sistem mencari kemiripan (*Cosine Similarity*) antara pertanyaan user dengan dokumen dinas di dalam Vector Database (Infinity / Elasticsearch).
4. **Augmented Generation:** Potongan dokumen paling relevan dikirim sebagai *prompt* ke LLM Llama 3.2, yang kemudian merangkai jawaban dengan bahasa natural dan akurat.

## 4. Implementasi Knowledge Base (Pengetahuan Sendiri)
Data diekstraksi (scraping) dari website resmi masing-masing dinas dan distrukturisasi ke dalam file `.txt`:
1. `kominfo_sleman.txt` (Berisi Visi, Misi, Sejarah, dan Tupoksi Dinas Kominfo).
2. `pariwisata_sleman.txt` (Berisi Visi, Misi, Sejarah, dan Tupoksi Dinas Pariwisata).
Dokumen ini kemudian di-embed ke dalam *Knowledge Base* spesifik di RAGFlow menggunakan kemampuan *Deep Document Understanding* untuk ekstraksi tingkat lanjut.

## 5. Implementasi Keamanan & Siloing Data (Multi-Tenant)
Untuk mencegah staf Dinas Pariwisata melihat data Dinas Kominfo (dan sebaliknya), diterapkan sistem **Role-Based Access Control (RBAC)** secara *native* dan fitur isolasi *Knowledge Base* di RAGFlow:
1. **Super Admin (`admin`)**: Memiliki hak akses penuh untuk mengelola *Users*, membuat konfigurasi Tenant, mengunggah dokumen ke *Dataset* (Knowledge Base), dan mendesain *Chat Apps* (Agen AI).
2. **Tenant/Grup Dinas (Contoh: `Kominfo` & `Pariwisata`)**: Akun perwakilan dinas dialokasikan ke dalam Tenant masing-masing. *Dataset* dan *Chat Apps* diisolasi secara spesifik menggunakan fitur **Role-Based Access Control**, sehingga anggota dinas hanya bisa berinteraksi dengan asisten AI dan melihat dokumen milik dinasnya sendiri.
3. **Pembatasan Tools (Strict RAG):** Izin akses (*Permissions*) terhadap fitur eksternal seperti *Web Search*, *Code Interpreter*, dan *Image Generation* dimatikan secara paksa untuk grup dinas. Hal ini mencegah LLM melakukan pencarian ke internet, memastikan sistem beroperasi murni secara *offline* (Air-gapped) demi keamanan data.

## 6. Hasil Pengujian
* **Validasi RAG:** AI mampu menjawab pertanyaan spesifik seperti "Apa visi dan misi dari dinas ini?" dengan akurasi 100% berdasarkan dokumen teks yang diunggah.
* **Validasi Siloing:** Saat login sebagai staf Pariwisata, *Dataset* dan *Chat App* milik Kominfo tidak muncul di antarmuka, dan AI menolak menjawab pertanyaan seputar Kominfo karena ketiadaan akses (*Access Denied / Out of Context*).

## 7. Kesimpulan
Sistem Local AI Chatbot ini telah **siap digunakan untuk tahap purwarupa (prototype) dan demonstrasi**. Sistem ini terbukti aman, cerdas, mampu menampung banyak instansi (multi-user), dan mengisolasi data masing-masing instansi dengan sempurna menggunakan kontrol akses bawaan (Role-Based Access Control).

## 8. Pencapaian Migrasi ke RAGFlow & Saran Pengembangan (Future Work)
Sistem ini telah bermigrasi ke ekosistem **RAGFlow** (skala *Enterprise*) untuk menjawab tantangan penguraian dokumen dinas yang kompleks.
1. **Keunggulan Penguraian Dokumen (Deep Document Understanding):** RAGFlow memungkinkan sistem untuk mengekstraksi tidak hanya teks biasa, tetapi juga tabel, bagan, dan tata letak berlapis pada dokumen tata usaha dinas, meningkatkan akurasi *retrieval* secara drastis.
2. **Skalabilitas & Infrastruktur:** Mengingat RAGFlow membutuhkan komputasi yang cukup tinggi (melibatkan *Elasticsearch*, *Redis*, dan *MinIO*), pastikan *server deployment* minimal memiliki RAM 16GB (sangat direkomendasikan 32GB) dan CPU yang mumpuni.
3. **Pengembangan Kedepan:** Memanfaatkan integrasi API RAGFlow yang masif untuk disematkan langsung ke dalam *website* publik masing-masing dinas terkait.
