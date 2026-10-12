# BAB IV. PEMBAHASAN

---

## 4.1 Hasil Evaluasi Platform AI Chatbot

Evaluasi terhadap lima platform AI Chatbot dilakukan secara langsung melalui proses instalasi, konfigurasi, dan pengujian pada lingkungan lokal (self-hosted) menggunakan perangkat laptop dengan spesifikasi standar dan koneksi jaringan internal. Pengujian dilakukan berdasarkan lima kriteria utama yang telah ditentukan pada bab sebelumnya, yaitu kemampuan self-hosted, agentic workflow, multi-agent & knowledge isolation, embeddable, dan production ready.

Hasil evaluasi terhadap masing-masing platform dirangkum dalam tabel berikut.

---

**Tabel 4.1 Hasil Evaluasi Platform AI Chatbot**

| No | Platform           | Self-Hosted | Agentic Workflow | Multi-Agent & Knowledge Isolation | Embeddable | Production Ready |
|----|--------------------|:-----------:|:----------------:|:---------------------------------:|:----------:|:----------------:|
| 1  | Flowise            | ✅          | ✅               | ✅                                | ✅         | ⚠️               |
| 2  | Dify               | ✅          | ✅               | ❌                                | ✅         | ✅               |
| 3  | Open WebUI         | ✅          | ❌               | ⚠️                                | ⚠️         | ✅               |
| 4  | RAGFlow            | ✅          | ✅               | ✅                                | ✅         | ✅               |
| 5  | NyAI & Support Board | ❌        | ❌               | ❌                                | ✅         | ✅               |

Keterangan: ✅ = Terpenuhi, ⚠️ = Terpenuhi Sebagian, ❌ = Tidak Terpenuhi

---

## 4.2 Pembahasan per Platform

### 4.2.1 Flowise

Flowise merupakan platform open-source berbasis antarmuka visual (drag-and-drop) yang memungkinkan pengguna membangun alur kerja AI Agent secara modular tanpa memerlukan keahlian pemrograman mendalam. Setelah dilakukan instalasi melalui Docker pada lingkungan lokal, Flowise berhasil dijalankan secara self-hosted dengan lancar.

Pengujian terhadap kemampuan agentic workflow menunjukkan bahwa Flowise mendukung pembuatan AI Agent yang kompleks melalui node-based pipeline. Pengguna dapat menghubungkan berbagai komponen seperti LLM, retriever, dan tool secara visual, sehingga fleksibilitas dalam membangun alur chatbot sangat tinggi. Selain itu, Flowise menyediakan fitur Chatflow yang dapat di-embed ke dalam website melalui widget JavaScript, sehingga kriteria embeddable terpenuhi.

Untuk multi-agent & knowledge isolation, Flowise mendukung pembuatan beberapa chatflow yang masing-masing dapat dikonfigurasi dengan knowledge base berbeda. Hal ini memungkinkan isolasi pengetahuan antar departemen secara teknis. Namun demikian, terdapat keterbatasan pada aspek production ready, yaitu manajemen pengguna yang belum memiliki sistem Role-Based Access Control (RBAC) yang matang, sehingga pengelolaan akses untuk lingkungan instansi pemerintah memerlukan konfigurasi tambahan yang cukup kompleks. Selain itu, Flowise dinilai memiliki kurva pembelajaran yang cukup tinggi bagi pengguna non-teknis di dalam instansi.

**Kelebihan:** Sangat fleksibel untuk membangun agentic workflow yang kompleks, antarmuka visual yang intuitif, dan mendukung berbagai model LLM.

**Kekurangan:** Sistem manajemen pengguna belum enterprise-grade, stabilitas pada beban tinggi masih perlu pengujian lebih lanjut, serta kurva pembelajaran relatif tinggi.

---

### 4.2.2 Dify

Dify merupakan platform pengembangan aplikasi AI yang menawarkan tampilan antarmuka yang modern dan kemampuan agentic workflow berbasis visual. Instalasi Dify pada lingkungan lokal berhasil dilakukan melalui Docker Compose, dan platform ini dapat dioperasikan secara self-hosted dengan baik.

Dify mendukung pembuatan AI Agent dengan fitur workflow visual yang cukup lengkap, termasuk dukungan terhadap berbagai jenis tool dan integrasi dengan model LLM lokal melalui Ollama. Platform ini juga menyediakan fitur embed chatbot ke dalam website menggunakan script JavaScript yang mudah dikonfigurasi.

