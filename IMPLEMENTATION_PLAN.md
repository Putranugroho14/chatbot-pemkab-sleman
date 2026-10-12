# Implementation Plan: Laravel + RAGFlow Multi-Tenant Chatbot
**Project:** AI Chatbot Layanan Informasi Pemkab Sleman  
**Deadline:** 31 Oktober 2026  
**Status:** Planning Phase  
**Date Created:** 11 Oktober 2026

---

## 🎯 Problem Statement

### Masalah Platform yang Sudah Dievaluasi:

| Platform | Issue |
|----------|-------|
| **Flowise** | Production ready kurang (RBAC enterprise dikunci) |
| **Dify** | Multi-tenant isolation kurang ketat |
| **Open WebUI** | Bukan agentic workflow, no embed widget |
| **Langflow** | **FATAL: No RBAC** - Admin bisa lihat semua flow |
| **Botpress** | v12 sunset, modern version cloud-only |
| **RAGFlow** | ❌ **TIDAK ADA SUPER ADMIN** - hanya flat structure (1 user = 1 agent) |
| **AnythingLLM** | RBAC kurang granular, agentic workflow simple |

### Kriteria yang HARUS Dipenuhi:

1. ✅ **Self-hosted** - Install di server sendiri, 100% offline/air-gapped
2. ✅ **Agentic workflow** - Support AI Agent kompleks
3. ✅ **Multi-user** - Banyak user bisa pakai
4. ✅ **Multi-agent** - Banyak agent berbeda
5. ✅ **1 Agent = 1 Knowledge Base TERISOLASI** - Knowledge tidak bocor antar dinas
6. ✅ **Fitur chat** - Interface chat
7. ✅ **Embeddable** - Bisa di-embed ke website
8. ✅ **RBAC 3-Level yang KETAT:**
   - **Super Admin** (IT Diskominfo) - Lihat & manage SEMUA agent
   - **Dinas Admin** - Hanya akses agent dinas sendiri
   - **Staff** - Chat only
9. ✅ **Production ready** - Stable & reliable
10. ✅ **Resource efficient** - Tidak se-heavy RAGFlow standalone

---

## ✅ Solution: Hybrid Architecture (Laravel + RAGFlow)

### Architecture Overview:

```
┌─────────────────────────────────────────────────────┐
│          Laravel Admin Dashboard                    │
│            (SUPER ADMIN LAYER)                      │
│                                                     │
│  🔐 RBAC 3-Level (Laravel Native):                 │
│  ┌────────────────────────────────────────┐        │
│  │ Super Admin (IT Diskominfo)            │        │
│  │  └── Full access semua dinas           │        │
│  │      - Manage all users                │        │
│  │      - Manage all agents               │        │
│  │      - View all analytics              │        │
│  └────────────────────────────────────────┘        │
│              │                                      │
│  ┌───────────┼───────────┬─────────────┐          │
│  │           │           │             │          │
│  ▼           ▼           ▼             ▼          │
│  Admin      Admin       Admin         Staff       │
│  Kominfo    Pariwisata  DUKCAPIL      (All)       │
│  (Isolated) (Isolated)  (Isolated)    (Read)      │
└──────────────┬──────────────────────────────────────┘
               │ (API Proxy dengan Auth Token)
               │ Laravel routing berdasarkan user.dinas
               ▼
┌─────────────────────────────────────────────────────┐
│              RAGFlow Backend                        │
│           (AI Engine - No RBAC)                     │
│                                                     │
│  1 Master Account RAGFlow:                         │
│  ├── Agent Kominfo (agent_id: xxx)                 │
│  │   └── Knowledge Base: kominfo_sleman.txt        │
│  ├── Agent Pariwisata (agent_id: yyy)              │
│  │   └── Knowledge Base: pariwisata_sleman.txt     │
│  └── Agent DUKCAPIL (agent_id: zzz)                │
│      └── Knowledge Base: dukcapil_sleman.txt       │
└──────────────┬──────────────────────────────────────┘
               │
               ▼
           Ollama (Llama 3.2 3B)
```

