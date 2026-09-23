/**
 * Static ID/EN dictionaries. English is the key source of truth: `id` must
 * satisfy `Record<TranslationKey, string>` so TypeScript fails the build on
 * drift. Server-originated strings (ApiError messages, validation bags)
 * are intentionally NOT here — they render verbatim from the backend.
 */

const en = {
  'nav.brand': 'Cabin Villa Dieng',
  'nav.main': 'Main navigation',
  'nav.account': 'Account navigation',
  'nav.admin': 'Admin navigation',
  'nav.language': 'Language',
  'nav.language.id': 'Indonesian',
  'nav.language.en': 'English',

  'auth.signIn': 'Sign in',
  'auth.signingIn': 'Signing in…',
  'auth.createAccount': 'Create account',
  'auth.creatingAccount': 'Creating account…',
  'auth.signOut': 'Sign out',
  'auth.email': 'Email',
  'auth.password': 'Password',
  'auth.fullName': 'Full name',
  'auth.confirmPassword': 'Confirm password',
  'auth.noAccount': 'No account yet?',
  'auth.createOne': 'Create one',
  'auth.haveAccount': 'Already have an account?',

  'common.loading': 'Loading…',
  'common.retry': 'Try again',
  'common.backHome': 'Back to home',
  'common.goToAccount': 'Go to account',
  'common.authErrorFallback':
    'We could not reach the server. Your session was kept.',

  'footer.brand': 'Cabin Villa Dieng',

  'notFound.title': 'Page not found',
  'notFound.body': 'The page you are looking for does not exist or has moved.',

  'forbidden.title': 'Access forbidden',
  'forbidden.body': 'You do not have permission to view this page.',

  'placeholder.body':
    'This page is part of the application. Its feature is not implemented yet.',

  'page.cabinDetail': 'Cabin Detail',
  'page.booking': 'Booking',
  'page.checkout': 'Checkout',
  'page.payment': 'Payment',
  'page.bookingSuccess': 'Booking Success',
  'page.bookingFailed': 'Booking Failed',
  'page.faq': 'FAQ',
  'page.location': 'Location',
  'page.houseRules': 'House Rules',
  'page.contact': 'Contact',
  'page.account': 'Account',
  'page.myBookings': 'My Bookings',
  'page.bookingDetail': 'Booking Detail',
  'page.profile': 'Profile',
  'page.adminDashboard': 'Admin Dashboard',
  'page.reservations': 'Reservations',
  'page.reservationDetail': 'Reservation Detail',
  'page.availability': 'Availability',
  'page.customers': 'Customers',
  'page.customerDetail': 'Customer Detail',

  'home.eyebrow': 'Exclusive cabin retreat · Dieng highlands',
  'home.checkAvailability': 'Check availability',
  'home.introTitle': 'The property',
  'home.cabinsTitle': 'Cabins',
  'home.highlightsTitle': 'Highlights',
  'home.finalCtaTitle': 'Your cabin in the highlands is waiting.',
  'home.perNight': '/night',
  'home.capacity': 'Adults {adults} · Children {children}',
  'home.imageFallback': 'Photo of {name}',
  'home.emptyTitle': 'No property is listed yet',
  'home.emptyBody':
    'There is currently no active property to show. Please check back later.',
  'home.errorTitle': 'We could not load the homepage',
  'home.errorBody':
    'Something went wrong while loading. Please try again.',
  'home.cabinsErrorTitle': 'We could not load the cabins',
  'home.cabinsErrorBody':
    'Property information is shown below. Please try loading the cabins again.',
  'home.cabinsEmpty': 'No cabins are listed for this property yet.',
} as const;

export type TranslationKey = keyof typeof en;

