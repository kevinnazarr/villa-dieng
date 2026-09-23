# Villa Dieng Booking Engine — Dokumentasi Arsip

| Item | Nilai |
|---|---|
| Status | ARCHIVED — development dihentikan |
| Development terakhir | 23 September 2026 |
| Branch utama | `main` (commit terakhir `6943adb`, 2026-09-22) |
| Branch kerja terakhir | `feature/frontend-foundation` (commit `fc34374`, 2026-09-23) |
| Nama repository | `villa-dieng` |
| Nama project | Villa Dieng Booking Engine |

Repository ini tidak lagi dikembangkan. README ini adalah dokumentasi arsip, bukan dokumentasi marketing: isinya menjelaskan apa yang benar-benar dibangun, apa yang belum selesai, dan mengapa project dihentikan.

## 1. Ringkasan Project

Project ini adalah eksperimen pembuatan website profile villa/cabin di Dieng (Wonosobo) yang sekaligus berfungsi sebagai self-service booking engine. Tujuan awal:

- website profile villa dan informasi cabin
- menampilkan availability
- guest melakukan booking sendiri (direct booking)
- reservation flow dan reservation management
- sandbox payment flow
- customer account
- admin dashboard

Pendekatan teknis yang dipilih: Laravel REST API, React + TypeScript, PostgreSQL, Redis, dan Docker.

Hasil akhir yang benar-benar ada di repository:

- **Backend**: domain booking (reservation, payment, availability block, reservation event, expiration) dan REST API `/api/v1` sudah lengkap untuk scope yang dikerjakan, dan memiliki test suite. Pekerjaan ini sudah digabung ke `main`.
- **Frontend**: fondasi aplikasi (Vite + TypeScript + Tailwind v4, API client, routing + layout, auth context, lokalisasi ID/EN) dan halaman publik Home, Cabin Detail, serta langkah pertama Booking. Pekerjaan ini berada di branch `feature/frontend-foundation` dan **belum** digabung ke `main`.
- **Belum ada**: UI customer account, UI admin dashboard, UI checkout/pembayaran, dan halaman konten (FAQ, lokasi, house rules, kontak) — semuanya masih berupa route placeholder.

Catatan status yang perlu diketahui saat membuka repository ini:

- `main` hanya berisi sampai backend API layer (commit terakhir 2026-09-22).
- Pekerjaan flow booking terakhir (tercatat di `docs/logs/LOG-009.md`) masih berupa perubahan working tree yang belum di-commit (`frontend/src/pages/Booking.tsx`, `frontend/src/services/booking.ts`, dan tiga file frontend yang dimodifikasi).
- `docs/PRD.md` dan `docs/DESIGN.md` menjelaskan target produk yang jauh lebih luas daripada yang terimplementasi. Dokumen tersebut adalah rencana, bukan deskripsi kondisi akhir.

## 2. Timeline Pengembangan

Tanggal di bawah diambil dari git history (tanggal commit).

| Tanggal | Milestone | Bukti commit / PR |
|---|---|---|
| 2026-09-21 | Project dimulai (initial commit, dokumen PRD/DESIGN/Technical Design) | `1636c96`, `ba6ddb3`, PR #1 |
| 2026-09-21 – 2026-09-22 | Docker foundation (PostgreSQL, Redis, backend PHP, frontend Node) | `93e53b0`, `257b23f`, `5e33211`, PR #4–#5 |
| 2026-09-22 | Backend foundation (boundary `/api/v1`, error envelope JSON, UUID user, Sanctum) | `dbb017a`, PR #6 |
| 2026-09-22 | Domain & database foundation (13 migration, enum, model) | `55bbb2a`, `d099064`, `f8bc79c`, `a44a758`, PR #8–#14 |
| 2026-09-22 | Reservation & booking domain (action, state machine, sandbox payment, expiry) | `a668ce4`, `9dc5af5`, `f9f869e`, PR #15–#17 |
| 2026-09-22 | Backend API layer (controller, form request, resource, rate limiter) — commit terakhir di `main` | `e84d842`, `8782daa`, `f178a28`, `18db107`, PR #18–#19 |
| 2026-09-23 | Frontend foundation (migrasi TypeScript, Tailwind v4, API client, routing/layout, auth, lokalisasi ID/EN) | `9b7a7a7`, `9765504`, `76c6709`, `3bb0c26`, `ab93171`, `558270f`, PR #20–#27 |
| 2026-09-23 | Public Home (API-driven) | `5edbad6` |
| 2026-09-23 | Public Cabin Detail | `fc34374` |
| 2026-09-23 | Availability & Booking (langkah pertama di frontend) — belum di-commit | working tree: `frontend/src/pages/Booking.tsx`, `frontend/src/services/booking.ts` |
| 2026-09-23 | Development dihentikan | — |

Catatan: `docs/logs/LOG-003.md`, `LOG-004.md`, dan `LOG-005.md` mencantumkan tanggal 2026-09-23, sedangkan commit untuk pekerjaan tersebut bertanggal 2026-09-22 pada git history. Tabel di atas memakai tanggal commit Git sebagai sumber utama.

### Status milestone

