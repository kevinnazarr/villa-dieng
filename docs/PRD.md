# PRD — Cabin Villa Dieng Booking Engine

**Status:** Draft v1.0  
**Product type:** Direct-to-consumer villa booking website + booking engine + admin dashboard  
**Target:** Production-ready technical demo / portfolio-grade product  
**Primary market:** Wisatawan domestik Indonesia, dengan dukungan bahasa Inggris untuk wisatawan internasional  
**MVP inventory:** 1 cabin atau beberapa cabin identik  
**Booking model:** Per malam  
**Payment:** Sandbox/simulasi  
**Architecture:** Monorepo — Laravel REST API + React/Vite + Tailwind CSS v4 + shadcn/ui + PostgreSQL + Redis + Docker

---

## 1. Problem Statement

Villa/cabin eksklusif di kawasan wisata Dieng membutuhkan kanal reservasi langsung yang memberikan pengalaman pemesanan sederhana, profesional, dan terpercaya tanpa bergantung sepenuhnya pada OTA atau komunikasi manual melalui WhatsApp.

### Masalah utama

**Wisatawan dirugikan karena:**

- Tidak dapat mengetahui ketersediaan cabin secara langsung dan real-time.
- Harus bertanya melalui chat untuk mengetahui tanggal kosong.
- Risiko mendapatkan informasi availability yang sudah berubah.
- Proses booking terlalu panjang atau tidak terstruktur.
- Tidak mendapatkan konfirmasi pemesanan yang konsisten.
- Wisatawan internasional menghadapi hambatan bahasa.
- Tidak memiliki satu tempat untuk melihat detail villa, fasilitas, harga, aturan, dan status reservasi.

**Pengelola villa dirugikan karena:**

- Reservasi manual meningkatkan risiko *double booking*.
- Data booking tersebar di chat, spreadsheet, atau platform lain.
- Sulit mendapatkan overview occupancy dan reservation status.
- Perubahan availability harus dilakukan secara manual.
- Tidak memiliki sistem terpusat untuk mengelola reservasi.
- Brand terlihat kurang profesional dibanding pengalaman booking digital modern.

### Problem statement inti

> **Wisatawan membutuhkan cara yang cepat, transparan, dan terpercaya untuk mengetahui availability dan memesan cabin di Dieng secara langsung, sementara pengelola membutuhkan sistem terpusat yang mencegah bentrok reservasi dan memudahkan pengelolaan booking.**

---

# 2. Target User + Persona

## Target User

### Primary

1. **Wisatawan domestik**
   - Individu
   - Pasangan
   - Keluarga kecil
   - Usia kira-kira 20–45 tahun
   - Mobile-first
   - Mencari pengalaman menginap yang unik dan nyaman.

2. **Wisatawan internasional**
   - Pasangan atau small group
   - Membutuhkan interface berbahasa Inggris.
   - Membutuhkan informasi villa yang jelas sebelum booking.

### Secondary

**Admin/pengelola villa**

Bukan customer utama, tetapi merupakan pengguna utama dashboard internal.

### Explicitly out of MVP

- Travel agent
- Corporate booking
- Reseller
- Affiliate
- Group booking kompleks

---

## Persona 1 — Raka

**Profil:** 29 tahun, software engineer, tinggal di Jakarta.

**Behavior:**

- Mencari villa melalui Google/social media.
- Membandingkan beberapa tempat sebelum booking.
- Lebih nyaman melakukan booking sendiri daripada chat admin.
- Menggunakan smartphone.
- Mengharapkan informasi harga dan availability secara transparan.

**Pain points:**

- "Tanggal yang saya mau kosong atau tidak?"
- "Total harga akhirnya berapa?"
- "Booking saya benar-benar tercatat atau belum?"

**Goal:**

> Memesan cabin untuk weekend dalam beberapa menit tanpa harus menunggu respons admin.

---

## Persona 2 — Sarah

**Profil:** 34 tahun, wisatawan dari Singapura.

