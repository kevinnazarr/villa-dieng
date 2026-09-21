# Design Brief — Cabin Villa Dieng Booking Engine

**Status:** Design specification / pre-development  
**Produk:** Website profil + direct booking engine + customer account + admin dashboard  
**Primary stack:** React/Vite + Tailwind CSS v4 + shadcn/ui  
**Primary audience:** Wisatawan domestik, pasangan/keluarga kecil, wisatawan internasional  
**Brand character:** Exclusive cabin, intimate, nature-driven, calm, premium  
**Primary conversion:** Direct cabin booking

---

# 1. Design Principles

## Principle 01 — Nature-first, not hotel-generic

Interface harus terasa seperti **cabin di dataran tinggi Dieng**, bukan website hotel korporat.

Visual harus mengambil inspirasi dari:
- kabut pagi
- hutan pegunungan
- kayu
- batu
- api unggun
- cahaya hangat
- udara dingin
- lanskap dataran tinggi

Elemen alam digunakan sebagai **atmosphere**, bukan dekorasi berlebihan.

### Rule

Gunakan:
- photography besar
- earthy colors
- warm surfaces
- organic visual rhythm
- whitespace

Hindari:
- gradient neon
- glassmorphism berlebihan
- card grid yang terlalu SaaS
- warna biru corporate
- ilustrasi generik hotel

## Principle 02 — Booking harus terasa effortless

Primary objective bukan sekadar membuat website terlihat premium.

Website harus membuat user berpikir:

> "Saya tahu tempat ini seperti apa, tahu kapan tersedia, tahu berapa harganya, dan bisa booking sekarang."

Setiap halaman harus memiliki **satu primary action yang jelas**.

Public website:
> **Check Availability / Book Now**

Booking flow:
> **Continue**

Payment:
> **Pay / Complete Booking**

Admin:
> **View / Manage**

Tidak boleh ada banyak CTA yang memiliki visual weight sama.

## Principle 03 — Premium melalui restraint

"Luxury" tidak diwujudkan dengan banyak ornamen.

Premium dicapai melalui:
- typography yang kuat
- spacing luas
- fotografi berkualitas
- alignment presisi
- animasi subtle
- warna terbatas
- micro-interaction yang purposeful

> Jika sebuah dekorasi tidak membantu storytelling, hierarchy, usability, atau conversion, hapus.

---

# 2. Visual Direction

## Overall Mood

**Warm alpine retreat.**

Mood:
- intimate
- quiet
- refined
- natural
- warm
- secluded
- cinematic
- trustworthy

Bayangan visual:

> Pagi berkabut di cabin kayu, udara dingin, lampu interior hangat, kopi panas, dan landscape Dieng di luar jendela.

Bukan luxury resort dengan marmer, chandelier, dan visual hotel chain.

## Photography Direction

Prioritas:
1. Exterior cabin dalam landscape Dieng
2. Interior dengan warm lighting
3. Bedroom
4. View dari cabin
5. Detail material: kayu, linen, coffee, fireplace
6. Landscape Dieng
7. Human presence secukupnya

Photography harus:
- cinematic
- natural
- slightly desaturated
- warm
- high dynamic range
- authentic

Hindari:
- stock photo yang terlalu staged
- model yang terlihat seperti iklan hotel
- foto terlalu saturated
- artificial HDR berlebihan
- terlalu banyak foto yang tidak memberikan informasi

## Visual References

### Boutique hospitality
Ambil:
- editorial typography
- large photography
- whitespace
- minimal navigation

### Scandinavian cabin
Ambil:
- natural materials
- muted colors
- restrained UI
- functional simplicity

### Premium travel editorial
Ambil:
- asymmetric composition
- large imagery
- strong visual storytelling
- typography-led hierarchy

### Modern booking products
Ambil:
- date picker yang jelas
- price transparency
- progressive disclosure
- predictable checkout

## Yang Harus Dihindari

### Generic SaaS
Jangan:
- dashboard-style cards untuk homepage
- rounded-everything
- purple/blue gradients
- excessive shadows
- excessive badges

### Generic hotel website
Jangan:
- navbar terlalu kompleks
- carousel hero otomatis
- "Book Now" berulang di setiap 100px
- badge "BEST PRICE"
- visual terlalu glossy