Namun, pengujian secara langsung terhadap fitur multi-agent & knowledge isolation mengungkap keterbatasan yang signifikan. Pada implementasi praktis, setiap chatbot (application) dalam Dify hanya dapat dihubungkan dengan satu knowledge base secara bersamaan. Apabila terdapat kebutuhan untuk memisahkan pengetahuan antar departemen (misalnya Kominfo dan Pariwisata), pengguna harus membuat application yang terpisah untuk masing-masing departemen. Meskipun secara teknis hal ini dapat dilakukan, arsitektur tersebut tidak mendukung isolasi yang sesungguhnya dalam satu workspace terintegrasi. Kondisi ini menjadi kelemahan utama Dify dalam konteks implementasi chatbot multi-departemen di Diskominfo Kabupaten Sleman.

**Kelebihan:** Antarmuka modern dan mudah digunakan, mendukung agentic workflow, fitur embed yang mudah dikonfigurasi, dan relatif stabil untuk lingkungan produksi.

**Kekurangan:** Tidak mendukung multi-agent knowledge isolation yang sesungguhnya dalam satu workspace, sehingga kurang optimal untuk lingkungan multi-departemen.

---

### 4.2.3 Open WebUI

Open WebUI merupakan antarmuka web berbasis open-source yang dirancang sebagai frontend untuk model LLM lokal yang dijalankan melalui Ollama. Platform ini dapat diinstal secara self-hosted dengan mudah menggunakan Docker dan memiliki tampilan yang bersih dan ramah pengguna.

Pengujian menunjukkan bahwa Open WebUI memiliki manajemen pengguna dan workspace yang cukup baik, termasuk dukungan terhadap pembuatan Knowledge Base per pengguna atau per kelompok. Dalam hal multi-agent & knowledge isolation, terdapat mekanisme Groups dan Workspaces yang memungkinkan pembatasan akses ke knowledge base tertentu. Namun, fitur ini dinilai belum sepenuhnya mendukung isolasi yang ketat dalam konteks multi-departemen karena konfigurasinya yang cukup kompleks dan rentan terhadap kebocoran akses apabila tidak dikonfigurasi dengan cermat.

Pada aspek agentic workflow, Open WebUI tidak menyediakan fitur visual pipeline atau agentic workflow secara native. Platform ini lebih berfungsi sebagai antarmuka chat yang terhubung ke model LLM tanpa kemampuan orkestrasi agent yang kompleks. Untuk fitur embed, Open WebUI tidak menyediakan widget embed secara langsung ke dalam website eksternal, sehingga integrasi ke laman resmi pemerintah memerlukan pengembangan tambahan.

**Kelebihan:** Antarmuka yang sederhana dan mudah digunakan, manajemen pengguna cukup lengkap, dan mendukung berbagai model LLM lokal.

**Kekurangan:** Tidak mendukung agentic workflow visual, fitur embeddable terbatas, dan isolasi knowledge base antar departemen belum sepenuhnya matang.

---

### 4.2.4 RAGFlow

RAGFlow merupakan platform enterprise-grade yang dirancang khusus untuk implementasi Retrieval-Augmented Generation (RAG) berbasis dokumen. Platform ini diinstal menggunakan Docker Compose dengan ekosistem layanan yang terdiri dari MySQL, MinIO, Redis, dan Elasticsearch/Infinity sebagai mesin pencarian vektor.

Pengujian menunjukkan bahwa RAGFlow memenuhi seluruh kriteria evaluasi yang telah ditetapkan. Pertama, dari sisi self-hosted, RAGFlow berhasil dijalankan sepenuhnya pada infrastruktur lokal tanpa ketergantungan terhadap layanan cloud pihak ketiga, sehingga mendukung prinsip kedaulatan data (data sovereignty) yang penting bagi instansi pemerintah.

Kedua, dari sisi agentic workflow, RAGFlow menyediakan fitur Agent yang memungkinkan pembuatan alur kerja AI yang dapat mengakses berbagai tool secara otomatis. Fitur ini memungkinkan chatbot tidak hanya menjawab pertanyaan berbasis dokumen, tetapi juga melakukan pemrosesan bertahap dalam menyelesaikan kueri yang kompleks.

Ketiga, RAGFlow mendukung multi-agent & knowledge isolation secara penuh melalui arsitektur multi-tenant. Setiap knowledge base dapat dikonfigurasi secara terpisah dan diakses hanya oleh chatbot yang telah ditentukan. Dalam implementasi di Diskominfo Kabupaten Sleman, setiap departemen (seperti Kominfo dan DUKCAPIL) dapat memiliki knowledge base tersendiri yang terisolasi, sehingga tidak terjadi kebocoran informasi antar departemen.