**Behavior:**

- Menggunakan bahasa Inggris.
- Membutuhkan informasi fasilitas, lokasi, kapasitas, dan aturan dengan jelas.
- Terbiasa menggunakan online booking.

**Pain points:**

- Website hanya tersedia dalam Bahasa Indonesia.
- Informasi booking tidak jelas.
- Tidak mengetahui apakah reservation berhasil.

**Goal:**

> Memahami properti dan menyelesaikan reservasi secara mandiri dengan interface berbahasa Inggris.

---

## Persona 3 — Admin Villa

**Profil:** Pengelola operasional villa.

**Goal:**

- Mengetahui booking masuk.
- Mengelola availability.
- Mengubah status reservation.
- Melihat kalender okupansi.
- Menghindari double booking.

---

# 3. Goals dan Non-Goals

## Product Goals

### G1 — Direct booking

Memungkinkan customer menyelesaikan proses:

**Discover → Check availability → Select dates → Guest details → Payment simulation → Confirmation**

tanpa intervensi admin.

### G2 — Prevent double booking

Sistem harus menjamin dua reservation yang valid tidak dapat menguasai inventory yang sama pada tanggal yang sama.

### G3 — Professional digital presence

Website harus sekaligus berfungsi sebagai:

- brand website,
- property showcase,
- booking engine.

### G4 — Admin operability

Admin dapat mengelola reservation dan availability melalui dashboard terpisah.

### G5 — Portfolio-grade architecture

Implementasi harus menunjukkan:

- REST API,
- authentication,
- authorization,
- database design,
- transactional booking,
- concurrency handling,
- localization,
- payment state machine,
- validation,
- testing,
- Dockerized deployment.

### G6 — Deployment-ready

Aplikasi dapat dijalankan secara konsisten melalui Docker dan dideploy ke VPS.

---

## Non-Goals MVP

MVP **tidak** mencakup:

- Real payment gateway.
- Automatic payout.
- OTA integration.
- Travel agent portal.
- Corporate booking.
- Reseller system.
- Channel manager.
- Coupon engine kompleks.
- Loyalty program.
- Referral program.
- Multiple property management.
- Advanced revenue management.
- Native mobile app.

---

# 4. User Stories

## Guest / Customer

### Discovery

- Sebagai wisatawan, saya ingin melihat informasi cabin supaya saya dapat menentukan apakah properti sesuai kebutuhan saya.
- Sebagai wisatawan, saya ingin melihat foto dan fasilitas supaya saya dapat memahami pengalaman menginap.
- Sebagai wisatawan, saya ingin melihat harga dasar supaya saya dapat memperkirakan budget.
- Sebagai wisatawan, saya ingin melihat aturan menginap supaya saya dapat memastikan cocok dengan kebutuhan saya.
- Sebagai wisatawan, saya ingin mengganti bahasa ID/EN supaya saya dapat memahami informasi dengan nyaman.

### Availability

- Sebagai wisatawan, saya ingin memilih tanggal check-in dan check-out supaya saya dapat mengetahui apakah cabin tersedia.
- Sebagai wisatawan, saya ingin melihat tanggal yang tidak tersedia supaya saya tidak memilih tanggal yang sudah dipesan.
- Sebagai wisatawan, saya ingin mendapatkan availability terbaru sebelum pembayaran supaya saya tidak membayar reservation yang sudah tidak tersedia.

### Booking

- Sebagai wisatawan, saya ingin memasukkan data diri supaya reservation dapat dikaitkan dengan saya.
- Sebagai wisatawan, saya ingin melakukan guest checkout supaya saya tidak wajib membuat akun terlebih dahulu.
- Sebagai wisatawan, saya ingin akun dibuat otomatis setelah booking supaya saya dapat menggunakan sistem tanpa proses registrasi terpisah.
- Sebagai wisatawan, saya ingin melihat breakdown harga supaya saya memahami total pembayaran.
- Sebagai wisatawan, saya ingin melakukan pembayaran sandbox supaya saya dapat menyelesaikan simulasi reservation.
- Sebagai wisatawan, saya ingin menerima booking confirmation supaya saya memiliki bukti reservation.
- Sebagai wisatawan, saya ingin melihat booking reference supaya saya dapat mengidentifikasi reservation saya.