### Key Concept:

- ✅ **Laravel = RBAC Layer** (3-level hierarchy yang RAGFlow tidak punya)
- ✅ **RAGFlow = AI Engine** (1 master account, semua agent di manage dari sini)
- ✅ **Laravel middleware** check permission sebelum proxy ke RAGFlow
- ✅ **Mapping:** `user.dinas` → `ragflow.agent_id`

---

## 📅 Implementation Timeline (20 Hari)

### Phase 1: Database & RBAC Foundation (Day 1-2)
**Task #1: Setup database schema untuk RBAC 3-level**

**Migration:**
```php
// database/migrations/xxxx_add_rbac_to_users_table.php
Schema::table('users', function (Blueprint $table) {
    $table->enum('role', ['super_admin', 'dinas_admin', 'staff'])
          ->default('staff')
          ->after('password');
    $table->enum('dinas', ['kominfo', 'pariwisata', 'dukcapil'])
          ->nullable()
          ->after('role');
    $table->index(['role', 'dinas']);
});
```

**Update User Model:**
```php
// app/Models/User.php
#[Fillable(['name', 'email', 'password', 'role', 'dinas'])]
class User extends Authenticatable {
    
    public function isSuperAdmin(): bool {
        return $this->role === 'super_admin';
    }
    
    public function isDinasAdmin(): bool {
        return $this->role === 'dinas_admin';
    }
    
    public function isStaff(): bool {
        return $this->role === 'staff';
    }
    
    public function canAccessDinas(string $dinas): bool {
        if ($this->isSuperAdmin()) {
            return true; // Super admin akses semua
        }
        return $this->dinas === $dinas;
    }
    
    public function canManageAgent(string $agentDinas): bool {
        if ($this->isSuperAdmin()) {
            return true;
        }
        if ($this->isDinasAdmin() && $this->dinas === $agentDinas) {
            return true;
        }
        return false;
    }
}
```

---

### Phase 2: Middleware & Policies (Day 3-4)
**Task #2: Implement RBAC middleware dan policies**

**Middleware:**
```php
// app/Http/Middleware/CheckDinasAccess.php
class CheckDinasAccess {
    public function handle(Request $request, Closure $next, string $requiredRole = null) {
        $user = $request->user();
        $requestedDinas = $request->input('dinas') ?? $request->route('dinas');
        
        // Super admin bypass all checks
        if ($user->isSuperAdmin()) {
            return $next($request);
        }
        
        // Check role requirement
        if ($requiredRole && $user->role !== $requiredRole) {
            abort(403, 'Akses ditolak: Role tidak sesuai');
        }
        
        // Check dinas access
        if ($requestedDinas && !$user->canAccessDinas($requestedDinas)) {
            abort(403, 'Akses ditolak: Anda tidak memiliki akses ke dinas ini');
        }
        
        return $next($request);
    }
}
```

**Register Middleware:**
```php
// bootstrap/app.php atau app/Http/Kernel.php
protected $middlewareAliases = [
    'dinas.access' => \App\Http\Middleware\CheckDinasAccess::class,
];
```

---

### Phase 3: RAGFlow Integration (Day 5-6)
**Task #4: Connect Laravel ke RAGFlow API**

**Update .env:**
```env
# RAGFlow Configuration
RAGFLOW_API_URL=http://localhost:9380/api/v1
RAGFLOW_API_TOKEN=your_ragflow_master_token

# Agent ID Mapping
RAGFLOW_AGENT_KOMINFO=agent_kominfo_id_xxx
RAGFLOW_AGENT_PARIWISATA=agent_pariwisata_id_yyy
RAGFLOW_AGENT_DUKCAPIL=agent_dukcapil_id_zzz
```