### Over-designed luxury
Jangan:
- gold berlebihan
- serif decorative font
- dark UI di seluruh website
- animation berat
- parallax berlebihan

---

# 3. Design Tokens

## 3.1 Color Palette

Palet menggunakan kombinasi **forest green + warm cream + charcoal + terracotta**.

Alasan:
- Forest green menghubungkan produk dengan landscape Dieng.
- Cream memberi rasa hospitality dan warmth.
- Charcoal menjaga readability.
- Terracotta memberikan accent natural tanpa menjadi "luxury gold".

### Primary

| Token | Hex | Usage |
|---|---|---|
| `primary-900` | `#17261F` | Hero overlay, dark section |
| `primary-800` | `#20372D` | Primary dark |
| `primary-700` | `#29483A` | Primary |
| `primary-600` | `#355B49` | Interactive |
| `primary-500` | `#4B705C` | Secondary accent |
| `primary-100` | `#E3EBE5` | Soft background |
| `primary-50` | `#F3F7F4` | Subtle surface |

### Neutral

| Token | Hex | Usage |
|---|---|---|
| `neutral-950` | `#191A17` | Primary text |
| `neutral-900` | `#242521` | Heading |
| `neutral-700` | `#575852` | Secondary text |
| `neutral-500` | `#8A8A81` | Muted text |
| `neutral-300` | `#D8D7CF` | Border |
| `neutral-200` | `#E8E6DE` | Divider |
| `neutral-100` | `#F3F1EA` | Surface |
| `neutral-50` | `#F8F7F2` | Page background |
| `white` | `#FFFFFF` | Cards / primary surface |

### Accent

| Token | Hex | Usage |
|---|---|---|
| `accent-700` | `#A45C38` | CTA accent / selected state |
| `accent-600` | `#B86B44` | Hover |
| `accent-100` | `#F3E3D9` | Accent background |

Terracotta dipilih karena lebih relevan dengan material alam daripada gold.

### Semantic Colors

| Purpose | Color |
|---|---|
| Success | `#3F6B4A` |
| Success background | `#E8F0E9` |
| Warning | `#A66A24` |
| Warning background | `#F8EEDC` |
| Error | `#A33D35` |
| Error background | `#F7E6E4` |
| Info | `#42677A` |
| Info background | `#E7EFF3` |

---

## 3.2 Typography

### Primary: DM Sans

DM Sans digunakan untuk seluruh UI karena:
- highly readable pada mobile
- modern tanpa terasa seperti SaaS
- geometric tetapi tidak terlalu futuristik
- cocok dengan contemporary hospitality
- memiliki weight range yang baik
- numerals jelas untuk pricing dan booking dates

### Display / editorial: Cormorant Garamond

Digunakan secara terbatas:
- hero headline
- major editorial heading
- section statement

Alasan: memberi karakter editorial agar terasa seperti boutique retreat, bukan booking SaaS.

**Tidak digunakan untuk:**
- form
- button
- navigation
- table
- price
- body copy

### Type Scale

| Token | Size | Line Height | Usage |
|---|---:|---:|---|
| `display-xl` | 64px | 1.0 | Hero desktop |
| `display-lg` | 52px | 1.05 | Major hero |
| `display-md` | 42px | 1.1 | Section heading |
| `heading-xl` | 32px | 1.15 | Page heading |
| `heading-lg` | 28px | 1.2 | Section heading |
| `heading-md` | 24px | 1.25 | Card heading |
| `heading-sm` | 20px | 1.3 | Component heading |
| `body-lg` | 18px | 1.6 | Intro |
| `body-md` | 16px | 1.55 | Default body |
| `body-sm` | 14px | 1.5 | Secondary |
| `caption` | 12px | 1.4 | Metadata |
| `button` | 14px | 1 | CTA |

Mobile display sizes turun sekitar 15–25%.

---

## 3.3 Spacing

Menggunakan base 4px:

```text
space-1   = 4px
space-2   = 8px
space-3   = 12px
space-4   = 16px
space-5   = 20px
space-6   = 24px
space-8   = 32px
space-10  = 40px
space-12  = 48px
space-16  = 64px
space-20  = 80px
space-24  = 96px
space-32  = 128px
```

Section spacing:
- Desktop: 96–128px
- Tablet: 72–96px
- Mobile: 56–72px

---

## 3.4 Radius