### Account

- Sebagai customer, saya ingin login setelah akun otomatis dibuat supaya saya dapat melihat reservation saya.
- Sebagai customer, saya ingin melihat riwayat booking supaya saya dapat mengetahui reservation sebelumnya.

---

## Admin

- Sebagai admin, saya ingin login ke dashboard supaya hanya staff berwenang yang dapat mengelola reservation.
- Sebagai admin, saya ingin melihat daftar booking supaya saya dapat memonitor reservation.
- Sebagai admin, saya ingin melihat detail booking supaya saya dapat memeriksa data guest dan reservation.
- Sebagai admin, saya ingin mengubah status booking supaya status operasional selalu akurat.
- Sebagai admin, saya ingin melihat calendar availability supaya saya mengetahui occupancy.
- Sebagai admin, saya ingin melakukan blocking tanggal supaya cabin tidak dapat dibooking pada periode tertentu.
- Sebagai admin, saya ingin melihat data customer supaya saya dapat menangani kebutuhan operasional.
- Sebagai admin, saya ingin melihat payment status supaya saya mengetahui apakah booking telah menyelesaikan payment flow.

---

# 5. Feature Roadmap

## MVP

### Public Website

- Homepage
- Hero/property showcase
- Cabin detail
- Gallery
- Facilities
- Location
- House rules
- FAQ
- Contact
- Responsive/mobile-first UI
- ID/EN localization

### Booking Engine

- Date picker
- Availability checking
- Guest count
- Price calculation
- Booking summary
- Guest checkout
- Automatic account creation
- Reservation creation
- Booking reference
- Booking status
- Payment sandbox
- Payment result handling
- Confirmation page

### Customer Account

- Login
- Logout
- Profile
- Booking history
- Booking detail

### Admin

- Admin authentication
- Dashboard overview
- Reservation list
- Reservation detail
- Reservation status management
- Availability calendar
- Manual date blocking
- Basic customer management
- Payment status

### Infrastructure

- PostgreSQL
- Redis
- Docker
- Laravel queues
- Email simulation/logging
- API validation
- Authentication
- Authorization
- Error handling
- Logging
- Automated tests

---

## V2

### Commercial features

- Multiple cabin types
- Multiple cabin inventory
- Seasonal pricing
- Weekend pricing
- Holiday pricing
- Promo codes
- Discount
- Extra guest pricing
- Additional amenities

### Distribution

- WhatsApp integration
- OTA/channel manager
- iCal synchronization
- Travel agent booking
- Corporate booking
- Reseller

### Customer experience

- Booking cancellation
- Reschedule
- Reviews
- Wishlist
- Saved guest information
- Advanced email notifications

---

## Later

- Real payment gateway
- Multi-property management
- Dynamic pricing
- Revenue management
- Loyalty program
- Affiliate system
- Referral program
- Mobile application
- CRM
- Marketing automation
- Advanced analytics
- AI concierge
- Automated upselling

---

# 6. Functional Requirements — MVP

## 6.1 Public Website

### FR-PUB-001 — Homepage

Homepage harus menyediakan:

- Property hero image
- Property name
- Short description
- Primary CTA: **Book Now**
- Secondary CTA: **Explore Cabin**
- Highlight facilities
- Gallery preview
- Location
- Booking CTA
- Footer

### FR-PUB-002 — Cabin Detail

Menampilkan:

- Nama cabin
- Description
- Capacity
- Bed configuration
- Facilities
- Gallery
- Base price
- House rules
- Check-in/check-out information
- CTA booking

### FR-PUB-003 — Gallery

Customer dapat:

- melihat image thumbnails,
- membuka image viewer,
- melakukan navigasi antar gambar.