**Create RAGFlow Service:**
```php
// app/Services/RAGFlowService.php
class RAGFlowService {
    
    private string $apiUrl;
    private string $apiToken;
    
    public function __construct() {
        $this->apiUrl = config('services.ragflow.api_url');
        $this->apiToken = config('services.ragflow.api_token');
    }
    
    public function chat(string $agentId, string $message, ?string $conversationId = null): array {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiToken,
            'Content-Type' => 'application/json'
        ])->timeout(60)->post("{$this->apiUrl}/chats", [
            'agent_id' => $agentId,
            'message' => $message,
            'conversation_id' => $conversationId,
            'user_id' => auth()->id(),
        ]);
        
        if (!$response->successful()) {
            throw new \Exception('RAGFlow API Error: ' . $response->body());
        }
        
        return $response->json();
    }
    
    public function getAgentIdForDinas(string $dinas): string {
        $mapping = [
            'kominfo' => config('services.ragflow.agent_kominfo'),
            'pariwisata' => config('services.ragflow.agent_pariwisata'),
            'dukcapil' => config('services.ragflow.agent_dukcapil'),
        ];
        
        return $mapping[$dinas] ?? throw new \Exception("Agent not found for dinas: {$dinas}");
    }
}
```

---

### Phase 4: Update ChatbotController (Day 10)
**Task #3: Update ChatbotController dengan permission checking**

```php
// app/Http/Controllers/ChatbotController.php
class ChatbotController extends Controller {
    
    protected RAGFlowService $ragflow;
    
    public function __construct(RAGFlowService $ragflow) {
        $this->middleware('auth');
        $this->middleware('dinas.access')->only(['chat', 'history']);
        $this->ragflow = $ragflow;
    }
    
    public function chat(Request $request) {
        $request->validate([
            'message' => 'required|string|max:2000',
            'dinas' => 'required|in:kominfo,pariwisata,dukcapil',
            'conversation_id' => 'nullable|string'
        ]);
        
        $user = $request->user();
        $dinas = $request->dinas;
        
        // Permission check
        if (!$user->canAccessDinas($dinas)) {
            return response()->json([
                'error' => 'Akses ditolak: Anda tidak memiliki akses ke dinas ini'
            ], 403);
        }
        
        try {
            // Get agent ID for dinas
            $agentId = $this->ragflow->getAgentIdForDinas($dinas);
            
            // Call RAGFlow API
            $response = $this->ragflow->chat(
                $agentId,
                $request->message,
                $request->conversation_id
            );
            
            // Save to chat history
            ChatHistory::create([
                'user_id' => $user->id,
                'dinas' => $dinas,
                'question' => $request->message,
                'answer' => $response['answer'],
                'conversation_id' => $response['conversation_id'],
                'ip_address' => $request->ip(),
            ]);
            
            return response()->json([
                'answer' => $response['answer'],
                'conversation_id' => $response['conversation_id']
            ]);
            
        } catch (\Exception $e) {
            Log::error('RAGFlow Chat Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
```

---

### Phase 5: Super Admin Dashboard (Day 7-8)
**Task #5: Build Super Admin dashboard**

**Features:**
- View all agents & statistics
- Manage all users (CRUD)
- Assign role & dinas
- View chat history dari semua dinas
- System monitoring

**Route:**
```php
// routes/web.php
Route::middleware(['auth', 'dinas.access:super_admin'])->prefix('superadmin')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('superadmin.dashboard');
    Route::resource('users', UserManagementController::class);
    Route::get('/agents', [SuperAdminController::class, 'agents'])->name('superadmin.agents');
    Route::get('/analytics', [SuperAdminController::class, 'analytics'])->name('superadmin.analytics');
    Route::get('/chat-history', [SuperAdminController::class, 'allChatHistory'])->name('superadmin.chat-history');
});
```

---

### Phase 6: Dinas Admin Dashboard (Day 8-9)
**Task #6: Build Dinas Admin dashboard**

**Features:**
- View agent dinas sendiri only
- Manage staff di dinas sendiri
- Upload dokumen ke KB dinas sendiri
- View chat history dinas sendiri only

**Route:**
```php
// routes/web.php
Route::middleware(['auth', 'dinas.access:dinas_admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DinasAdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/staff', [DinasAdminController::class, 'staff'])->name('admin.staff');
    Route::post('/knowledge-base/upload', [DinasAdminController::class, 'uploadDocument'])->name('admin.kb.upload');
    Route::get('/chat-history', [DinasAdminController::class, 'chatHistory'])->name('admin.chat-history');
});
```

