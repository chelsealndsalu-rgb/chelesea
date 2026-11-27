
<?php

require_once __DIR__ . '/config/functions.php';
require_once __DIR__ . '/classes/Pelanggan.php';
require_once __DIR__ . '/classes/Transaksi.php';
require_once __DIR__ . '/classes/Layanan.php';

require_login();

$pelangganClass = new Pelanggan();
$transaksiClass = new Transaksi();
$layananClass = new Layanan();

$customers = $pelangganClass->countAll();
$summary = $transaksiClass->getDashboardSummary();
$recent = $transaksiClass->getRecent(5);
$layanan = $layananClass->getAll();

$jam = (int) date('H');

if ($jam >= 5 && $jam < 11) {
    $sapaan = 'Selamat Pagi';
} elseif ($jam >= 11 && $jam < 15) {
    $sapaan = 'Selamat Siang';
} elseif ($jam >= 15 && $jam < 18) {
    $sapaan = 'Selamat Sore';
} else {
    $sapaan = 'Selamat Malam';
}

$pageTitle = 'Beranda';
$active = 'dashboard';

include __DIR__ . '/includes/header.php';
?>

<!-- BANNER -->
<section class="dashboard-hero">
    <div class="hero-content">
        <h1><?= e($sapaan) ?>, Kasir!</h1>
        <p>
            Berikut ringkasan aktivitas dan transaksi Fresh Laundry hari ini.
        </p>
    </div>

    <div class="hero-illustration">
        <i class="bi bi-washing-machine"></i>
    </div>
</section>

<!-- STATISTIK -->
<div class="row g-3 dashboard-stats">

    <div class="col-12 col-md-4">
        <a href="pelanggan/index.php" class="stat-card-link">
            <div class="stat-card stat-customer">
                <div class="stat-title">Total Pelanggan</div>

                <div class="stat-value">
                    <?= number_format((int)$customers, 0, ',', '.') ?>
                </div>

                <div class="stat-description">
                    Pelanggan terdaftar
                </div>
            </div>
        </a>
    </div>

    <div class="col-12 col-md-4">
        <a href="transaksi/riwayat.php" class="stat-card-link">
            <div class="stat-card stat-transaction">
                <div class="stat-title">Transaksi Hari Ini</div>

                <div class="stat-value">
                    <?= number_format((int)$summary['jumlah'], 0, ',', '.') ?>
                </div>

                <div class="stat-description">
                    Transaksi tercatat
                </div>
            </div>
        </a>
    </div>

    <div class="col-12 col-md-4">
        <a href="laporan/verifikasi.php" class="stat-card-link">
            <div class="stat-card stat-income">
                <div class="stat-title">Pendapatan Hari Ini</div>

                <div class="stat-value">
                    <?= e(rupiah($summary['total'])) ?>
                </div>

                <div class="stat-description">
                    Total pembayaran lunas
                </div>
            </div>
        </a>
    </div>

</div>

<!-- TOMBOL AKSI -->
<div class="dashboard-actions">

    <a href="transaksi/index.php"
       class="btn-dashboard btn-dashboard-primary">
        <i class="bi bi-plus-lg"></i>
        Transaksi Baru
    </a>

    <a href="pelanggan/index.php"
       class="btn-dashboard btn-dashboard-outline">
        <i class="bi bi-person-plus"></i>
        Tambah Pelanggan
    </a>

</div>

<!-- TRANSAKSI DAN LAYANAN -->
<div class="row g-3 dashboard-bottom">

    <!-- TRANSAKSI TERBARU -->
    <div class="col-12 col-xl-7">
        <section class="dashboard-panel transaction-panel">

            <div class="panel-heading">
                <h2>Transaksi Terbaru</h2>

                <a href="transaksi/riwayat.php">
                    Lihat Semua →
                </a>
            </div>

            <div class="table-responsive">
                <table class="table dashboard-table">

                    <thead>
                        <tr>
                            <th>NO. NOTA</th>
                            <th>PELANGGAN</th>
                            <th>LAYANAN</th>
                            <th>TOTAL</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if ($recent->num_rows === 0): ?>

                        <tr>
                            <td colspan="5" class="empty-data">
                                Belum ada transaksi yang tercatat.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php while ($row = $recent->fetch_assoc()): ?>

                            <tr>
                                <td>
                                    <strong>
                                        <?= e($row['nomor_nota']) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= e($row['nama']) ?>
                                </td>

                                <td>
                                    <?= e($row['nama_layanan']) ?>
                                </td>

                                <td>
                                    <strong>
                                        <?= e(rupiah($row['total_pembayaran'])) ?>
                                    </strong>
                                </td>

                                <td>
                                    <a
                                        href="nota/cetak.php?id=<?= (int)$row['id_transaksi'] ?>"
                                        class="btn-print"
                                        target="_blank"
                                    >
                                        Cetak
                                    </a>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    <?php endif; ?>

                    </tbody>

                </table>
            </div>

        </section>
    </div>

    <!-- LAYANAN LAUNDRY -->
    <div class="col-12 col-xl-5">
        <section class="dashboard-panel service-panel">

            <div class="panel-heading">
                <h2>Layanan Laundry</h2>
            </div>

            <div class="service-list">

                <?php while ($item = $layanan->fetch_assoc()): ?>

                    <div class="service-item">

                        <div class="service-info">
                            <h3>
                                <?= e($item['nama_layanan']) ?>
                            </h3>

                            <p>
                                <?php
                                $durasi = (int)$item['durasi_jam'];

                                if ($durasi === 0) {
                                    echo 'Cuci & kering sendiri (0 jam)';
                                } elseif ($durasi === 24) {
                                    echo 'Selesai ± 24 jam';
                                } else {
                                    echo 'Selesai ± ' . $durasi . ' jam';
                                }
                                ?>
                            </p>
                        </div>

                        <span class="service-price">
                            <?= e(rupiah($item['harga_per_kg'])) ?>/kg
                        </span>

                    </div>

                <?php endwhile; ?>

            </div>

        </section>
    </div>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