| # | Milestone | Status |
|---|---|---|
| 1 | Backend foundation | Selesai (sudah di `main`) |
| 2 | Domain / database foundation | Selesai (sudah di `main`) |
| 3 | Reservation / booking domain | Selesai (sudah di `main`) |
| 4 | Backend API layer | Selesai (sudah di `main`) |
| 5 | Frontend foundation | Selesai pada branch `feature/frontend-foundation`, belum di-merge ke `main` |
| 6 | Authentication | Sebagian: backend Sanctum selesai; frontend login/register/logout selesai di branch; tanpa reset password atau verifikasi email |
| 7 | Localization | Selesai di branch (ID/EN), belum di `main` |
| 8 | Public Home | Selesai di branch, belum di `main` |
| 9 | Public Cabin Detail | Selesai di branch, belum di `main` |
| 10 | Availability & Booking | Sebagian: cek availability, pemilihan cabin/tanggal, guest checkout, dan pembuatan reservasi sudah berjalan; UI checkout pembayaran dan halaman hasil belum dibuat |

## 3. Technology Stack

Semua versi di bawah dapat diverifikasi dari file konfigurasi repository.

| Bagian | Teknologi | Versi | Sumber |
|---|---|---|---|
| Backend | PHP | `^8.4` | `backend/composer.json` |
| Backend | Laravel | `^13.17` (lockfile: `v13.32.0`) | `backend/composer.json`, `backend/composer.lock` |
| Backend | Laravel Sanctum | `^4.0` | `backend/composer.json` |
| Backend | Laravel Tinker | `^3.0` | `backend/composer.json` |
| Backend (dev) | PHPUnit | `^12.5.12` (lockfile: `12.5.35`) | `backend/composer.json`, `backend/composer.lock` |
| Backend (dev) | Laravel Pint, Mockery, Collision, Faker | lihat lockfile | `backend/composer.json` |
| Frontend | React / React DOM | `^19.2.8` | `frontend/package.json` |
| Frontend | react-router-dom | `^7.18.4` | `frontend/package.json` |
| Frontend | TypeScript | `^5.9.0` | `frontend/package.json` |
| Frontend | Vite | `^8.3.0` | `frontend/package.json` |
| Frontend | Tailwind CSS + `@tailwindcss/vite` | `^4.3.3` | `frontend/package.json` |
| Frontend | axios | `^1.20.0` | `frontend/package.json` |
| Frontend | lucide-react | `^1.47.0` | `frontend/package.json` |
| Frontend (dev) | oxlint | `^1.81.0` | `frontend/package.json` |
| Database | PostgreSQL 15 (image `postgres:15-alpine`) | 15 | `docker-compose.yml` |
| Database | Ekstensi PostgreSQL | `pgcrypto`, `btree_gist`, `citext` | `docker/postgres/init-extensions.sql` |
| Cache / Queue / Rate limiter | Redis 7 (image `redis:7-alpine`), dipakai untuk rate limiter | 7 | `docker-compose.yml` |
| Containerization | Docker + Docker Compose (4 service: postgres, redis, backend, frontend) | Compose v2 | `docker-compose.yml`, `backend/Dockerfile`, `frontend/Dockerfile` |
| Authentication | Laravel Sanctum personal access token, tanpa expiry; role `guest` / `admin`; middleware `EnsureAdmin` | — | `backend/routes/api.php`, `bootstrap/app.php`, `app/Http/Middleware/EnsureAdmin.php` |
| API | REST JSON, prefix `/api/v1`, response `{data: ...}` / `{message: ...}` / `{message, errors}` | — | `backend/routes/api.php`, `bootstrap/app.php` |
| Testing backend | PHPUnit (`php artisan test`), termasuk suite PostgreSQL | — | `backend/phpunit.xml`, `backend/tests/` |
| Testing frontend | Tidak ada test runner; hanya `tsc` (typecheck) dan `oxlint` | — | `frontend/package.json` |

Detail konfigurasi runtime yang perlu diketahui:

- Rate limiter memakai Redis (`throttleWithRedis`): `api` 60/menit/IP, `auth-strict` 10/menit/IP, `booking` 30/menit/IP (`backend/bootstrap/app.php`, `backend/app/Providers/AppServiceProvider.php`).
- Meskipun Redis tersedia, di `docker-compose.yml` lokal `CACHE_STORE=file`, `SESSION_DRIVER=file`, dan `QUEUE_CONNECTION=database` (Redis hanya dipakai untuk rate limiter).
- `docs/DESIGN.md` dan `frontend/AGENTS.md` menyebut shadcn/ui, tetapi paket tersebut **tidak** terpasang di `frontend/package.json`.
- Backend test suite memakai SQLite `:memory:` secara default, kecuali suite PostgreSQL (`backend/tests/PgTestCase.php`) yang membutuhkan koneksi `pgsql_testing` (database default `villa_dieng_test`) dan gagal keras bila tidak tersedia.

## 4. Arsitektur

Kondisi repository sebenarnya (4 service Docker + satu API):

```text
Browser (guest / customer / admin)
        |
        v
React + Vite + TypeScript  (frontend, port 5173)
        |
        | HTTP JSON, Authorization: Bearer <sanctum token>
        v
Laravel REST API  /api/v1  (backend, port 8000)
        |
        +--> PostgreSQL 15  (sumber kebenaran data & availability)
        |
        +--> Redis 7  (rate limiter; cache/queue disediakan tetapi belum dipakai untuk data bisnis)
```

Tanggung jawab tiap bagian:

- **Frontend** (`frontend/`): hanya menangani UI dan pemanggilan API. Semua aturan bisnis, validasi server, dan keputusan konflik ada di backend. Stateless: menyimpan token Sanctum di `localStorage` (`src/services/token.ts`) dan seluruh data diambil ulang dari API. Tidak ada state management global selain `AuthContext` dan `LocaleContext`.
- **Backend** (`backend/`): Laravel REST API. Alur yang dipakai konsisten: Route → Controller tipis → Form Request (validasi bentuk) → Action (`app/Actions/`) → Domain (`app/Domain/`) → Model Eloquent → PostgreSQL. Controller tidak memuat logika bisnis.
- **PostgreSQL**: sumber kebenaran data termasuk availability dan anti-double-booking (constraint `EXCLUDE`). Keputusan database-level tidak boleh dipindahkan ke Redis.
- **Redis**: hanya untuk rate limiter. Cache, session, dan queue tetap memakai driver `file`/`database` pada konfigurasi lokal.
- **Docker**: menyediakan environment pengembangan yang reproducible (PostgreSQL + Redis + backend + frontend) dengan health check dan named volume.

Lapisan domain backend:

| Lokasi | Isi |
|---|---|
| `app/Actions/` | `CreateReservationAction`, `InitiatePaymentAction`, `ProcessPaymentAction`, `ConfirmReservationAction`, `CancelReservationAction`, `ExpirePendingReservationAction` |
| `app/Domain/` | `ReservationStateMachine`, `PaymentStateMachine`, `AvailabilityChecker` |
| `app/Services/` | `SandboxPaymentGateway` (simulasi pembayaran, tanpa uang nyata) |
| `app/Jobs/` | `ExpirePendingReservationsJob` (dijadwalkan setiap menit di `routes/console.php`) |
| `app/Enums/` | `ReservationStatus`, `PaymentStatus`, `ReservationEventType`, `UserRole` |
| `app/Exceptions/` | `BookingConflictException` (dipetakan menjadi HTTP 409) |

## 5. Struktur Repository

```text
villa-dieng/
├── AGENTS.md                  pedoman kerja agent/AI untuk repo ini
├── docker-compose.yml         4 service pengembangan
├── docker/postgres/           init script ekstensi PostgreSQL
├── backend/                   Laravel REST API
│   ├── app/                   Actions, Domain, Enums, Http, Jobs, Models, Services
│   ├── database/migrations/   13 migration domain + migration bawaan Laravel
│   ├── routes/api.php         seluruh endpoint /api/v1
│   └── tests/                 PHPUnit (Feature/Api, BookingDomainTest, DomainConstraintsTest, PgTestCase)
├── frontend/                  React + Vite + TypeScript
│   └── src/
│       ├── app/               router, guards, layouts
│       ├── auth/              AuthContext
│       ├── components/        CabinCard, skeleton, LocaleSwitcher
│       ├── i18n/              dictionaries ID/EN, LocaleContext, format helpers
│       ├── lib/               error normalization, image helpers
│       ├── pages/             Home, CabinDetail, Booking, Login, Register, Forbidden, NotFound, PlaceholderPage
│       ├── services/          api client, catalog, booking, guest, token
│       └── types/api.ts       tipe yang diturunkan dari API Resource
├── docs/
│   ├── PRD.md                 rencana produk (lebih luas dari implementasi)
│   ├── DESIGN.md              arah desain visual dan UX (sebagian belum diimplementasikan)
│   ├── Technical-Design-Villa-Dieng.md   desain teknis, skema, state machine, concurrency
│   ├── knowledge/             placeholder untuk pengetahuan durable (masih minimal)
│   └── logs/LOG-001.md … LOG-009.md       riwayat perubahan per pekerjaan
├── assets/                    logo dan favicon
└── .github/                   CONTRIBUTING, branch naming, PR template
```

## 6. Booking Domain

Bagian ini adalah inti project dan seluruhnya dapat diverifikasi dari migration dan kode backend (`backend/database/migrations/`, `backend/app/`).

### Reservation

- Satu reservation = satu cabin, satu rentang tanggal (`check_in`, `check_out`), jumlah dewasa/anak, data tamu (`guest_name`, `guest_email`, `guest_phone`, `special_request`), dan `booking_code` publik berformat `VD-XXXXXXXX`.
- Harga disimpan sebagai snapshot saat reservasi dibuat: `nightly_rate`, `subtotal`, `total`, `currency`. Perubahan harga cabin di kemudian hari tidak mengubah reservasi lama. Untuk scope saat ini `total = subtotal` (belum ada pajak/biaya tambahan).
- `guest_email` disimpan sebagai `CITEXT` (case-insensitive) dan dinormalisasi menjadi lowercase di kode.
- Guest checkout tidak memerlukan akun. Sistem melakukan find-or-create user berdasarkan email; user hasil guest checkout dibuat dengan `password = NULL` sehingga tidak bisa login (login selalu gagal secara generik).

### Status reservation dan transisinya

| Status | Artefak | Boleh bertransisi ke |
|---|---|---|
| `pending_payment` | dibuat saat checkout, punya `expires_at` (15 menit) | `paid`, `expired`, `cancelled` |
| `paid` | setelah pembayaran sukses | `confirmed`, `cancelled` |
| `confirmed` | setelah dikonfirmasi (dilakukan admin lewat API) | `cancelled` |
| `expired` | melewati batas pembayaran | — (final) |
| `cancelled` | dibatalkan | — (final) |

Transisi tidak sah ditolak oleh `ReservationStateMachine` (menghasilkan `InvalidArgumentException`, yang dirender sebagai HTTP 422).

### Payment

| Status | Boleh bertransisi ke |
|---|---|
| `pending` | `processing`, `expired` |
| `processing` | `paid`, `failed`, `unknown`, `expired` |
| `unknown` | `paid`, `failed`, `expired` |
| `failed` | `processing` |
| `paid` | — (final) |
| `expired` | — (final) |