---

### Phase 7: Embed Widget (Day 11)
**Task #7: Create embed widget generator**

**Widget Endpoint:**
```php
// routes/web.php
Route::get('/embed/{dinas}', [EmbedController::class, 'widget'])->name('embed.widget');
Route::post('/embed/{dinas}/chat', [EmbedController::class, 'chat'])->name('embed.chat');
```

**Widget HTML:**
```html
<!-- resources/views/embed/widget.blade.php -->
<div id="chatbot-widget" data-dinas="{{ $dinas }}">
    <div class="chatbot-container">
        <div class="chatbot-header">
            <h3>Chatbot {{ ucfirst($dinas) }}</h3>
        </div>
        <div class="chatbot-messages"></div>
        <div class="chatbot-input">
            <input type="text" placeholder="Ketik pesan...">
            <button>Kirim</button>
        </div>
    </div>
</div>

<script>
// Widget JavaScript untuk handle chat
// Isolated per dinas, responsive, customizable
</script>
```

---

### Phase 8: Database Seeder (Day 12)
**Task #8: Create database seeder untuk test users**

```php
// database/seeders/UserSeeder.php
class UserSeeder extends Seeder {
    public function run(): void {
        // Super Admin
        User::create([
            'name' => 'Super Admin IT Diskominfo',
            'email' => 'superadmin@slemankab.go.id',
            'password' => Hash::make('SuperAdmin123!'),
            'role' => 'super_admin',
            'dinas' => null,
        ]);
        
        // Admin Kominfo
        User::create([
            'name' => 'Admin Kominfo',
            'email' => 'admin.kominfo@slemankab.go.id',
            'password' => Hash::make('AdminKominfo123!'),
            'role' => 'dinas_admin',
            'dinas' => 'kominfo',
        ]);
        
        // Admin Pariwisata
        User::create([
            'name' => 'Admin Pariwisata',
            'email' => 'admin.pariwisata@slemankab.go.id',
            'password' => Hash::make('AdminPariwisata123!'),
            'role' => 'dinas_admin',
            'dinas' => 'pariwisata',
        ]);
        
        // Admin DUKCAPIL
        User::create([
            'name' => 'Admin DUKCAPIL',
            'email' => 'admin.dukcapil@slemankab.go.id',
            'password' => Hash::make('AdminDukcapil123!'),
            'role' => 'dinas_admin',
            'dinas' => 'dukcapil',
        ]);
        
        // Staff Kominfo
        User::create([
            'name' => 'Staff Kominfo',
            'email' => 'staff.kominfo@slemankab.go.id',
            'password' => Hash::make('StaffKominfo123!'),
            'role' => 'staff',
            'dinas' => 'kominfo',
        ]);
    }
}
```

---

### Phase 9: Testing (Day 13-15)
**Task #9: Testing & debugging full system**

**Test Scenarios:**

1. **Role-Based Access Testing:**
   - ✅ Super Admin bisa lihat & manage semua
   - ✅ Admin Kominfo TIDAK bisa akses Pariwisata/DUKCAPIL
   - ✅ Admin Pariwisata TIDAK bisa akses Kominfo/DUKCAPIL
   - ✅ Staff hanya bisa chat, tidak bisa manage

2. **Knowledge Isolation Testing:**
   - ✅ Chat ke agent Kominfo hanya dapat jawaban dari KB Kominfo
   - ✅ Chat ke agent Pariwisata hanya dapat jawaban dari KB Pariwisata
   - ✅ Tidak ada knowledge leakage antar dinas

3. **Embed Widget Testing:**
   - ✅ Widget di website Kominfo hanya akses agent Kominfo
   - ✅ Widget responsive di mobile
   - ✅ Widget bisa di-customize

4. **Security Testing:**
   - ✅ Unauthorized access ditolak (403)
   - ✅ SQL injection prevention
   - ✅ XSS prevention
   - ✅ CSRF protection

