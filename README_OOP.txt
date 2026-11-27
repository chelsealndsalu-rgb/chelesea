# LaundryPOS - Revisi OOP

## Teknologi
- PHP Native
- MySQL
- XAMPP (Apache + MySQL)
- Bootstrap 5

## Struktur OOP
Folder `classes/` berisi:
- `Database.php`: koneksi database (singleton sederhana).
- `Kasir.php`: autentikasi kasir.
- `Pelanggan.php`: pengambilan ringkasan dan daftar pelanggan.
- `Layanan.php`: pengambilan data layanan.
- `Transaksi.php`: ringkasan dashboard, transaksi terbaru, dan rekap periode.

Halaman PHP bertindak sebagai penghubung antara class dan tampilan HTML. CSS dan JavaScript tetap berada di folder `assets`.

## Database
Gunakan database `laundrypos` dan empat tabel yang sudah dibuat:
`kasir`, `pelanggan`, `layanan`, `transaksi`.

Konfigurasi koneksi berada di `classes/Database.php`. Default XAMPP:
- host: localhost
- username: root
- password: (kosong)
- database: laundrypos

## Menjalankan
1. Ekstrak folder `laundrypos` ke `C:\xampp\htdocs\`.
2. Jalankan Apache dan MySQL dari XAMPP.
3. Buka `http://localhost/laundrypos/`.
4. Login menggunakan akun kasir yang ada di database.

## Rekap pendapatan
Buka menu Rekap Pendapatan, kemudian pilih:
- Hari Ini: memilih tanggal.
- Bulan Ini: memilih bulan dan tahun.
- Tahun Ini: memilih tahun.

Laporan hanya menghitung transaksi dengan `status_pembayaran = 'Lunas'`.
Rincian pembayaran menampilkan jumlah transaksi dan total nominal untuk QRIS dan Tunai.

## Catatan keamanan
Untuk mengikuti database demo tugas yang saat ini digunakan, autentikasi masih membandingkan password teks biasa. Ini tidak aman untuk aplikasi produksi. Untuk penggunaan nyata, ubah penyimpanan password menjadi `password_hash()` dan verifikasi dengan `password_verify()`.

## Pengujian yang perlu dilakukan
Setelah diekstrak dan dijalankan, uji:
1. Login dengan akun kasir.
2. Dashboard menampilkan transaksi dan pendapatan sesuai tanggal hari ini.
3. Rekap harian, bulanan, dan tahunan.
4. Jumlah transaksi QRIS dan Tunai.
5. Cetak nota dan riwayat transaksi.