```text
radius-sm   = 6px
radius-md   = 10px
radius-lg   = 14px
radius-xl   = 20px
radius-full = 9999px
```

Usage:
- input: 10px
- card: 14px
- image: 14–20px
- button: 10px
- badge: full
- modal: 20px

---

## 3.5 Shadow

```text
shadow-sm:
0 1px 2px rgba(25, 26, 23, 0.05)

shadow-md:
0 8px 24px rgba(25, 26, 23, 0.08)

shadow-lg:
0 20px 50px rgba(25, 26, 23, 0.12)
```

Tidak menggunakan heavy drop shadows.

---

# 4. Screen Inventory

## Public

| Screen | Purpose |
|---|---|
| Home | Brand introduction + conversion |
| Cabin Detail | Explain accommodation |
| Gallery | Visual exploration |
| Availability / Booking | Select dates + guests |
| Checkout | Collect guest information |
| Payment | Sandbox payment |
| Booking Success | Confirmation |
| Booking Failed | Recovery |
| FAQ | Resolve objections |
| Location | Explain location/access |
| House Rules | Set expectations |
| Contact | Direct communication |
| 404 | Recovery |

## Customer

| Screen | Purpose |
|---|---|
| Login | Authenticate |
| Account Overview | Customer dashboard |
| Booking List | Reservation history |
| Booking Detail | Reservation information |
| Profile | Manage customer information |

## Admin

| Screen | Purpose |
|---|---|
| Admin Login | Secure entry |
| Dashboard | Operational overview |
| Reservation List | Manage bookings |
| Reservation Detail | Inspect booking |
| Availability Calendar | Manage occupancy |
| Availability Block | Block inventory |
| Customer List | Customer lookup |
| Customer Detail | Customer information |

---

# 5. User Flow

## Journey A — Discover → Book

```text
Homepage
   ↓
Cabin Detail
   ↓
Select Dates
   ↓
Select Guests
   ↓
Availability Result
   ↓
Booking Summary
   ↓
Guest Information
   ↓
Review
   ↓
Sandbox Payment
   ↓
Payment Processing
   ↓
Booking Success
```

### Step 1 — Homepage
User memahami:
- apa property ini
- di mana lokasinya
- seperti apa cabin-nya
- kenapa menarik

Primary action: **Check Availability**

### Step 2 — Cabin Detail
User melihat:
- photos
- capacity
- amenities
- price
- rules

Primary action: **Check Availability**

### Step 3 — Date Selection
User memilih check-in dan check-out. System memberi visual availability.

### Step 4 — Guest Selection
User memilih adults dan children. System melakukan capacity validation.

### Step 5 — Availability
System menampilkan available atau not available.

Jika available: **Continue Booking**

### Step 6 — Guest Information
User mengisi name, email, phone, country, special request. Tidak perlu login.

### Step 7 — Review
User memverifikasi dates, guests, nightly price, nights, total.

Primary action: **Continue to Payment**

### Step 8 — Payment
User memilih sandbox scenario. System menampilkan processing state.

### Step 9 — Success
Tampilkan success confirmation, booking reference, dates, guest, total, payment status.

Primary action: **View My Booking**  
Secondary: **Back to Home**

---

## Journey B — Existing Customer

```text
Homepage
 ↓
Login
 ↓
Account
 ↓
Booking History
 ↓
Booking Detail
```

---

## Journey C — Admin Manage Booking

```text
Admin Login
 ↓
Dashboard
 ↓
Reservation List
 ↓
Reservation Detail
 ↓
Review / Update Status
```

---

## Journey D — Admin Block Availability

```text
Dashboard
 ↓
Availability
 ↓
Select Dates
 ↓
Block Date
 ↓
Enter Reason
 ↓
Confirm
 ↓
Calendar Updated
```

---

## Journey E — Payment Failure Recovery

```text
Checkout
 ↓
Payment
 ↓
Payment Failed
 ↓
Explain Failure
 ↓
Retry Payment
 ↓
Processing
 ↓
Success
```

Jangan melempar user kembali ke homepage.

---

# 6. Layout per Screen

## 6.1 Homepage

```text
[Transparent Navbar]

[Hero]
  Full-bleed cabin photography
  Eyebrow
  Large headline
  Supporting copy
  Check Availability CTA

[Property Introduction]
  Editorial image
  Description

[Cabin]
  Large image
  Cabin summary
  Capacity
  Starting price
  CTA

[Experience]
  3–4 feature blocks

[Gallery]
  Masonry/editorial layout

[Location]
  Map/location visual
  Dieng description

[House Rules]
  Short list

[Final CTA]
  "Your cabin in the highlands is waiting."

[Footer]
```