5. **Performance Testing:**
   - ✅ Response time < 3 detik
   - ✅ Concurrent users (50+)
   - ✅ Memory leak check

---

### Phase 10: Deployment & Documentation (Day 16-18)
**Task #10: Deploy dan dokumentasi**

**Deployment Checklist:**
- [ ] Setup production .env
- [ ] Migrate database
- [ ] Seed users
- [ ] Configure RAGFlow agents
- [ ] Setup reverse proxy (Nginx)
- [ ] SSL certificate
- [ ] Firewall rules
- [ ] Backup strategy

**Documentation:**

1. **User Guide:**
   - Login & authentication
   - Dashboard per role
   - Chat dengan agent
   - Upload dokumen (untuk admin)

2. **Admin Guide:**
   - User management
   - Agent configuration
   - Knowledge base maintenance
   - Monitoring & analytics

3. **Technical Documentation:**
   - Architecture diagram
   - API documentation
   - Database schema
   - Deployment guide

4. **Laporan Magang:**
   - BAB 4: Implementation
   - Hasil testing
   - Screenshots
   - Kesimpulan

---

## 📊 Risk Assessment

| Risk | Probability | Impact | Mitigation |
|------|-------------|--------|------------|
| RAGFlow API changes | Low | High | Version pinning, abstraction layer |
| Performance issues | Medium | Medium | Caching, load testing early |
| Security vulnerabilities | Medium | High | Security audit, Laravel best practices |
| Knowledge leakage | Low | Critical | Strict middleware checks, audit logs |
| Deployment delays | Medium | High | Early staging deployment |

---

## ✅ Success Criteria

1. ✅ **Super Admin bisa manage semua agent & users**
2. ✅ **Dinas Admin hanya akses dinas sendiri (isolated)**
3. ✅ **Staff hanya bisa chat (read-only)**
4. ✅ **Knowledge base terisolasi per dinas (no leakage)**
5. ✅ **Embed widget berjalan di website masing-masing dinas**
6. ✅ **Production-ready by 31 Oktober 2026**
7. ✅ **Dokumentasi lengkap untuk laporan magang**

---

## 📝 Notes & Decisions

### Design Decisions:

1. **Why Laravel + RAGFlow hybrid?**
   - RAGFlow punya advanced agentic workflow & knowledge isolation
   - RAGFlow TIDAK punya RBAC hierarchy (flat structure)
   - Laravel sebagai RBAC layer menyelesaikan masalah ini
   - Laravel proxy request ke RAGFlow berdasarkan user permission

2. **Why not custom build (LangGraph)?**
   - Development time 2-4 minggu (too risky dengan deadline 20 hari)
   - RAGFlow sudah production-ready, tinggal wrapper Laravel

3. **Why not other platforms?**
   - Semua platform lain gagal memenuhi kriteria RBAC 3-level
   - Langflow: No RBAC (fatal)
   - Dify: Multi-tenant isolation kurang
   - Botpress: Modern version cloud-only

### Technical Decisions:

1. **Database:** MySQL (existing)
2. **Auth:** Laravel Breeze (existing)
3. **Frontend:** Blade + Tailwind (simple, fast)
4. **API:** RESTful (Laravel standard)
5. **Deployment:** Docker (RAGFlow) + PHP-FPM (Laravel)

---

## 🚀 Next Steps

**Immediate Actions (Day 1):**
1. [ ] Setup RAGFlow dengan 3 agent (Kominfo, Pariwisata, DUKCAPIL)
2. [ ] Test RAGFlow API dengan curl/Postman
3. [ ] Get RAGFlow agent IDs untuk masing-masing dinas
4. [ ] Start Task #1: Database migration

**Questions to Resolve:**
- [ ] Apakah RAGFlow sudah running?
- [ ] Sudah ada agent untuk 3 dinas?
- [ ] Sudah ada knowledge base di masing-masing agent?

---

**Last Updated:** 11 Oktober 2026, 05:24 UTC  
**Status:** Draft - Waiting for confirmation to start implementation