Keempat, RAGFlow menyediakan fitur embed yang memungkinkan chatbot diintegrasikan ke dalam website melalui iframe maupun API, sehingga chatbot dapat ditampilkan langsung pada laman resmi Pemerintah Kabupaten Sleman. Kelima, dari sisi production ready, RAGFlow memiliki infrastruktur yang matang dengan dukungan pemrosesan dokumen tingkat lanjut (Deep Document Understanding), manajemen knowledge base yang robust, serta antarmuka administrasi yang lengkap.

Selama proses implementasi, RAGFlow berhasil memproses berbagai jenis dokumen pemerintahan, termasuk file TXT berisi informasi organisasi Diskominfo Sleman dan dokumen PDF peraturan daerah DUKCAPIL. Pengujian terhadap skenario percakapan multi-turn juga menunjukkan hasil yang memuaskan, dengan chatbot mampu memberikan jawaban yang akurat berdasarkan isi dokumen dan menghindari hallucination melalui konfigurasi similarity threshold yang ketat.

**Kelebihan:** Mendukung seluruh kriteria evaluasi, arsitektur enterprise-grade, kemampuan RAG tingkat lanjut, multi-tenant dengan isolasi data penuh, serta embeddable ke website.

**Kekurangan:** Membutuhkan sumber daya server yang lebih besar dibandingkan platform lain dan proses instalasi yang lebih kompleks.

---

### 4.2.5 NyAI & Support Board

NyAI & Support Board merupakan platform chatbot yang dirancang dengan fokus utama pada layanan pelanggan (customer service) dan dukungan integrasi dengan berbagai saluran komunikasi seperti WhatsApp, Telegram, dan live chat. Pengujian awal terhadap platform ini menunjukkan bahwa NyAI tidak menyediakan opsi self-hosted secara penuh karena bergantung pada layanan cloud NyAI untuk pemrosesan AI-nya, sehingga kontrol penuh terhadap data tidak dapat terjamin.

Dari sisi agentic workflow, NyAI & Support Board tidak menyediakan kemampuan pembuatan AI Agent berbasis pipeline visual yang kompleks. Platform ini lebih berfokus pada respons otomatis berbasis aturan dan integrasi dengan model AI eksternal, bukan pada orkestrasi agent yang mandiri. Begitu pula dengan multi-agent & knowledge isolation, platform ini tidak mendukung pemisahan knowledge base antar departemen secara granular.

Satu-satunya kelebihan yang relevan dari platform ini adalah kemampuan embed ke dalam website dan antarmuka yang sudah siap pakai untuk layanan pelanggan. Namun, mengingat ketidaksesuaian pada kriteria self-hosted dan kebutuhan keamanan data instansi pemerintah, platform ini dinilai tidak tepat untuk diimplementasikan di lingkungan Diskominfo Kabupaten Sleman.

**Kelebihan:** Mudah dikonfigurasi untuk layanan pelanggan, mendukung multi-channel komunikasi, dan antarmuka yang ramah pengguna.

**Kekurangan:** Tidak mendukung self-hosted penuh, tidak memiliki agentic workflow, dan tidak mendukung isolasi knowledge base antar departemen.

---

## 4.3 Analisis Komparatif

Berdasarkan hasil evaluasi terhadap kelima platform, terdapat perbedaan yang signifikan dalam hal pemenuhan kriteria yang telah ditetapkan. Flowise dan RAGFlow menjadi dua platform yang paling banyak memenuhi kriteria evaluasi. Namun, Flowise memiliki keterbatasan pada aspek production ready, khususnya terkait manajemen pengguna yang belum enterprise-grade untuk lingkungan instansi pemerintah.

Dify, meskipun memiliki antarmuka yang modern dan fitur yang lengkap, memiliki kelemahan mendasar pada aspek multi-agent & knowledge isolation berdasarkan pengujian langsung. Ketidakmampuan Dify dalam mendukung isolasi knowledge base yang sesungguhnya dalam satu workspace menjadi faktor pembatas yang kritis dalam konteks implementasi chatbot multi-departemen.

Open WebUI cocok sebagai antarmuka chat sederhana berbasis LLM lokal, namun tidak memenuhi kebutuhan agentic workflow dan embeddable yang diperlukan untuk integrasi pada website resmi pemerintah. Sementara itu, NyAI & Support Board sepenuhnya tidak memenuhi kebutuhan self-hosted dan aspek teknis lainnya, sehingga langsung gugur dari pertimbangan.