- Provider selalu `sandbox`. `SandboxPaymentGateway` memberi hasil deterministik dari skenario: `success` → `paid`, `fail` → `failed`, `timeout` → `unknown` (dapat direkonsiliasi). Tidak ada uang nyata dan tidak ada payment gateway sungguhan.
- Saat pembayaran sukses, alur berantai `pending_payment → paid → confirmed` dijalankan melalui state machine.
- Idempotency memakai header `Idempotency-Key` (header menang atas body); percobaan ulang dengan key yang sama akan memakai ulang payment yang masih terbuka, bukan membuat payment baru.

### Status pembayaran blocking (availability)

Reservation yang memblokir inventory adalah `pending_payment`, `paid`, dan `confirmed`. Status `expired` dan `cancelled` tidak memblokir.

### Perhitungan overlap dan anti-double-booking

- Rentang tanggal memakai semantik `[)`: `check_in` inklusif, `check_out` eksklusif. Dua reservasi yang bersinggungan di tanggal check-out bukan konflik.
- Ada tiga lapis pengamanan:
  1. Pemeriksaan aplikasi: `AvailabilityChecker` melakukan query overlap terhadap reservation blocking dan availability block.
  2. Transaksi database: `CreateReservationAction` menjalankan pembuatan reservasi di dalam transaksi dan mengunci baris cabin dengan `lockForUpdate()` sebelum pengecekan ulang.
  3. Constraint PostgreSQL sebagai penentu akhir: `EXCLUDE USING gist (cabin_id WITH =, daterange(check_in, check_out, '[)') WITH &&) WHERE status IN ('pending_payment','paid','confirmed')` (migration `2026_09_23_000109_add_reservations_no_overlap.php`). Pelanggaran constraint (SQLSTATE 23P01) ditangkap dan dipetakan menjadi `BookingConflictException` → HTTP 409 `{message: "BOOKING_CONFLICT"}`.
- Invariant yang dijaga: **satu cabin tidak boleh memiliki lebih dari satu reservasi yang memblokir inventory pada rentang tanggal yang overlap pada waktu yang sama.**

### Availability block

- `availability_blocks` adalah cara admin menutup tanggal cabin (mis. maintenance) tanpa membuat reservasi. Berisi `cabin_id`, `starts_on`, `ends_on`, `reason`, dan `created_by`.
- Memakai semantik `[)` yang sama dan dilindungi constraint `availability_blocks_no_overlap` (GiST `EXCLUDE`, tanpa predikat status), sehingga block per cabin tidak boleh saling overlap.
- Pembuatan/penghapusan block melalui API hanya menulis baris di tabel `availability_blocks`; belum ada kode yang menulis reservation event. Enum `ReservationEventType` menyediakan nilai `availability_blocked` dan `availability_unblocked`, tetapi keduanya belum pernah dipakai.

### Reservation events (audit)

- Tabel `reservation_events` bersifat append-only (tanpa `updated_at`).
- Jenis event: `created`, `payment_initiated`, `payment_succeeded`, `payment_failed`, `confirmed`, `expired`, `cancelled`, `availability_blocked`, `availability_unblocked`.
- Setiap event menyimpan `from_status`, `to_status`, `actor_id` (nullable), dan `metadata`. Transisi status melalui state machine selalu menulis satu event.

### Expiration

- Batas pembayaran `CreateReservationAction::PAYMENT_WINDOW_MINUTES = 15` menit; `reservations.expires_at` wajib terisi selama status `pending_payment` (dijaga CHECK constraint `chk_reservation_expiry`).
- `ExpirePendingReservationsJob` dijadwalkan setiap menit (`routes/console.php`), mengambil reservasi `pending_payment` yang sudah lewat `expires_at` dengan `for update skip locked` dan `chunkById(100)`, lalu `ExpirePendingReservationAction` mengunci ulang baris dan menutup reservasi beserta payment yang masih terbuka.

### Booking code

- Format `VD-` + 8 karakter acak; kolom `booking_code` unique. Bila terjadi tabrakan kode, pembuatan diulang maksimal 5 kali (`CreateReservationAction::BOOKING_CODE_ATTEMPTS`).
- Akses guest ke reservasi memakai pasangan `booking_code` (di URL) + `guest_email` (dikirim per request); bila tidak cocok hasilnya HTTP 404.

## 7. API

Semua endpoint ada di prefix `/api/v1` (`backend/routes/api.php`). Response sukses memakai envelope `{data: ...}`; response error memakai `{message: ...}`, dan `{message, errors}` untuk kegagalan validasi.

### Public API

| Method | Endpoint | Tujuan |
|---|---|---|
| GET | `/v1/health` | Status service: `{status, checks: {database, redis}}`; 200, atau 503 jika salah satu dependency down |
| GET | `/v1/properties` | Daftar properti aktif |
| GET | `/v1/properties/{propertySlug}` | Detail properti berdasarkan slug |
| GET | `/v1/properties/{propertySlug}/cabins` | Daftar cabin aktif pada properti tersebut |
| GET | `/v1/properties/{propertySlug}/cabins/{cabinSlug}` | Detail cabin (termasuk gambar, amenity, dan properti induk) |
| GET | `/v1/availability?cabin_id=&check_in=&check_out=` | Cek ketersediaan cabin (referensi UUID cabin); 422 untuk UUID/tanggal tidak valid |

### Guest checkout dan payment