Primary action: **Check Availability**

Components:
- Navbar
- Hero
- Button
- ImageGallery
- FeatureList
- CabinCard
- LocationPreview
- CTASection
- Footer

---

## 6.2 Cabin Detail

```text
Breadcrumb

Gallery
  Large primary image
  Supporting images

Cabin Header
  Cabin name
  Location
  Capacity
  Price

Description

Amenities

Stay Information

House Rules

Booking Widget
```

Desktop: booking widget sticky di sisi kanan.  
Mobile: booking widget menjadi bottom sticky CTA.

---

## 6.3 Availability / Booking

Desktop:

```text
┌───────────────────────────────┬───────────────┐
│ Calendar                      │ Booking       │
│ Date selection                │ Summary       │
│                               │ Guest selector │
│                               │ Price         │
│                               │ [Continue]    │
└───────────────────────────────┴───────────────┘
```

Mobile:

```text
Cabin summary
↓
Date picker
↓
Guest selector
↓
Price summary
↓
Continue
```

Primary action: **Continue Booking**

---

## 6.4 Checkout

Desktop:

```text
┌─────────────────────────┬───────────────────┐
│ Guest Information       │ Booking Summary   │
│                         │                   │
│ Name                    │ Dates             │
│ Email                   │ Guests            │
│ Phone                   │ Nights            │
│ Country                 │ Subtotal          │
│ Special Request         │ Total             │
└─────────────────────────┴───────────────────┘
```

Mobile menggunakan single-column focused layout.

Jangan menampilkan navigation penuh.

---

## 6.5 Payment

```text
Page Header

Booking Summary

Payment Method

Sandbox Controls

Payment Details

Security / Simulation Notice

[Complete Payment]
```

Sandbox indicator:

> **Demo payment — no real money will be charged.**

---

## 6.6 Booking Success

```text
✓

Booking Confirmed

CAB-20260921-A8F3

Thank you, Raka.

[Booking Summary]

Check-in
10 Dec 2026

Check-out
12 Dec 2026

Total
Rp2.000.000

[View My Booking]
[Back to Home]
```

Tidak perlu confetti berlebihan.

---

## 6.7 Login

Centered layout:

```text
Logo

Welcome back

Email
Password

[Sign In]

Forgot Password

Don't have an account?
```

Guest booking tetap tidak boleh terhalang oleh login.

---

## 6.8 Customer Dashboard

```text
Sidebar / mobile nav

Welcome back

Upcoming Stay
[Booking Card]

Recent Bookings

Quick Actions
```

Upcoming reservation mendapat visual priority terbesar.

---

## 6.9 Admin Dashboard

Admin harus terasa berbeda dari public site.

Public site:
> Editorial hospitality

Admin:
> Operational clarity

```text
Sidebar
   ↓
Topbar

Page title
Date/context

KPI row

Reservation overview
Availability snapshot

Upcoming check-ins
Upcoming check-outs
```

Admin tidak perlu menggunakan typography editorial secara berlebihan.

---

## 6.10 Reservation List

```text
Page header
  Reservations
  [Search]

Filter row

Status
Date
Payment

Table

Booking Ref
Guest
Dates
Guests
Amount
Payment
Status
Actions
```

Mobile berubah menjadi card list.

---

## 6.11 Availability Calendar

Desktop:

```text
Header
Month navigation

Calendar grid

Legend:
Available
Booked
Blocked
```

Click date/range → **Block Availability**

Mobile menggunakan simplified calendar dengan bottom sheet untuk detail.

---

# 7. Component Library

## Navigation
Variants:
- transparent
- solid
- admin

States:
- default
- scrolled
- mobile-open

## Button
Variants:
- primary
- secondary
- outline
- ghost
- destructive

Sizes:
- sm
- md
- lg

States:
- default
- hover
- focus
- active
- disabled
- loading

Primary button menggunakan forest green.

## Input
Variants:
- text
- email
- phone
- password
- textarea

States:
- default
- focus
- filled
- error
- disabled
- read-only

Error memiliki border, helper message, dan accessible error association.