Image harus memiliki alt text untuk accessibility.

---

# 6.2 Localization

### FR-I18N-001

System mendukung:

- `id`
- `en`

### FR-I18N-002

User dapat mengganti bahasa dari public website.

### FR-I18N-003

Language preference harus dipertahankan selama session.

### FR-I18N-004

Konten berikut minimal harus localized:

- navigation,
- CTA,
- form labels,
- validation errors,
- booking status,
- payment status,
- confirmation message,
- house rules,
- core property content.

**Open question:** apakah konten property akan disimpan multilingual di database atau cukup didefinisikan sebagai static translation pada frontend?

---

# 6.3 Availability

Ini adalah komponen kritis sistem.

### FR-AVL-001

Customer memilih:

- check-in date,
- check-out date,
- jumlah dewasa,
- jumlah anak.

### FR-AVL-002

System harus memastikan:

`check_out > check_in`

### FR-AVL-003

Tanggal harus menggunakan timezone properti.

Untuk Dieng:

**Asia/Jakarta**

### FR-AVL-004

Availability harus dihitung berdasarkan reservation yang masih memblokir inventory.

Contoh:

Booking A:

`10 Jan → 12 Jan`

Maka cabin tidak tersedia untuk:

- 10 Jan
- 11 Jan

Check-in baru diperbolehkan pada:

`12 Jan`

jika policy property mengizinkan.

### FR-AVL-005

Availability tidak boleh hanya divalidasi di frontend.

Backend harus melakukan final availability validation.

### FR-AVL-006

Backend harus menangani concurrent booking.

Flow ideal:

```text
Request booking
      ↓
Validate request
      ↓
Begin DB transaction
      ↓
Lock relevant inventory/resource
      ↓
Re-check availability
      ↓
Create reservation
      ↓
Commit
```

Tujuannya mencegah:

```text
Customer A ─┐
            ├── same cabin/date ──> only one succeeds
Customer B ─┘
```

---

# 6.4 Guest Checkout

### FR-BOOK-001

User tidak diwajibkan login untuk membuat reservation.

### FR-BOOK-002

Form minimal:

- Full name
- Email
- Phone
- Country
- Adults
- Children
- Special request — optional

### FR-BOOK-003

System melakukan validation:

- name required
- valid email
- valid phone
- valid guest count
- capacity validation

### FR-BOOK-004

System membuat atau menemukan customer berdasarkan email.

### FR-BOOK-005

Jika customer belum memiliki account:

```text
Customer
   ↓
Create account
   ↓
Create reservation
```

Account dibuat dengan status appropriate untuk post-booking activation.

**Open question:** mekanisme aktivasi account apakah melalui magic link/email verification atau password sementara + reset password?

---

# 6.5 Pricing

Untuk MVP:

```text
nightly_rate × number_of_nights
```

Contoh:

```text
Rp1.000.000 × 2 malam
= Rp2.000.000
```

### FR-PRICE-001

Backend menjadi source of truth untuk harga.

Frontend tidak boleh menentukan total final.

### FR-PRICE-002

Booking summary harus menunjukkan:

- Check-in
- Check-out
- Nights
- Guest count
- Base rate
- Subtotal
- Additional fee jika ada
- Total

### FR-PRICE-003

Harga yang digunakan pada reservation harus disimpan sebagai snapshot.

Perubahan harga berikutnya tidak boleh mengubah reservation lama.

---

# 6.6 Reservation

### FR-RES-001

Reservation memiliki unique booking reference.

Contoh format:

`CAB-20260921-A8F3`

Format final masih dapat ditentukan.

### FR-RES-002

Reservation memiliki lifecycle:

```text
PENDING_PAYMENT
       ↓
PAID
       ↓
CONFIRMED
```

Dengan kemungkinan:

```text
PENDING_PAYMENT → EXPIRED
PENDING_PAYMENT → PAYMENT_FAILED
CONFIRMED → CANCELLED
```

Status final perlu dikunci dalam business rule.