| Method | Endpoint | Tujuan |
|---|---|---|
| POST | `/v1/reservations` | Membuat reservasi guest; 201 + `booking_code`; 409 `BOOKING_CONFLICT` saat overlap; 422 validasi; limiter `booking` |
| GET | `/v1/reservations/{booking_code}?guest_email=` | Detail reservasi guest; email tidak cocok → 404 |
| POST | `/v1/reservations/{booking_code}/cancel` | Membatalkan reservasi guest (disertai email) |
| POST | `/v1/reservations/{booking_code}/payments` | Memulai pembayaran sandbox; key idempotency sama akan memakai payment yang masih terbuka |
| POST | `/v1/payments/{payment}/process` | Memproses pembayaran sandbox (skenario `success` / `fail` / `timeout`; ditolak di environment production) |

### Authentication API

| Method | Endpoint | Tujuan |
|---|---|---|
| POST | `/v1/auth/register` | Registrasi; selalu membuat user `guest` (input `role` diabaikan); 201 + token; limiter `auth-strict` |
| POST | `/v1/auth/login` | Login; kredensial salah menghasilkan 422 generik (tanpa membocorkan keberadaan email); limiter `auth-strict` |
| POST | `/v1/auth/logout` | Mencabut token yang sedang dipakai; 204 |
| GET | `/v1/me` | Profil user yang sedang login; butuh `auth:sanctum` |

### Customer API

| Method | Endpoint | Tujuan |
|---|---|---|
| GET | `/v1/my/reservations?status=&page=` | Daftar reservasi milik user yang login (15 per halaman, filter status); hanya data miliknya sendiri |

### Admin API

Semua endpoint di bawah butuh `auth:sanctum` + middleware `admin` (role selain `admin` → 403).

| Method | Endpoint | Tujuan |
|---|---|---|
| GET | `/v1/admin/reservations` | Daftar reservasi |
| GET | `/v1/admin/reservations/{reservation}` | Detail reservasi (berdasarkan UUID) |
| POST | `/v1/admin/reservations/{reservation}/confirm` | Konfirmasi reservasi |
| POST | `/v1/admin/reservations/{reservation}/cancel` | Batalkan reservasi |
| GET | `/v1/admin/availability-blocks` | Daftar availability block (filter opsional `cabin_id`) |
| POST | `/v1/admin/availability-blocks` | Membuat availability block; overlap → 409 |
| DELETE | `/v1/admin/availability-blocks/{block}` | Menghapus availability block; 204 |

### Endpoint scaffolding

| Method | Endpoint | Tujuan |
|---|---|---|
| GET | `/api/user` | Route bawaan Laravel, dipertahankan tanpa perubahan |
| POST | `/v1/probe/validation` | Alat uji cepat untuk envelope error validasi `{email}` |

Catatan: tidak ada endpoint untuk mengubah data properti/cabin, tidak ada endpoint update untuk availability block, dan seluruh endpoint admin maupun customer belum dipakai oleh frontend (halaman terkait masih placeholder).

## 8. Frontend

Ringkasan: frontend berisi fondasi aplikasi, halaman publik, dan halaman auth. Semua ini ada di branch `feature/frontend-foundation` (belum di-merge ke `main`), dan pekerjaan Booking masih berupa perubahan working tree yang belum di-commit.

### Public

| Route | Isi | Status |
|---|---|---|
| `/` | Home: hero properti, daftar cabin (`CabinCard`), highlights amenity, CTA; data dari API; ada skeleton serta state kosong/error dengan retry | Terimplementasi |
| `/cabin/:propertySlug/:cabinSlug` | Cabin Detail: breadcrumb, galeri gambar, harga via `Intl`, deskripsi, amenity, konteks properti, CTA ke `/booking` | Terimplementasi |
| `/booking` | Booking langkah pertama: pilih properti/cabin, tanggal, jumlah tamu, cek availability (terikat snapshot input), form data tamu, POST reservasi dengan `Idempotency-Key` per percobaan, lalu konfirmasi inline (kode booking, email, total dari server). Tetap di halaman ini; tidak ada UI pembayaran | Terimplementasi (belum di-commit) |
| `/login`, `/register` | Form login dan registrasi via `AuthContext` | Terimplementasi |
| `/forbidden` | Halaman 403 | Terimplementasi |
| `*` | Halaman 404 | Terimplementasi |
| `/checkout`, `/payment`, `/booking/success`, `/booking/failed`, `/faq`, `/location`, `/house-rules`, `/contact` | `PlaceholderPage` (hanya judul dan keterangan bahwa fitur belum ada) | Placeholder |

### Customer (butuh login, dijaga `RequireAuth`)

| Route | Status |
|---|---|
| `/account` | Placeholder |
| `/account/bookings` | Placeholder |
| `/account/bookings/:bookingCode` | Placeholder |
| `/account/profile` | Placeholder |

### Admin (butuh login + role `admin`, dijaga `RequireAdmin`)

| Route | Status |
|---|---|
| `/admin` | Placeholder |
| `/admin/reservations` | Placeholder |
| `/admin/reservations/:reservationId` | Placeholder |
| `/admin/availability` | Placeholder |
| `/admin/customers` | Placeholder |
| `/admin/customers/:userId` | Placeholder |

### Bagian frontend yang sudah jadi (bukan halaman)