RAGFlow menjadi satu-satunya platform yang memenuhi seluruh kriteria evaluasi secara penuh. Kemampuannya dalam mengelola knowledge base yang terisolasi per departemen, didukung oleh infrastruktur RAG tingkat lanjut dan fitur embed yang fleksibel, menjadikannya pilihan yang paling sesuai untuk implementasi chatbot layanan informasi di Diskominfo Kabupaten Sleman.

---

## 4.4 Implementasi RAGFlow di Diskominfo Kabupaten Sleman

Berdasarkan hasil evaluasi komparatif, RAGFlow dipilih sebagai platform yang diimplementasikan sebagai AI Chatbot layanan informasi di Dinas Komunikasi dan Informatika Kabupaten Sleman. Implementasi dilakukan melalui beberapa tahapan sebagai berikut.

**a. Instalasi dan Konfigurasi Infrastruktur**

RAGFlow diinstal pada lingkungan lokal menggunakan Docker Compose dengan konfigurasi layanan yang mencakup RAGFlow sebagai aplikasi utama, MySQL sebagai basis data relasional, MinIO sebagai object storage untuk dokumen, Redis sebagai in-memory cache, dan Infinity sebagai mesin pencarian vektor. Port layanan dikonfigurasi ulang (remapping) untuk menghindari konflik dengan layanan lain yang telah berjalan di server.

**b. Integrasi Model AI Lokal**

RAGFlow dikonfigurasi untuk menggunakan model AI lokal melalui Ollama, yaitu model Llama 3.2 (3B parameter) sebagai Large Language Model untuk menghasilkan jawaban, dan model nomic-embed-text sebagai Embedding Model untuk proses vektorisasi dokumen. Penggunaan model lokal ini memastikan seluruh pemrosesan data dilakukan di dalam infrastruktur internal tanpa mengirimkan data ke server eksternal (air-gapped deployment).

**c. Pengelolaan Knowledge Base**

Knowledge base dibuat secara terpisah untuk setiap departemen. Dataset Kominfo Sleman berisi informasi profil organisasi, tugas dan fungsi, serta layanan yang tersedia, sedangkan dataset DUKCAPIL berisi informasi mengenai persyaratan dan prosedur administrasi kependudukan. Dokumen-dokumen tersebut diunggah dan diproses menggunakan fitur Deep Document Understanding RAGFlow untuk memastikan kualitas ekstraksi teks yang optimal.

**d. Konfigurasi Chatbot dan Embed**

Chatbot dikonfigurasi dengan system prompt yang mendefinisikan peran sebagai asisten layanan informasi Pemerintah Kabupaten Sleman dengan persona yang profesional dan empatik. Similarity threshold diatur secara ketat untuk mencegah AI memberikan jawaban di luar cakupan dokumen yang tersedia (hallucination prevention). Chatbot kemudian di-embed ke dalam halaman web melalui fitur embed RAGFlow yang menghasilkan kode iframe siap pakai.

**e. Pengujian dan Validasi**

Pengujian dilakukan menggunakan skenario percakapan multi-turn yang mencakup pertanyaan mengenai layanan pemerintahan, prosedur administrasi kependudukan, dan informasi umum Kabupaten Sleman. Hasil pengujian menunjukkan bahwa chatbot mampu memberikan jawaban yang akurat dan relevan berdasarkan isi knowledge base, serta menolak pertanyaan di luar cakupan dengan respons yang sopan.

---

## 4.5 Rekomendasi

Berdasarkan hasil evaluasi komparatif dan implementasi yang telah dilakukan, rekomendasi platform AI Chatbot untuk instansi pemerintah adalah sebagai berikut.

**RAGFlow** direkomendasikan sebagai platform utama untuk implementasi AI Chatbot layanan informasi di instansi pemerintah, khususnya yang memiliki kebutuhan multi-departemen dengan isolasi data yang ketat. Kemampuan RAG tingkat lanjut, arsitektur multi-tenant yang matang, serta dukungan deployment air-gapped menjadikan RAGFlow sebagai solusi yang paling sesuai untuk lingkungan pemerintahan yang mengutamakan keamanan dan kedaulatan data.

**Flowise** dapat direkomendasikan sebagai alternatif apabila instansi membutuhkan fleksibilitas tinggi dalam membangun alur kerja AI yang kompleks dan memiliki tim teknis yang mampu mengelola konfigurasi agent secara mendalam.

**Dify** dan **Open WebUI** dapat dipertimbangkan untuk kebutuhan yang lebih sederhana, namun tidak direkomendasikan untuk implementasi multi-departemen yang memerlukan isolasi knowledge base yang ketat.

**NyAI & Support Board** tidak direkomendasikan untuk implementasi pada instansi pemerintah yang memiliki persyaratan self-hosted dan keamanan data yang tinggi.