### FR-RES-003

Reservation menyimpan snapshot:

- guest information
- dates
- guest count
- cabin
- nightly rate
- total amount
- payment status

### FR-RES-004

Reservation harus memiliki timestamps:

- created_at
- updated_at
- confirmed_at
- cancelled_at
- expired_at

Jika relevan.

---

# 6.7 Sandbox Payment

Payment bukan transaksi uang nyata.

### FR-PAY-001

User dapat memilih metode payment sandbox yang tersedia.

Contoh:

- Sandbox Card
- Sandbox Success
- Sandbox Failed

### FR-PAY-002

Payment harus memiliki status:

```text
PENDING
SUCCESS
FAILED
EXPIRED
```

### FR-PAY-003

Payment success harus mengubah reservation sesuai state transition yang valid.

### FR-PAY-004

Payment failure tidak boleh menghasilkan reservation confirmed.

### FR-PAY-005

Payment operation harus idempotent.

Request payment callback yang sama dua kali tidak boleh membuat dua payment record atau mengubah booking secara tidak konsisten.

---

# 6.8 Confirmation

Setelah successful payment:

Customer melihat:

- Success state
- Booking reference
- Property
- Guest name
- Check-in
- Check-out
- Guest count
- Total
- Payment status

### FR-CON-001

System menghasilkan confirmation email.

Pada MVP, email dapat diarahkan ke mail trap/logging environment.

### FR-CON-002

Email harus menggunakan bahasa yang sesuai dengan booking/session locale.

---

# 6.9 Customer Account

### FR-AUTH-001

Customer dapat:

- login,
- logout.

### FR-AUTH-002

Customer dapat melihat:

`My Bookings`

### FR-AUTH-003

Customer hanya dapat mengakses reservation miliknya sendiri.

Authorization harus dilakukan di backend.

---

# 6.10 Admin Authentication

### FR-ADM-001

Admin dashboard tidak menggunakan authorization berdasarkan frontend saja.

Backend wajib melakukan authorization.

### FR-ADM-002

Minimal role:

`ADMIN`

Struktur dapat diperluas menjadi:

```text
SUPER_ADMIN
ADMIN
```

pada versi berikutnya.

### FR-ADM-003

Admin yang tidak terautentikasi tidak dapat mengakses administrative API.

---

# 6.11 Admin Dashboard

Dashboard menampilkan minimal:

- Total reservations
- Pending payments
- Confirmed bookings
- Cancelled bookings
- Upcoming check-ins
- Upcoming check-outs
- Current occupancy indicator

### Reservation table

Kolom:

- Booking reference
- Guest
- Check-in
- Check-out
- Guests
- Amount
- Payment status
- Reservation status
- Created date

Admin dapat:

- search,
- filter,
- sort,
- membuka detail.

---

# 6.12 Availability Management

Admin dapat melihat calendar.

Calendar menunjukkan:

```text
AVAILABLE
BOOKED
BLOCKED
```

Admin dapat membuat availability block:

```text
Block from: 20 Dec
Block until: 22 Dec
Reason: Maintenance
```

### FR-AVL-ADMIN-001

Blocked date tidak dapat dibooking customer.

### FR-AVL-ADMIN-002

Admin tidak boleh membuat block yang menyebabkan konflik dengan confirmed reservation tanpa explicit override workflow.

**Open question:** apakah admin boleh melakukan override terhadap reservation existing? Untuk MVP, rekomendasi requirement awal: **tidak boleh**.

---

# 6.13 API

Minimal API domains:

```text
/api/v1/auth
/api/v1/properties
/api/v1/cabins
/api/v1/availability
/api/v1/reservations
/api/v1/payments
/api/v1/customer
/api/v1/admin
```

API harus memiliki:

- request validation,
- consistent response structure,
- authentication,
- authorization,
- pagination,
- filtering,
- rate limiting untuk endpoint sensitif,
- structured errors.

---

# 6.14 Security

MVP wajib menerapkan:

- password hashing,
- authentication token/session security,
- authorization,
- CSRF strategy sesuai architecture,
- input validation,
- mass-assignment protection,
- rate limiting,
- secure headers,
- environment secret management,
- SQL injection protection melalui ORM/query binding,
- audit logging untuk administrative actions.

Admin API harus memiliki protection lebih ketat dibanding public endpoints.

---

# 7. Sketsa Data Model

Berikut model awal yang cukup untuk MVP tanpa overengineering.

## User

```text
users
---------
id
name
email
password
role
email_verified_at
created_at
updated_at
```

`role`:

```text
CUSTOMER
ADMIN
```

---

## Property

```text
properties
----------
id
name
slug
description
location
timezone
check_in_time
check_out_time
status
created_at
updated_at
```

---

## Cabin

```text
cabins
------
id
property_id
name
slug
description
max_adults
max_children
max_guests
base_price
status
created_at
updated_at
```

MVP dapat memiliki satu record.

---

## Cabin Images

```text
cabin_images
------------
id
cabin_id
url
alt_text
sort_order
created_at
updated_at
```

---

## Amenities

```text
amenities
---------
id
name
icon
created_at
updated_at
```

Pivot:

```text
cabin_amenity
-------------
cabin_id
amenity_id
```

---

## Reservations

```text
reservations
------------
id
booking_reference
user_id
cabin_id
check_in
check_out
adults
children
guest_name
guest_email
guest_phone

nightly_rate
nights
subtotal
fees
total_amount
currency

status
locale

confirmed_at
cancelled_at
expired_at

created_at
updated_at
```

---

## Payments

```text
payments
--------
id
reservation_id
reference
provider
method
amount
currency
status
paid_at
failed_at
metadata
created_at
updated_at
```

---

## Availability Blocks

```text
availability_blocks
-------------------
id
cabin_id
start_date
end_date
reason
created_by
created_at
updated_at
```

---

## Reservation Events

Untuk auditability:

```text
reservation_events
------------------
id
reservation_id
event
old_status
new_status
actor_id
metadata
created_at
```

Contoh event:

```text
RESERVATION_CREATED
PAYMENT_SUCCESS
RESERVATION_CONFIRMED
RESERVATION_CANCELLED
```

---

# 8. Edge Cases & Failure States

## Availability

### E1 — Dua user booking tanggal sama secara bersamaan

**Expected:**

Satu request berhasil.

Request kedua:

```text
409 Conflict
```

Message:

> Cabin is no longer available for the selected dates.

### E2 — User membuka booking page lama

Availability dapat berubah setelah page dibuka.

**Expected:**

Backend melakukan availability check ulang saat reservation creation/payment.

### E3 — Check-out sebelum check-in

Reject:

```text
422 Unprocessable Entity
```

### E4 — Check-in = check-out

Reject.

Minimum:

`1 night`

### E5 — Capacity exceeded

Misalnya:

```text
2 adults + 2 children
```

sementara capacity:

```text
2 adults + 1 child
```

Booking ditolak.

---

## Payment

### E6 — Payment failed

Reservation tidak menjadi confirmed.

### E7 — Payment callback dua kali

System harus idempotent.

Result:

```text
one payment
one reservation state transition
```

### E8 — Payment berhasil tetapi response timeout

Customer tidak boleh langsung dianggap gagal.

Frontend dapat menampilkan:

> Payment status is being verified.

Backend menjadi source of truth.

### E9 — Reservation pending terlalu lama

Reservation harus dapat masuk status:

`EXPIRED`

berdasarkan expiry policy.

**Open question:** berapa lama reservation pending payment dipertahankan? Contoh kandidat: 10–15 menit.

---

## Account

### E10 — Email sudah memiliki account

System tidak membuat duplicate user.

### E11 — Guest checkout menggunakan email account existing tetapi belum login

System harus memiliki flow yang aman.

**Open question:** apakah user harus login terlebih dahulu, atau menggunakan verification/magic link?