## Date Picker
States:
- available
- selected
- range-start
- range-middle
- range-end
- unavailable
- blocked
- today
- disabled

Visual priority:
`selected > available > unavailable`

## Guest Selector

```text
Adults
−  2  +

Children
−  1  +
```

States:
- default
- min
- max
- invalid

## Price Summary
Variants:
- compact
- checkout
- confirmation

Total harus paling prominent.

## Cabin Card
Variants:
- featured
- compact
- booking

## Status Badge
Variants:
- success
- pending
- warning
- error
- neutral

## Alert
Variants:
- info
- success
- warning
- error

Harus memiliki icon + text, bukan hanya warna.

## Toast
Untuk:
- saved
- copied
- updated
- error notification

Jangan digunakan untuk critical booking confirmation.

## Modal
Variants:
- confirmation
- information
- destructive action

## Bottom Sheet
Mobile preferred untuk:
- filters
- guest selection
- availability detail
- admin actions

## Skeleton
Variants:
- text
- image
- card
- table
- calendar

## Empty State
Structure:
- Illustration/Icon
- Heading
- Explanation
- Primary action

Tidak menggunakan ilustrasi lucu/generic.

## Error State
Structure:
- Icon
- Something went wrong
- Short explanation
- Try Again

Untuk booking, tambahkan recovery action yang relevan.

---

# 8. State Design

## Homepage

**Loading:** image/content skeleton jika API-driven.  
**Error:** "We couldn't load this page." + Try Again.  
**Offline:** "You're offline. Some booking information may be unavailable."

Static marketing content sebisa mungkin tidak bergantung pada runtime API.

## Cabin Detail

**Loading:** image + text skeleton.  
**Error:** "We couldn't load the cabin information." + Try Again.  
**Empty:** tidak boleh terjadi pada published cabin.

## Availability

**Loading:** calendar skeleton; booking CTA disabled.

**Empty:**
> No available dates in this period.

CTA: **Try Another Month**

**Error:**
> We couldn't check availability.

CTA: **Try Again**

**Success:**
- Available → "Cabin available for your dates."
- Unavailable → "This cabin is unavailable for these dates."

**Offline:** disable final booking action. Jangan menganggap cabin available berdasarkan cached data saat user akan membuat reservation.

## Checkout

**Loading:** submit button → "Processing..."  
**Validation error:** inline error dekat field.  
**Server error:**
> We couldn't create your booking. Your payment has not been charged.

**Success:** redirect ke payment.

## Payment

**Idle:** payment controls aktif.

**Processing:**
> Processing payment...
>
> Please don't close this window.

Semua payment actions disabled.

**Success:** redirect success.

**Failure:**
> Payment wasn't completed.
>
> Your reservation has not been confirmed.

CTA: **Try Payment Again**

**Timeout:**
> We're checking your payment status.
>
> Please wait while we verify the transaction.

Jangan langsung menampilkan failed.

## Booking Success

**Success:** high-confidence confirmation.

**Loading:** hanya saat fetching booking details.

**Error:**
> Your booking may have been completed, but we couldn't load the details.

Tampilkan booking reference jika sudah diketahui.

## Customer Dashboard

**Loading:** skeleton.  
**Empty:**
> No bookings yet.
>
> Your next stay could start here.

CTA: **Explore Cabin**

**Error:** retry.

## Admin Dashboard

**Loading:** KPI + table skeleton.

**Empty:**
> No reservations yet.

**Error:** per-widget error jika memungkinkan. Jangan membuat satu API failure menghilangkan seluruh dashboard.

---

# 9. Responsive Behaviour

## Breakpoints

```text
Mobile:  < 640px
Tablet:  640–1023px
Desktop: ≥ 1024px
Large:   ≥ 1280px
```

Jangan mendesain mobile sebagai desktop yang diperkecil.

## Mobile

### Navigation

```text
Logo
Menu icon
```

Menu menjadi full-height sheet.

### Hero

Desktop: 64px display  
Mobile: 40px display

Hero image tetap dominan tetapi tidak boleh membuat CTA keluar dari initial viewport.

### Booking Widget

Desktop: sticky right panel.

Mobile: bottom sticky bar:

```text
Rp1.000.000 / night

[Check Availability]
```

Setelah tanggal dipilih:

```text
2 nights · Rp2.000.000

[Continue]
```

### Gallery