- API client axios dengan interceptor token dan normalisasi error (`src/services/api.ts`, `src/lib/errors.ts`).
- `AuthContext`: login, register, logout, refresh `GET /me`, token di `localStorage` (`src/services/token.ts`).
- Routing, layout (Public/Customer/Admin), dan guard `RequireAuth` / `RequireAdmin`; role admin dibaca dari `GET /me`, sedangkan otorisasi sebenarnya tetap di backend.
- Lokalisasi ID/EN: dictionary bertipe dengan EN sebagai sumber key, default `id`, format tanggal/angka/uang memakai `Intl`, dan `LocaleSwitcher`.
- Komponen pendukung: `CabinCard`, `HomeSkeleton`, `CabinDetailSkeleton`, helper gambar (`src/lib/images.ts`).

### Direncanakan tetapi belum dibuat

- Seluruh UI admin dan UI customer account (masih placeholder).
- UI checkout dan pembayaran: endpoint backend tersedia, tetapi frontend belum memanggil `POST /payments` maupun `POST /payments/{payment}/process`.
- Halaman hasil booking dan halaman konten publik (FAQ, lokasi, house rules, kontak).
- Kalender availability (UI masih memakai input tanggal biasa, bukan kalender visual).
- `src/services/guest.ts` (lookup reservasi guest) sudah dibuat tetapi belum diimpor halaman mana pun.
- Tidak ada automated test frontend.
- Komponen shadcn/ui yang disebut di `docs/DESIGN.md` dan `frontend/AGENTS.md` belum terpasang.

## 9. Status Fitur

### Sudah selesai

| Area | Keterangan | Bukti |
|---|---|---|
| Docker development environment | 4 service; health check pada postgres, redis, dan backend; named volume untuk data | `docker-compose.yml`, `backend/Dockerfile`, `frontend/Dockerfile`, LOG-001 |
| Fondasi backend | Boundary `/api/v1`, error envelope JSON, UUID user, Sanctum dasar | LOG-002 |
| Skema database domain | 13 migration domain, enum PostgreSQL, CHECK dan foreign key | `backend/database/migrations/`, LOG-003 |
| Domain reservasi & pembayaran | Action layer, state machine, sandbox gateway, job expiry, pemetaan 409 | `backend/app/`, LOG-004 |
| REST API `/api/v1` | Catalog, availability, guest checkout, payment, auth Sanctum, customer, admin, rate limiter Redis | `backend/routes/api.php`, LOG-005 |
| Test backend | 79 method pengujian (termasuk 2 test contoh bawaan Laravel) | `backend/tests/` |
| Fondasi frontend | Vite + TypeScript + Tailwind v4, API client, routing/layout/guard, AuthContext, lokalisasi ID/EN | `frontend/src/`, LOG-006 |
| Public Home (API-driven) | Data properti dan cabin dari API, dengan state loading/empty/error | `frontend/src/pages/Home.tsx`, LOG-007 |
| Public Cabin Detail | Detail cabin dengan galeri dan amenity; 404 dibedakan dari error | `frontend/src/pages/CabinDetail.tsx`, LOG-008 |
| Booking langkah pertama | Cek availability, form data tamu, pembuatan reservasi, konfirmasi inline | `frontend/src/pages/Booking.tsx`, LOG-009 (belum di-commit) |
| Login/Register/Logout (frontend) | Form login dan registrasi, logout yang mencabut token, serta guard `RequireAuth`/`RequireAdmin` | `frontend/src/auth/AuthContext.tsx`, `frontend/src/app/guards.tsx` |

### Sebagian selesai

| Area | Yang sudah ada | Yang belum ada |
|---|---|---|
| Authentication | Sanctum di backend, register/login/logout, middleware `EnsureAdmin`, AuthContext di frontend | Reset password, verifikasi email, expiry/refresh token, halaman profil |
| Booking flow | Availability check, guest checkout, pembuatan reservasi, konfirmasi inline | UI checkout, UI pembayaran (initiate/process), halaman sukses/gagal, UI pembatalan, lookup reservasi guest dari UI |
| Customer account | Endpoint `GET /v1/my/reservations` | Semua halaman akun masih placeholder |
| Admin | Endpoint daftar/detail/confirm/cancel reservasi dan CRUD terbatas availability block | Seluruh UI admin belum ada; event `availability_blocked`/`availability_unblocked` belum ditulis kode; tidak ada endpoint update block |
| Availability | Query availability dan constraint anti-double-booking di PostgreSQL | Kalender availability di UI, harga musiman, inventory multi-cabin |
| Frontend foundation | Seluruh pekerjaan frontend selesai di branch | Belum di-merge ke `main` |
| Pengujian | Suite backend ada dan hasil verifikasinya tercatat di `docs/logs/` | Tidak ada pengujian otomatis untuk frontend |

### Direncanakan tetapi belum selesai

Diambil dari `docs/PRD.md` (MVP dan V2), `docs/DESIGN.md`, dan `frontend/AGENTS.md` — belum ada implementasinya:

- Payment gateway nyata (MVP sendiri memang memilih sandbox).
- Email/notifikasi (simulasi email pun belum ada kodenya).
- Halaman konten publik: FAQ, lokasi, house rules, kontak.
- UI customer dashboard: profil, riwayat booking, detail booking.
- UI admin dashboard: overview, daftar/detail reservasi, kalender availability, block manual, customer management, status pembayaran.
- Komponen shadcn/ui.
- Multi-cabin inventory, seasonal/weekend/holiday pricing, promo code, extra guest pricing (V2).
- Integrasi WhatsApp/OTA/iCal, reschedule, ulasan, saved guest info (V2).
- Pengujian otomatis frontend.

## 10. Pengujian dan Verifikasi

Backend:

- Perintah: `php artisan test` (PHPUnit 12, konfigurasi `backend/phpunit.xml`).
- Ada 79 method pengujian yang tersebar di `backend/tests/`:
  - `tests/Feature/ApiFoundationTest.php` (7), `tests/Feature/Api/AdminApiTest.php` (5), `AuthApiTest.php` (8), `AvailabilityApiTest.php` (4), `CatalogApiTest.php` (5), `CustomerApiTest.php` (3), `ErrorContractApiTest.php` (4), `GuestReservationApiTest.php` (6), `IdempotencyApiTest.php` (2), `PaymentApiTest.php` (7), `RateLimitApiTest.php` (2).
  - `tests/Feature/BookingDomainTest.php` (13) dan `tests/Feature/DomainConstraintsTest.php` (11) berjalan di atas PostgreSQL asli dan mencakup overlap/EXCLUDE, CITEXT, capacity, state machine, expiry, serta idempotency.
  - `tests/Feature/ExampleTest.php` dan `tests/Unit/ExampleTest.php` adalah test contoh bawaan Laravel.
- Suite PostgreSQL memakai `tests/PgTestCase.php`, yang memaksa koneksi `pgsql_testing` dan **gagal keras** (bukan skip) bila PostgreSQL tidak tersedia. Database test default `villa_dieng_test`; ekstensi `btree_gist`, `pgcrypto`, dan `citext` dibuat oleh base class tersebut.
- Hasil pengujian terakhir yang tercatat di repository (dari `docs/logs/`): LOG-002 "9 passed, 21 assertions", LOG-004 `php artisan test --filter=BookingDomainTest` "13 passed, 36 assertions", LOG-005 pengujian alur auth dan booking API. README ini tidak menjalankan ulang test suite tersebut; angka di atas berasal dari log, bukan dari eksekusi baru.

Frontend:

- Tidak ada test runner (tidak ada Vitest/Jest). Verifikasi yang dipakai saat pengembangan: `npm run build` (typecheck `tsc` + `vite build`), `npm run lint` (`oxlint`), dan pengujian manual di browser melalui Docker container, sebagaimana dicatat pada LOG-006 sampai LOG-009.
- Perintah yang tersedia: `npm run dev`, `npm run build`, `npm run typecheck`, `npm run lint`.

## 11. Menjalankan Project Secara Lokal

Langkah ini berasal dari `docker-compose.yml` dan dokumentasi di `docs/logs/LOG-001.md`. Konfigurasi ini adalah environment **development**, bukan konfigurasi produksi.

```bash
cp .env.example .env                              # opsional: ubah port/kredensial lokal
docker compose up --build                         # menjalankan 4 service
docker compose exec backend php artisan migrate --force
docker compose exec backend php artisan test       # butuh database test PostgreSQL
```

| Service | URL default | Catatan |
|---|---|---|
| frontend | http://localhost:5173 | Vite dev server |
| backend | http://localhost:8000 | Laravel dev server, API di `/api/v1` |
| postgres | localhost:5432 | user/database mengikuti `.env` root |
| redis | localhost:6379 | dipakai rate limiter |

Reset database (menghapus seluruh data lokal):

```bash
docker compose down -v
docker compose up --build
docker compose exec backend php artisan migrate --force
```

Catatan penting: branch `main` tidak memuat pekerjaan frontend (routing, auth, lokalisasi, Home, Cabin Detail, Booking). Untuk melihat frontend tersebut, periksa branch `feature/frontend-foundation` (dan perubahan working tree yang belum di-commit untuk halaman Booking).

## 12. Cara Membaca Repository Ini

Jika suatu hari repository ini dibuka kembali:

1. Baca README ini, khususnya bagian status project, timeline, dan status fitur.
2. Periksa `git log` — tanggal dan urutan commit adalah sumber status implementasi yang paling jujur. Perhatikan juga branch mana yang berisi pekerjaan apa.
3. Periksa `docs/logs/LOG-001.md` sampai `LOG-009.md` untuk memahami apa yang dikerjakan, keputusan teknis, dan hasil verifikasi di setiap tahap.
4. Periksa `docs/Technical-Design-Villa-Dieng.md` untuk skema database, aturan booking, dan pembahasan concurrency.
5. Jangan menganggap `docs/PRD.md` dan `docs/DESIGN.md` sebagai daftar fitur yang sudah selesai; keduanya adalah rencana (lihat bagian 9).
6. Gunakan `backend/routes/api.php` dan `frontend/src/app/router.tsx` sebagai daftar endpoint dan halaman yang benar-benar ada, dan `git status` untuk melihat pekerjaan yang belum di-commit.

## 13. Alasan Project Dihentikan

Project ini dihentikan pada 23 September 2026, bukan karena repository rusak. Seluruh pekerjaan yang tercatat di `main` tetap dapat dijalankan dan diuji; tidak ada kerusakan teknis yang memaksa penghentian.

Alasan yang sebenarnya adalah scope dan kompleksitas:

- Scope berkembang menjadi booking engine yang cukup kompleks — reservation, payment, availability block, reservation event, state machine, expiration, dan aturan anti-double-booking — sebelum pemahaman yang cukup kuat terhadap domain bisnis dan keseluruhan sistem dimiliki.
- Akibatnya, proses development menjadi sangat bergantung pada perencanaan dan implementasi bertahap (per branch, per log, per commit) tanpa gambaran menyeluruh tentang produk yang sedang dibangun.
- Backend dibangun lebih dulu dan lebih dalam daripada pemahaman terhadap alur pengguna akhir. Ketika pekerjaan mencapai frontend, sebagian besar keputusan domain sudah terlanjur diambil dan harus dipahami ulang.
- Keputusan akhir: menghentikan project ini dan memulai project baru dengan pendekatan yang lebih sederhana, lebih terukur, dan benar-benar dipahami dari awal.

