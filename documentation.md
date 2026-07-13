# My Wallet — Dokumentasi Proyek

> **Aplikasi:** Manajemen Keuangan Pribadi  
> **Framework:** CodeIgniter 3 (PHP 7.4+)  
> **Database:** MySQL  
> **UI:** Neubrutalism Design — Mobile-first (480px)  
> **Author:** Dhaby Anggika Putra  
> **Live:** [https://finance.abysoft.my.id](https://finance.abysoft.my.id)

---

## Daftar Isi

1. [Arsitektur Aplikasi](#1-arsitektur-aplikasi)
2. [Struktur Folder](#2-struktur-folder)
3. [Database](#3-database)
4. [Fitur-Fitur](#4-fitur-fitur)
   - [4.1 Authentication](#41-authentication)
   - [4.2 Dashboard](#42-dashboard)
   - [4.3 Transaksi CRUD](#43-transaksi-crud)
   - [4.4 AI Chat](#44-ai-chat)
   - [4.5 Import CSV/Excel](#45-import-csvexcel)
   - [4.6 Admin Panel](#46-admin-panel)
   - [4.7 Statistik](#47-statistik)
5. [Alur Kode Penting](#5-alur-kode-penting)
   - [5.1 Alur Login](#51-alur-login)
   - [5.2 Alur Chat + Konfirmasi](#52-alur-chat--konfirmasi)
   - [5.3 Alur AI Extraction](#53-alur-ai-extraction)
6. [Keamanan](#6-keamanan)
7. [Cara Install](#7-cara-install)

---

## 1. Arsitektur Aplikasi

```
User → Auth (login/register)
     → Dashboard   (CRUD transaksi, stats, profile)
     → Chat        (AI-assisted transaksi)
     → Import      (CSV/Excel batch import)
     → Admin       (manajemen user & kategori)
```

**Lapisan:** MVC CodeIgniter 3

| Layer | Teknologi |
|-------|-----------|
| **Controller** | PHP — `Dashboard.php`, `Auth.php`, `Chat.php`, `Admin.php`, `Import.php` |
| **Model** | PHP — `Transaction_model.php`, `User_model.php`, `Chat_model.php` |
| **View** | PHP + Bootstrap 5 + CSS Neubrutalism |
| **Library** | `TransactionExtractor` (regex parser), `CategoryClassifier` (AI+rules), `AiRouter/Gemini/Groq` |
| **Frontend** | Swup (page transitions), Chart.js, jQuery, Icon inline SVG |
| **Database** | MySQL — 5 tables: `users`, `categories`, `transactions`, `chat_threads`, `chat_messages` |

---

## 2. Struktur Folder

```
finance_app/
├── application/
│   ├── config/          # Konfigurasi (app, DB, routes, AI, session, CSRF, CSP)
│   ├── controllers/     # Dashboard, Auth, Chat, Admin, Import
│   ├── libraries/       # TransactionExtractor, CategoryClassifier, Validator
│   │   └── ai/          # AiRouter, GeminiProvider, GroqProvider
│   ├── models/          # Transaction_model, User_model, Chat_model
│   └── views/           # templates/ (header, footer), dashboard/, chat/, auth/, admin/
├── assets/
│   ├── css/style.css    # Neubrutalism design system
│   └── js/              # page-init.js, admin-init.js, import-init.js
├── sql/                 # Migrasi database
├── vendor/              # Composer dependencies (phpdotenv, phpspreadsheet)
├── writable/            # Session data, uploads
├── .htaccess            # Rewrite, CSP header, security blocks
├── index.php            # Front controller + .env loader
└── composer.json        # PHP dependencies
```

---

## 3. Database

### `users`
| Field | Type | Keterangan |
|-------|------|------------|
| id | INT (PK) | Auto increment |
| name | VARCHAR(255) | Nama user |
| username | VARCHAR(255) | Unique login |
| password | VARCHAR(255) | `password_hash(PASSWORD_DEFAULT)` |
| role | ENUM('user','admin') | Default 'user' |
| goal_amount | DECIMAL(15,2) | Target financial freedom |
| created_at | TIMESTAMP | |

### `categories`
| Field | Type |
|-------|------|
| id | INT (PK) |
| user_id | INT (nullable — global jika null) |
| name | VARCHAR(100) |
| type | ENUM('income','expense') |

### `transactions`
| Field | Type |
|-------|------|
| id | INT (PK) |
| user_id | INT (FK → users) |
| title | VARCHAR(255) |
| amount | DECIMAL(15,2) |
| type | ENUM('income','expense') |
| category | VARCHAR(100) |
| payee | VARCHAR(255) |
| transaction_date | DATE |

### `chat_threads`
| Field | Type |
|-------|------|
| id | INT (PK) |
| user_id | INT (FK → users ON DELETE CASCADE) |
| title | VARCHAR(255) — 'default' untuk single thread |

### `chat_messages`
| Field | Type |
|-------|------|
| id | INT (PK) |
| thread_id | INT (FK → chat_threads ON DELETE CASCADE) |
| role | ENUM('user','assistant','system') |
| content | TEXT |
| meta_json | LONGTEXT — menyimpan draft transaksi, status confirmed |

---

## 4. Fitur-Fitur

### 4.1 Authentication

**Controller:** `Auth.php`

| Method | Deskripsi |
|--------|-----------|
| `login()` | Validasi username+password, rate limit 5x/5menit, regenerasi session |
| `register()` | Validasi unique username, min password 8 char, hash bcrypt |
| `logout()` | Hapus session, destroy |

**Keamanan login:**
```php
// Rate limiting — maks 5 percobaan, lockout 5 menit
$attempts = $this->session->userdata('login_attempts') ?: 0;
if ($attempts >= 5) {
    $lockout = $this->session->userdata('login_lockout');
    if ($lockout && (time() - $lockout) < 300) {
        // Tampilkan pesan lockout
    }
}

// Verifikasi pakai password_verify (bukan md5/sha1)
if (password_verify($password, $user['password'])) {
    // Login sukses — reset attempts, regenerasi session
    $this->session->unset_userdata(['login_attempts', 'login_lockout']);
    $this->session->sess_regenerate();
}
```

---

### 4.2 Dashboard

**Controller:** `Dashboard.php` — `index()`  
**View:** `views/dashboard/index.php`

Menampilkan:
- **Total Balance** — Income - Expense
- **Financial Freedom Goal** — Progress bar (goal_amount vs current savings)
- **Income card** — Total bulan ini
- **Expense card** — Total bulan ini
- **Recent Activity** — 5 transaksi terakhir

```php
// Dashboard.php — get balance
$data['total_income'] = $this->Transaction_model->get_total_income($user_id);
$data['total_expense'] = $this->Transaction_model->get_total_expense($user_id);
$data['balance'] = $data['total_income'] - $data['total_expense'];
$data['recent'] = $this->Transaction_model->get_transactions($user_id, 5);
$data['goal_percentage'] = ($goal > 0) ? min(100, round(($balance / $goal) * 100)) : 0;
```

---

### 4.3 Transaksi CRUD

**Controller:** `Dashboard.php`

| Method | Deskripsi |
|--------|-----------|
| `add()` | Tambah transaksi — support prefill dari query params (AI Chat Edit flow) |
| `detail($id)` | Lihat detail — cek kepemilikan |
| `delete($id)` | Hapus — method POST, cek kepemilikan |
| `transactions()` | Daftar paginasi (20/page), filter tanggal/bulan/tahun, AJAX Load More |

```php
// Add transaction — strip dots from amount (format Indonesia)
$amount = str_replace('.', '', $this->input->post('amount'));
$data = [
    'user_id' => $user_id,
    'title' => $this->input->post('title'),
    'amount' => $amount,
    'type' => $this->input->post('type'),
    'category' => $final_category, // Auto-create jika belum ada
    'payee' => $this->input->post('payee'),
    'transaction_date' => $this->input->post('transaction_date'),
];
$this->Transaction_model->add_transaction($data);
```

---

### 4.4 AI Chat

**Controller:** `Chat.php`  
**Library:** `TransactionExtractor`, `CategoryClassifier`, `AiRouter` / `GeminiProvider` / `GroqProvider`  
**View:** `views/chat/index.php`

Ini adalah fitur paling kompleks. Alur lengkap:

#### Flow Chat

```
User ketik: "beli kopi 25k kemarin"
    │
    ▼
1. Dedup cache — hash SHA256, reuse hasil jika sama
    │
    ▼
2. Simpan pesan user ke DB (chat_messages)
    │
    ▼
3. TransactionExtractor::extract() — REGEX PARSER (gratis, tanpa AI)
    │  - guess_type()   → "expense" (matching keyword "beli")
    │  - parse_amount() → 25000 (parse "25k")
    │  - parse_date()   → "2026-07-12" (parse "kemarin")
    │  - guess_title()  → "kopi kemarin"
    │
    ▼
4. [Optional] LLM Fallback — hanya jika AI_ENABLED=true DAN intent=clarify
    │  - GeminiProvider (primary) atau GroqProvider (fallback)
    │  - Prompt: strict JSON dengan daftar kategori yang diizinkan
    │
    ▼
5. CategoryClassifier — auto category
    │  - AI-first (jika enabled, pilih dari allowed list)
    │  - Fallback: keyword rules (makan→Food, bensin→Transport)
    │  - Last resort: "Others"
    │
    ▼
6. Simpan response assistant ke DB (dengan meta_json = draft)
    │
    ▼
7. Tampilkan DRAFT CARD ke user:
       ┌─────────────────────────┐
       │  DRAFT                  │
       │  Type: expense          │
       │  Amount: Rp 25.000      │
       │  Date: 2026-07-12       │
       │  Category: Food         │
       │  Title: kopi kemarin    │
       │  Payee: -               │
       │                         │
       │  [CONFIRM]  [EDIT]      │
       └─────────────────────────┘
```

#### Konfirmasi Transaksi

```javascript
// Frontend — confirmDraft()
window.confirmDraft = function(threadId, draft) {
    $.ajax({
        url: base_url + 'chat/confirm/' + threadId,
        method: 'POST',
        data: {
            type: draft.type,
            title: draft.title,
            amount: draft.amount,
            category: draft.category,
            payee: draft.payee,
            transaction_date: draft.transaction_date
        },
        success: function(res) {
            // Update UI: ganti tombol jadi "✓ SAVED"
            $container.find('.font-mono.fw-bold.mb-2').text('✓ SAVED');
            $container.find('.d-flex.gap-2').remove();
        }
    });
};
```

```php
// Backend — Chat::confirm()
$data = [
    'user_id' => $user_id,
    'title' => $title,
    'amount' => $amount,
    'type' => $type,
    'category' => $final_category,
    'payee' => $payee,
    'transaction_date' => $date,
];
$this->Transaction_model->add_transaction($data);

// Mark draft as confirmed
$this->db->set('meta_json', $updatedMeta);
$this->db->where('id', $lastAssistantMsg['id']);
$this->db->update('chat_messages');
```

---

### 4.5 Import CSV/Excel

**Controller:** `Import.php`  
**Library:** PhpSpreadsheet

| Step | Method | Deskripsi |
|------|--------|-----------|
| 1 | `index()` | Upload form (CSV, max 2MB) |
| 2 | `upload()` | Validasi MIME, simpan ke `writable/uploads/` |
| 3 | `map()` | Baca file, deteksi kolom aktif, tampilkan preview untuk mapping |
| 4 | `preview()` | Parse baris, mapping kolom (date, title, amount, type, payee), auto-kategori |
| 5 | `process()` | Batch insert via `insert_batch()`, auto-create kategori, cleanup file |

```php
// Date parser — handle berbagai format
function _parse_date($value) {
    if (is_numeric($value)) {
        // Excel serial date
        return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
    }
    // String format: Y-m-d, d/m/Y, d-m-Y, etc.
    return date('Y-m-d', strtotime($value));
}
```

---

### 4.6 Admin Panel

**Controller:** `Admin.php`

**Triple gate authentication:**
```php
// 1. Must be logged in
if (!$this->session->userdata('user_id')) redirect('auth/login');
// 2. Must have role=admin
if ($this->session->userdata('role') !== 'admin') show_404();
// 3. Must pass secret key challenge (kecuali di halaman login)
if (!$this->session->userdata('admin_authorized')) ...
```

**Admin login** menggunakan secret key (bukan password user):
```php
// Timing-safe comparison
if (hash_equals($admin_secret, $input_key)) {
    $this->session->set_userdata('admin_authorized', true);
}
```

| Method | Deskripsi |
|--------|-----------|
| `login()` | Secret key challenge, rate limit 5x/5menit |
| `index()` | Dashboard admin — statistik user, transaksi, kategori |
| `users()` | Daftar semua user |
| `categories()` | CRUD kategori global |
| `add_category()` | Tambah kategori |
| `edit_category($id)` | Edit kategori |
| `delete_category($id)` | Hapus kategori |

---

### 4.7 Statistik

**Controller:** `Dashboard.php` — `stats()`  
**View:** `views/dashboard/stats.php`

Fitur analitik keuangan:

| Metrik | Rumus |
|--------|-------|
| Monthly Summary | GROUP BY month, SUM income & expense |
| Average Income | Rata-rata income per bulan (6 bulan terakhir) |
| Average Expense | Rata-rata expense per bulan |
| Saving Rate | `((income - expense) / income) * 100` |
| Emergency Fund | `6 × avg_expense` — dana darurat ideal |
| Freedom Timeline | Berapa bulan lagi mencapai goal_amount |

```php
$monthly = $this->Transaction_model->get_monthly_summary($user_id, 6);
$avgIncome = array_sum(array_column($monthly, 'income')) / max(1, count($monthly));
$avgExpense = array_sum(array_column($monthly, 'expense')) / max(1, count($monthly));
$savingRate = ($avgIncome > 0) ? (($avgIncome - $avgExpense) / $avgIncome) * 100 : 0;
$emergencyFund = 6 * $avgExpense;
$freedomMonths = ($monthlySaving > 0) ? $remainingGoal / $monthlySaving : INF;
```

---

## 5. Alur Kode Penting

### 5.1 Alur Login

```
POST /auth/login
    │
    ▼
Auth::login()
    │
    ├── Cek session: sudah login? → redirect dashboard
    │
    ├── Rate limit: >5 attempts dalam 5 menit? → "Terlalu banyak percobaan"
    │
    ├── Validasi: username required, password required
    │
    ├── User_model::get_user_by_username($username)
    │
    ├── password_verify($password, $user['password'])
    │     │
    │     ├── Gagal → increment attempts, tampilkan error
    │     │
    │     └── Sukses →
    │           • Unset session attempts
    │           • sess_regenerate()
    │           • Set session: user_id, username, name, role
    │           • redirect dashboard
    │
    └── View: auth/login.php (dengan CSRF hidden input)
```

### 5.2 Alur Chat + Konfirmasi

```
                        CHAT FLOW
┌─────────────────────────────────────────────────────┐
│                                                     │
│   User                                           AI │
│   │                                                │
│   ├── "beli kopi 25k"                              │
│   │   │                                            │
│   │   ▼                                            │
│   │   POST /chat/send/1  ─────────────────────────► │
│   │                                                 │
│   │   TransactionExtractor::extract("beli kopi 25k")│
│   │     ├── guess_type()    → "expense"             │
│   │     ├── parse_amount()  → 25000                 │
│   │     ├── parse_date()    → "2026-07-13"          │
│   │     └── guess_title()   → "kopi"               │
│   │                                                 │
│   │◄── "Aku buat draft transaksi. Cek dulu ya"      │
│   │                                                 │
│   │           ┌──────────────────────┐              │
│   │           │ DRAFT                │              │
│   │           │ Amount: Rp 25.000    │              │
│   │           │ [CONFIRM]  [EDIT]    │              │
│   │           └──────────────────────┘              │
│   │                                                 │
│   ├── Klik CONFIRM                                  │
│   │   │                                             │
│   │   ▼                                            │
│   │   POST /chat/confirm/1  ──────────────────────► │
│   │                                                 │
│   │   Chat::confirm()                               │
│   │     ├── Validasi form                           │
│   │     ├── Transaction_model::add_transaction()    │
│   │     └── Update meta_json → confirmed=true       │
│   │                                                 │
│   │◄── Status success                               │
│   │                                                 │
│   │   ┌──────────────────────┐                      │
│   │   │ ✓ SAVED              │                      │
│   │   │ Amount: Rp 25.000    │                      │
│   │   └──────────────────────┘                      │
│                                                     │
└─────────────────────────────────────────────────────┘
```

### 5.3 Alur AI Extraction

```php
// TransactionExtractor.php — Regex Parser (zero LLM cost)
public function extract($text, $todayYmd) {
    $type = $this->guess_type($text);

    // Parse nominal: "25k" → 25000, "7.5jt" → 7500000
    $amount = $this->parse_amount_idr($text);

    // Parse tanggal: "kemarin", "tgl 25", "2026-07-13"
    $date = $this->parse_date($text, $todayYmd);

    // Generate title: buang keyword amount/tanggal
    $title = $this->guess_title($text, $amount, $date);

    // Kategori classifier
    $category = $this->categoryclassifier->classify(...);

    return [
        'intent' => ($amount >= 3) ? 'create_transaction' : 'clarify',
        'draft'  => [
            'type' => $type,
            'amount' => $amount,
            'title' => $title,
            'category' => $category,
            'payee' => '',
            'transaction_date' => $date,
        ],
        'confidence' => 0.85,
    ];
}
```

**Regex amount parser — dukung format Indonesia:**
```php
function parse_amount_idr($text) {
    // "250k" atau "250rb" → 250000
    if (preg_match('/(\d+[.,]?\d*)\s*(k|rb|ribu)\b/i', $text, $m))
        return (float)str_replace(',', '.', $m[1]) * 1000;

    // "7.5jt" atau "7,5jt" atau "7.5 juta" → 7500000
    if (preg_match('/(\d+[.,]?\d*)\s*(jt|juta|million)\b/i', $text, $m))
        return (float)str_replace(',', '.', $m[1]) * 1000000;

    // "7.500.000" — format Indonesia
    if (preg_match('/(\d{1,3}(?:\.\d{3})+)/', $text, $m))
        return (float)str_replace('.', '', $m[1]);

    // "7500000" — angka ≥3 digit
    if (preg_match('/\b(\d{3,})\b/', $text, $m))
        return (float)$m[1];

    return 0;
}
```

**Auto-category 3-tier:**
```php
// CategoryClassifier.php
public function classify($text, $type, $allowed, $aiCfg) {
    // Tier 1: AI-first (jika enabled)
    if (!empty($aiCfg['ai_enabled'])) {
        $result = $this->classify_with_ai($text, $type, $allowed, $aiCfg);
        if ($result) return $result;
    }

    // Tier 2: Deterministic keyword rules
    $result = $this->classify_with_rules($text, $type);
    if ($result) return $result;

    // Tier 3: Default fallback
    return $this->default_category($allowed);
}
```

---

## 6. Keamanan

| Aspek | Implementasi |
|-------|-------------|
| **Password** | `password_hash(PASSWORD_DEFAULT)` — bcrypt |
| **CSRF** | CodeIgniter CSRF protection, exclude chat AJAX endpoints |
| **Session** | Regenerasi session setelah login, destroy setelah logout |
| **Rate Limit** | Login: 5 attempts / 5 menit (session-based) |
| **Role-based Access** | Triple gate admin, ownership check tiap transaksi |
| **XSS** | `htmlspecialchars()` pada semua output user |
| **CSP** | Content-Security-Policy header di .htaccess |
| **Input Validation** | Form validation CI3, strip dots pada amount, MIME check upload |
| **Encryption Key** | Dari environment variable `ENCRYPTION_KEY` |
| **Block Public Access** | .htaccess blokir `.env`, `.git`, `composer.json`, `README.md` |
| **Admin Auth** | Secret key via `hash_equals()`, bukan password user |

---

## 7. Cara Install

```bash
# 1. Clone repo
git clone https://github.com/dhabyap/finance_app.git

# 2. Install dependencies
composer install

# 3. Import database
mysql -u root -p finance_app < finance_app.sql
# Atau jalankan migration:
mysql -u root -p finance_app < sql/ai_chat_tables.sql
mysql -u root -p finance_app < sql/migrations/001_add_role_and_goal_amount.sql

# 4. Copy .env
cp .env.example .env
# Isi: ENCRYPTION_KEY, AI_ENABLED, GEMINI_API_KEY, GROQ_API_KEY

# 5. Setting base_url di application/config/config.php
$config['base_url'] = 'http://localhost/finance_app/';

# 6. Setting database di application/config/database.php
$db['default']['database'] = 'finance_app';

# 7. Pastikan folder writable bisa ditulis
chmod -R 755 writable/
```

**Requirement:**
- PHP 7.4+
- MySQL 5.7+
- Composer
- mod_rewrite (Apache)

---

## Catatan untuk Interview

Fitur yang bisa di-highlight saat technical discussion:

1. **AI Chat dengan Regex-first approach** — Menghemat biaya LLM, fallback hanya jika diperlukan
2. **Neubrutalism UI** — Mobile-first, konsisten, aksesibel
3. **Security hardening** — CSP, CSRF, rate limiting, timing-safe comparison, session regeneration
4. **Auto-category 3-tier** — AI → Rules → Default, dengan strict guardrails
5. **Single conversation design** — Sederhana, user ga bingung dengan threads
6. **Dedup cache** — Hash SHA256 mencegah proses ulang pesan yang sama
7. **Import CSV/Excel** — Batch insert, auto-detect kolom, multi-format date parser
8. **Financial analytics** — Saving rate, emergency fund, freedom forecast