---

## Admin

### E12 — Admin mencoba block tanggal yang sudah booked

Reject.

### E13 — Customer mencoba membuka booking orang lain

Return:

```text
403 Forbidden
```

atau resource tidak ditemukan sesuai authorization strategy.

### E14 — Admin session expired

Redirect/login kembali dan request API harus ditolak secara aman.

---

## Infrastructure

### E15 — Database unavailable

API mengembalikan generic service error tanpa membocorkan detail database.

### E16 — Email service gagal

Booking tidak boleh gagal hanya karena email gagal.

Reservation tetap tersimpan; email dikirim melalui queue/retry.

### E17 — Redis unavailable

System harus memiliki defined fallback behavior.

**Open question:** apakah Redis dianggap mandatory runtime dependency atau hanya optimization layer?

---

# 9. Success Metrics

Karena ini merupakan produk portfolio yang juga dirancang seperti production system, metric harus mencakup **product**, **booking**, dan **technical quality**.

## Product Metrics

### Booking conversion

```text
Completed bookings
/
Booking attempts
```

Target awal:

> ≥ 5% pada traffic demo/realistic test traffic.

Angka ini perlu dianggap sebagai baseline awal, bukan business guarantee.

### Checkout completion

```text
Completed payment
/
Started checkout
```

Target:

> ≥ 70%

---

## Technical Metrics

### Double booking

Target:

> **0 confirmed double bookings**

Ini merupakan critical invariant.

### API error rate

Target:

> < 1% untuk normal application requests.

### Availability accuracy

Target:

> 100% consistency antara booking constraint di backend dan availability yang ditampilkan.

### Test coverage

Target awal:

- Critical booking domain: **≥90%**
- Overall backend: **≥80%**

Coverage bukan satu-satunya indikator kualitas; integration/feature tests lebih penting untuk booking flow.

---

## Performance

Target baseline:

- Public page interaction responsive pada mobile.
- API p95 untuk simple read endpoints: **<500 ms** pada deployment baseline.
- Availability query p95: **<500 ms** pada inventory MVP.

Angka final harus divalidasi dengan load testing.

---

## Reliability

Critical booking flow:

```text
Search availability
→ create reservation
→ payment
→ confirmation
```

harus memiliki integration test end-to-end.

Target:

> 100% pass rate pada critical automated test suite sebelum release.

---

## Admin efficiency

Target:

> Admin dapat menemukan reservation tertentu dalam ≤30 detik melalui search/filter.

---

# 10. Open Questions

Berikut hal-hal yang **sengaja tidak diasumsikan** dan perlu diputuskan sebelum implementation specification dikunci.

## Product / Business

1. Nama brand/property final apa?
2. Apakah MVP benar-benar hanya memiliki **1 cabin**, atau beberapa cabin identik?
3. Berapa **harga per malam final**?
4. Apakah harga berbeda berdasarkan weekday/weekend?
5. Apakah ada minimum stay?
6. Jam check-in dan check-out?
7. Apakah ada cleaning/service fee?
8. Apakah pajak sudah termasuk harga?
9. Apakah anak dihitung sebagai guest dalam capacity calculation?
10. Apakah extra bed tersedia?

## Reservation Policy

11. Berapa lama reservation `PENDING_PAYMENT` sebelum `EXPIRED`?
12. Apakah customer boleh membatalkan booking?
13. Apakah customer boleh mengubah tanggal?
14. Apakah admin dapat membatalkan confirmed booking?
15. Bagaimana policy refund pada versi production nanti?

## Account

16. Bagaimana account otomatis dibuat?
    - Password sementara?
    - Magic link?
    - Email verification + set password?

17. Apakah email verification wajib sebelum customer dapat login?

## Payment

18. Sandbox payment akan dimodelkan sebagai:
    - fake internal payment gateway,
    - atau integration dengan provider sandbox tertentu?

19. Apakah payment success langsung membuat `CONFIRMED`, atau perlu status intermediary?

## Content