const id: Record<TranslationKey, string> = {
  'nav.brand': 'Cabin Villa Dieng',
  'nav.main': 'Navigasi utama',
  'nav.account': 'Navigasi akun',
  'nav.admin': 'Navigasi admin',
  'nav.language': 'Bahasa',
  'nav.language.id': 'Indonesia',
  'nav.language.en': 'Inggris',

  'auth.signIn': 'Masuk',
  'auth.signingIn': 'Memproses masuk…',
  'auth.createAccount': 'Buat akun',
  'auth.creatingAccount': 'Membuat akun…',
  'auth.signOut': 'Keluar',
  'auth.email': 'Email',
  'auth.password': 'Kata sandi',
  'auth.fullName': 'Nama lengkap',
  'auth.confirmPassword': 'Konfirmasi kata sandi',
  'auth.noAccount': 'Belum punya akun?',
  'auth.createOne': 'Buat akun',
  'auth.haveAccount': 'Sudah punya akun?',

  'common.loading': 'Memuat…',
  'common.retry': 'Coba lagi',
  'common.backHome': 'Kembali ke beranda',
  'common.goToAccount': 'Ke akun',
  'common.authErrorFallback':
    'Kami tidak dapat menghubungi server. Sesi Anda tetap tersimpan.',

  'footer.brand': 'Cabin Villa Dieng',

  'notFound.title': 'Halaman tidak ditemukan',
  'notFound.body': 'Halaman yang Anda cari tidak ada atau telah dipindahkan.',

  'forbidden.title': 'Akses ditolak',
  'forbidden.body': 'Anda tidak memiliki izin untuk melihat halaman ini.',

  'placeholder.body':
    'Halaman ini adalah bagian dari aplikasi. Fiturnya belum diimplementasikan.',

  'page.cabinDetail': 'Detail Kabin',
  'page.booking': 'Pemesanan',
  'page.checkout': 'Checkout',
  'page.payment': 'Pembayaran',
  'page.bookingSuccess': 'Pemesanan Berhasil',
  'page.bookingFailed': 'Pemesanan Gagal',
  'page.faq': 'FAQ',
  'page.location': 'Lokasi',
  'page.houseRules': 'Tata Tertib',
  'page.contact': 'Kontak',
  'page.account': 'Akun',
  'page.myBookings': 'Pemesanan Saya',
  'page.bookingDetail': 'Detail Pemesanan',
  'page.profile': 'Profil',
  'page.adminDashboard': 'Dasbor Admin',
  'page.reservations': 'Reservasi',
  'page.reservationDetail': 'Detail Reservasi',
  'page.availability': 'Ketersediaan',
  'page.customers': 'Pelanggan',
  'page.customerDetail': 'Detail Pelanggan',

  'home.eyebrow': 'Retret kabin eksklusif · Dataran tinggi Dieng',
  'home.checkAvailability': 'Cek ketersediaan',
  'home.introTitle': 'Tentang properti',
  'home.cabinsTitle': 'Kabin',
  'home.highlightsTitle': 'Sorotan',
  'home.finalCtaTitle': 'Kabin Anda di dataran tinggi menanti.',
  'home.perNight': '/malam',
  'home.capacity': 'Dewasa {adults} · Anak {children}',
  'home.imageFallback': 'Foto {name}',
  'home.emptyTitle': 'Belum ada properti yang terdaftar',
  'home.emptyBody':
    'Saat ini belum ada properti aktif untuk ditampilkan. Silakan kembali lagi nanti.',
  'home.errorTitle': 'Kami tidak dapat memuat beranda',
  'home.errorBody': 'Terjadi kesalahan saat memuat. Silakan coba lagi.',
  'home.cabinsErrorTitle': 'Kami tidak dapat memuat kabin',
  'home.cabinsErrorBody':
    'Informasi properti ditampilkan di bawah. Silakan coba muat kabin lagi.',
  'home.cabinsEmpty': 'Belum ada kabin yang terdaftar untuk properti ini.',
};

export const dictionaries: Record<'id' | 'en', Record<TranslationKey, string>> = {
  en,
  id,
};
