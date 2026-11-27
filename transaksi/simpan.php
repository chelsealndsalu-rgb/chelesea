
<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    go('transaksi/index.php');
}

// Mengambil data dari form
$idP = (int) ($_POST['id_pelanggan'] ?? 0);
$idL = (int) ($_POST['id_layanan'] ?? 0);
$kg = (float) ($_POST['berat_pakaian'] ?? 0);
$method = $_POST['metode_pembayaran'] ?? '';

// Validasi data
if (
    $idP < 1 ||
    $idL < 1 ||
    $kg <= 0 ||
    !in_array($method, ['Tunai', 'QRIS'], true)
) {
    die('Data transaksi tidak valid.');
}

// Mengambil informasi layanan
$s = $conn->prepare(
    'SELECT harga_per_kg, durasi_jam
     FROM layanan
     WHERE id_layanan = ?'
);

$s->bind_param('i', $idL);
$s->execute();

$service = $s->get_result()->fetch_assoc();
$s->close();

if (!$service) {
    die('Layanan tidak ditemukan.');
}

// Memastikan pelanggan tersedia
$s = $conn->prepare(
    'SELECT id_pelanggan
     FROM pelanggan
     WHERE id_pelanggan = ?'
);

$s->bind_param('i', $idP);
$s->execute();

$customer = $s->get_result()->fetch_assoc();
$s->close();

if (!$customer) {
    die('Pelanggan tidak ditemukan.');
}

// Menghitung pembayaran
$price = (float) $service['harga_per_kg'];
$total = $kg * $price;

// Menghitung estimasi selesai
$now = new DateTime();
$finish = null;

if ($service['durasi_jam'] !== null) {
    $finish = clone $now;
    $finish->modify('+' . (int) $service['durasi_jam'] . ' hours');
}

$finishSql = $finish
    ? $finish->format('Y-m-d H:i:s')
    : null;

// ======================================
// GENERATE NOMOR NOTA
// Format: FLDDMMYY-001
// ======================================

$tanggalNota = date('dmy');
$prefix = 'FL' . $tanggalNota;

$pattern = $prefix . '-%';

$s = $conn->prepare(
    "SELECT MAX(
        CAST(SUBSTRING_INDEX(nomor_nota, '-', -1) AS UNSIGNED)
     ) AS urutan
     FROM transaksi
     WHERE nomor_nota LIKE ?"
);

$s->bind_param('s', $pattern);
$s->execute();

$result = $s->get_result()->fetch_assoc();
$s->close();

// Menentukan nomor urut berikutnya
$urutan = (int) ($result['urutan'] ?? 0) + 1;

// Membentuk nomor nota
$nota = $prefix . '-' . str_pad($urutan, 3, '0', STR_PAD_LEFT);

// ======================================
// MENYIMPAN TRANSAKSI
// ======================================

$cashier = (int) $_SESSION['id_kasir'];
$status = 'Lunas';

$s = $conn->prepare(
    'INSERT INTO transaksi (
        nomor_nota,
        id_kasir,
        id_pelanggan,
        id_layanan,
        berat_pakaian,
        harga_per_kg,
        total_pembayaran,
        metode_pembayaran,
        status_pembayaran,
        tanggal_transaksi,
        estimasi_selesai
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)'
);

$s->bind_param(
    'siiidddsss',
    $nota,
    $cashier,
    $idP,
    $idL,
    $kg,
    $price,
    $total,
    $method,
    $status,
    $finishSql
);

if (!$s->execute()) {
    die('Gagal menyimpan transaksi: ' . e($s->error));
}

$idTransaksi = $conn->insert_id;
$s->close();

// Menuju halaman cetak nota
go('nota/cetak.php?id=' . $idTransaksi);