20. Siapa yang mengelola:
    - foto,
    - description,
    - amenities,
    - FAQ,
    - house rules?

21. Apakah admin harus dapat mengedit seluruh content website dari dashboard, atau content MVP cukup melalui database/configuration?

## Infrastructure

22. Deployment target VPS apa?
23. Apakah menggunakan:
    - Nginx,
    - Docker Compose,
    - PostgreSQL container,
    - Redis container,
    - Laravel queue worker,
    - scheduler?

24. Apakah email simulation menggunakan Mailpit/Mailhog atau service lain?

## Architecture

25. Authentication frontend/backend akan menggunakan:
    - Laravel Sanctum SPA authentication,
    - atau token-based authentication?

26. Apakah React frontend dan Laravel API berada pada domain yang sama atau berbeda?

27. Apakah public website membutuhkan SSR/SEO tingkat lanjut?  
    Karena React/Vite murni memiliki konsekuensi SEO dibanding Next.js.

---

# MVP Definition of Done

MVP dinyatakan **DONE** apabila seluruh kondisi berikut terpenuhi.

## Customer

- [ ] User dapat membuka website.
- [ ] User dapat mengganti ID/EN.
- [ ] User dapat melihat detail cabin.
- [ ] User dapat memilih tanggal.
- [ ] Availability dihitung oleh backend.
- [ ] User dapat melakukan guest checkout.
- [ ] User dapat membuat reservation.
- [ ] System mencegah double booking.
- [ ] Total harga dihitung server-side.
- [ ] User dapat menjalankan sandbox payment.
- [ ] Payment success menghasilkan booking confirmed.
- [ ] Payment failure tidak menghasilkan confirmed booking.
- [ ] User mendapatkan booking reference.
- [ ] Confirmation page tersedia.
- [ ] Confirmation email tercatat di mail trap/log.
- [ ] Account customer dibuat otomatis.
- [ ] Customer dapat login.
- [ ] Customer dapat melihat booking history.

## Admin

- [ ] Admin dapat login.
- [ ] Admin dashboard tersedia terpisah.
- [ ] Admin dapat melihat reservation.
- [ ] Admin dapat melihat detail reservation.
- [ ] Admin dapat filter/search reservation.
- [ ] Admin dapat melihat availability calendar.
- [ ] Admin dapat block tanggal.
- [ ] Block mencegah booking.
- [ ] Admin tidak dapat melakukan aksi yang tidak authorized.

## Engineering

- [ ] Laravel REST API terstruktur.
- [ ] React/Vite frontend terstruktur.
- [ ] Tailwind CSS v4 digunakan.
- [ ] shadcn/ui digunakan untuk UI components.
- [ ] PostgreSQL digunakan.
- [ ] Redis digunakan sesuai kebutuhan yang ditetapkan.
- [ ] Docker development/deployment environment tersedia.
- [ ] Environment configuration terdokumentasi.
- [ ] Critical booking flow memiliki automated tests.
- [ ] Concurrency/double-booking scenario memiliki automated test.
- [ ] API validation dan authorization teruji.
- [ ] Error handling konsisten.
- [ ] Logging tersedia.
- [ ] Queue/email failure tidak merusak reservation.
- [ ] Production build berhasil.
- [ ] Deployment ke VPS dapat dilakukan menggunakan documented procedure.

---

# Engineering Priority

Untuk proyek ini, **booking consistency** adalah requirement teknis paling kritis. UI yang bagus tidak dapat mengompensasi reservation engine yang dapat menghasilkan double booking.

Urutan prioritas engineering:

```text
1. Reservation correctness
2. Availability consistency
3. Payment state integrity
4. Authentication & authorization
5. Data integrity
6. API architecture
7. Admin operability
8. UX/UI polish
9. Performance optimization
10. Secondary features
```

Dengan scope tersebut, MVP sudah cukup kuat untuk berfungsi sebagai **realistic booking platform**, bukan sekadar landing page dengan form booking palsu.