Desktop: editorial grid.  
Mobile: horizontal swipe carousel.

### Forms

Semua input full-width.

Touch target minimum **44 × 44px**.

### Tables

Admin table berubah menjadi card list:

```text
Booking Card
Guest
Dates
Amount
Status
View
```

## Tablet

- two-column layout jika ruang cukup
- sidebar lebih compact
- gallery 2–3 columns
- booking panel sticky jika memungkinkan

## Desktop

Maximum content width: **1280px**

Text-heavy content: **640–760px**

Hero dapat menggunakan full viewport width.

---

# 10. Accessibility

Target minimum:

**WCAG 2.2 AA**

## Contrast

- Normal text: **4.5:1**
- Large text: **3:1**
- UI component / graphical object: **3:1**

Jangan menggunakan `neutral-500` pada white untuk body text jika rasio aktual tidak memenuhi AA.

## Focus

Semua interactive element harus memiliki visible focus state.

Recommended:

```text
outline: 2px solid #B86B44
outline-offset: 3px
```

Focus tidak boleh hanya berupa perubahan warna.

## Keyboard Navigation

Core checkout flow:

```text
Check-in
→ Check-out
→ Adults
→ Children
→ Continue
→ Name
→ Email
→ Phone
→ Country
→ Special Request
→ Continue
→ Payment
```

Modal:
- open
- focus trapped inside
- Escape closes
- focus returns to trigger

## Date Picker Accessibility

Calendar membutuhkan:
- keyboard navigation
- arrow keys
- Enter/Space untuk select
- Escape untuk close
- month navigation
- visible selected date
- disabled dates

Screen reader harus mengetahui date, available/unavailable state, dan selected state.

Contoh accessible label:

> December 10, 2026, available, selected as check-in.

## ARIA

Gunakan native `<button>` untuk interactive controls.

Navigation:

```html
<nav aria-label="Main navigation">
```

Form:
- label
- input
- helper/error
- `aria-describedby`

Async status dapat menggunakan `aria-live="polite"`.

Critical payment result dapat menggunakan live announcement yang sesuai.

Error/payment failure menggunakan `role="alert"` ketika benar-benar membutuhkan immediate announcement.

Images:
- decorative → `alt=""`
- informational → descriptive alt

Jangan menggunakan filename sebagai alt text.

---

# Motion

Motion harus subtle.

Allowed:
- fade
- opacity
- transform
- slide
- skeleton shimmer

Duration:

```text
micro: 100–150ms
standard: 180–250ms
page transition: 250–350ms
```

Hindari:
- large parallax
- continuous animation
- autoplay video dengan suara
- excessive bouncing

Respect:

```text
prefers-reduced-motion
```

---

# Design Quality Bar

Sebelum screen dianggap selesai:

## 01 — Apakah user tahu harus melakukan apa?

Primary action harus jelas dalam ≤3 detik.

## 02 — Apakah user tahu status sistem?

Loading, error, unavailable, payment processing, dan success tidak boleh ambigu.

## 03 — Apakah visual terasa seperti cabin di Dieng?

Jika screen bisa digunakan tanpa perubahan untuk SaaS, fintech, atau hotel chain generik, visual direction belum cukup kuat.

## 04 — Apakah booking information transparent?

User harus selalu dapat memahami:
- tanggal
- jumlah malam
- jumlah guest
- harga per malam
- subtotal
- total

## 05 — Apakah UI tetap usable tanpa mouse dan dengan layar kecil?

Core booking flow harus dapat diselesaikan:
- mobile
- keyboard
- screen reader
- slow network

---

# Final Design Direction

Produk ini harus berada di antara:

```text
Boutique hospitality
        +
Editorial travel
        +
Modern booking UX
```

Bukan:

```text
Luxury hotel website
        +
Generic SaaS dashboard
```

## Visual hierarchy

```text
Photography
    ↓
Story / atmosphere
    ↓
Cabin information
    ↓
Availability
    ↓
Price transparency
    ↓
Booking
```

## UX hierarchy

```text
Discover
    ↓
Trust
    ↓
Check availability
    ↓
Understand price
    ↓
Book
    ↓
Confirm
```

Dengan keputusan ini, desain memiliki identitas yang cukup kuat untuk menjadi portfolio piece, tetapi tetap disiplin sebagai produk booking yang benar-benar usable.