Penghentian dilakukan secara sadar oleh pengembang, bukan karena kegagalan teknis, bukan karena keterbatasan satu teknologi tertentu, dan bukan karena satu penyebab eksternal tunggal.

## 14. Pelajaran dari Project

Catatan berikut diambil dari pola yang terlihat pada repository dan riwayat commit project ini.

1. **Pahami domain sebelum memilih arsitektur.** Struktur folder, state machine, dan lapisan Action/Domain dibangun lebih dulu, sementara aturan bisnis yang sebenarnya (kebijakan pembatalan, siapa yang boleh memblokir tanggal, siapa yang mengonfirmasi) masih belum konvergen.
2. **Batasi MVP secara eksplisit.** `docs/PRD.md` memuat daftar luas (MVP, V2, Later) yang jauh melampaui kapasitas satu pengembang. Dokumen itu sendiri menjadi sumber kompleksitas, bukan alat pemfokusan.
3. **Pahami alur pengguna sebelum implementasi.** Backend lebih dulu lengkap; frontend datang belakangan. Halaman seperti `/checkout`, `/payment`, dan `/booking/success` sudah punya route jauh sebelum perilakunya dipahami, sehingga yang tersedia hanya placeholder.
4. **Jangan membangun infrastruktur terlalu jauh sebelum kebutuhan produk jelas.** Docker, Redis, rate limiter bertingkat, dan suite PostgreSQL disiapkan pada tahap awal; sebagian belum benar-benar terpakai (misalnya cache/queue Redis belum dipakai untuk data bisnis, `services/guest.ts` belum dipanggil halaman mana pun, dan test contoh Laravel masih tertinggal).
5. **Dokumentasi harus membantu pemahaman, bukan menjadi beban.** Setiap perubahan menghasilkan log panjang dengan banyak tabel, keputusan, dan audit verifikasi. Biayanya besar dan pada akhirnya menjadi pekerjaan tersendiri, sementara gambaran produk tetap sulit dipahami.
6. **Kompleksitas project harus mengikuti kebutuhan, bukan sebaliknya.** Constraint `EXCLUDE`, tiga lapis pengecekan availability, dan state machine dua arah adalah desain yang matang untuk sistem berskala besar — tetapi untuk satu villa yang baru mulai dijual, beban tersebut tidak sebanding dengan nilainya di tahap ini.

## 15. Project Pengganti

Project ini digantikan oleh project baru, `[NAMA PROJECT BARU]`, yang dibuat dari awal dengan prinsip:

- domain lebih sederhana
- scope lebih kecil dan dibatasi secara eksplisit
- seluruh business flow dipahami oleh pengembang
- teknologi dipilih setelah kebutuhan dipahami, bukan sebelumnya
- development dilakukan secara bertahap dari fitur yang paling penting
- setiap fitur harus dapat dijelaskan secara manual oleh pengembang

Tujuan praktisnya: membangun pemahaman terhadap domain terlebih dahulu, lalu menulis kode sesedikit mungkin untuk memenuhi kebutuhan yang sudah jelas. Repository lama ini dipertahankan sebagai referensi bila diperlukan (misalnya untuk skema database booking, penanganan konflik jadwal, atau pola API), bukan sebagai basis yang dilanjutkan.

## 16. Status Repository

| Item | Nilai |
|---|---|
| Status | Archived — tidak ada development aktif |
| Development terakhir | 23 September 2026 |
| Branch utama | `main`, commit terakhir `6943adb` (2026-09-22) — berisi backend sampai API layer |
| Branch kerja terakhir | `feature/frontend-foundation`, commit `fc34374` (2026-09-23) — 16 commit di depan `main` |
| Pekerjaan belum di-commit | Perubahan working tree pada `frontend/src/app/router.tsx`, `frontend/src/i18n/dictionaries.ts`, `frontend/src/pages/CabinDetail.tsx`, dan file baru `frontend/src/pages/Booking.tsx`, `frontend/src/services/booking.ts`, `docs/logs/LOG-009.md` |
| Branch lokal lain | `feature/backend-foundation`, `feature/backend-api-layer`, `feature/domain-foundation`, `feature/domain-database-foundation`, `feature/reservation-booking-domain`, `feature/docker-foundation`, `chore/project-foundation` |

Semua branch tersebut sudah menjadi riwayat yang selesai; tidak ada branch yang masih dikembangkan. Repository ini dipertahankan sebagai arsip pembelajaran dan referensi historis. Tidak ada development aktif yang direncanakan.

## 17. Catatan Akhir

Repository ini adalah dokumentasi perjalanan pengembangan sebuah project lama: apa yang direncanakan, apa yang benar-benar dibangun, apa yang berhenti di tengah jalan, dan mengapa diputuskan untuk dihentikan pada 23 September 2026. Repository ini bukan project yang sedang aktif dikembangkan, dan tidak ada rencana untuk melanjutkannya.

Yang perlu diingat ketika membukanya kembali: baca bagian status dan timeline terlebih dahulu, gunakan `git log` dan `docs/logs/` sebagai sumber kebenaran status implementasi, dan jangan menganggap seluruh rencana di `docs/PRD.md` serta `docs/DESIGN.md` sudah selesai.
