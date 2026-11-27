
<?php

// ========================================
// 1. KONFIGURASI
// ========================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';
require_once __DIR__ . '/../classes/Transaksi.php';

require_login();

// Verifikasi akses laporan
if (
    empty($_SESSION['report_verified']) ||
    time() - $_SESSION['report_verified'] > 300
) {
    go('laporan/verifikasi.php');
}


// ========================================
// 2. FILTER TAHUN
// ========================================

$tahunSekarang = (int) date('Y');

$tahun = isset($_GET['tahun'])
    ? (int) $_GET['tahun']
    : $tahunSekarang;

// Validasi tahun
if ($tahun < 2020 || $tahun > $tahunSekarang) {
    $tahun = $tahunSekarang;
}


// ========================================
// 3. RENTANG TANGGAL LAPORAN
// ========================================

// Awal tahun yang dipilih
$start = new DateTimeImmutable(
    sprintf('%04d-01-01 00:00:00', $tahun)
);

// Awal tahun berikutnya
$end = $start->modify('+1 year');

// Format tanggal untuk query database
$awal = $start->format('Y-m-d H:i:s');
$akhir = $end->format('Y-m-d H:i:s');

// Keterangan periode
$periodeLabel = 'Tahun ' . $tahun;


// ========================================
// 4. MENGAMBIL DATA LAPORAN
// ========================================

$transaksi = new Transaksi();

$report = $transaksi->getReport($awal, $akhir);

$jumlah = $report['jumlah'];
$total = $report['total'];
$methods = $report['methods'];


// ========================================
// 5. INFORMASI HALAMAN
// ========================================

$pageTitle = 'Rekap Pendapatan';
$active = 'laporan';

include __DIR__ . '/../includes/header.php';

?>


<!-- =====================================
     JUDUL HALAMAN
====================================== -->

<div class="page-heading">

    <div>
        <h1>Rekap Pendapatan</h1>

        <p class="muted">
            Hanya transaksi berstatus lunas yang dihitung.
        </p>
    </div>

</div>


<!-- =====================================
     FILTER PERIODE
====================================== -->

<section class="panel mb-3">

    <!-- Navigasi Periode -->
    <div class="report-tabs">

        <a href="harian.php" class="report-tab">
            Hari Ini
        </a>

        <a href="bulanan.php" class="report-tab">
            Bulan Ini
        </a>

        <a href="tahunan.php" class="report-tab active">
            Tahun Ini
        </a>

    </div>


    <h2 class="report-filter-title">
        Pilih Periode
    </h2>


    <!-- Form Pilihan Tahun -->
    <form
        method="GET"
        action="tahunan.php"
        class="report-filter-form"
    >

        <div class="report-filter-field">

            <label for="tahun">
                Tahun
            </label>

            <select
                name="tahun"
                id="tahun"
                class="form-select"
                required
            >

                <?php for ($i = $tahunSekarang; $i >= 2020; $i--): ?>

                    <option
                        value="<?= $i ?>"
                        <?= $tahun === $i ? 'selected' : '' ?>
                    >
                        <?= $i ?>
                    </option>

                <?php endfor; ?>

            </select>

        </div>


        <button type="submit" class="btn btn-primary">
            Tampilkan
        </button>

    </form>


    <!-- Keterangan Periode -->
    <p class="muted mt-3 mb-0">
        Periode laporan: <?= e($periodeLabel) ?>
    </p>

</section>


<!-- =====================================
     RINGKASAN PENDAPATAN
====================================== -->

<div class="row g-3">


    <!-- Jumlah Transaksi -->
    <div class="col-md-6">

        <section class="panel">

            <div class="muted">
                Jumlah Transaksi
            </div>

            <div class="stat-value">
                <?= number_format($jumlah, 0, ',', '.') ?>
            </div>

        </section>

    </div>


    <!-- Total Pendapatan -->
    <div class="col-md-6">

        <section class="panel">

            <div class="muted">
                Total Pendapatan
            </div>

            <div class="stat-value">
                <?= rupiah($total) ?>
            </div>

        </section>

    </div>

</div>


<!-- =====================================
     RINCIAN PEMBAYARAN
====================================== -->

<section class="panel mt-3">

    <h2>Rincian Pembayaran</h2>

    <div class="table-responsive">

        <table class="table mt-3">

            <thead>

                <tr>
                    <th>Metode Pembayaran</th>
                    <th class="text-end">Jumlah Transaksi</th>
                    <th class="text-end">Total</th>
                </tr>

            </thead>


            <tbody>

                <!-- QRIS -->
                <tr>

                    <td>QRIS</td>

                    <td class="text-end">
                        <?= number_format(
                            $methods['QRIS']['jumlah'],
                            0,
                            ',',
                            '.'
                        ) ?>
                    </td>

                    <td class="text-end">
                        <?= rupiah($methods['QRIS']['total']) ?>
                    </td>

                </tr>


                <!-- Tunai -->
                <tr>

                    <td>Tunai</td>

                    <td class="text-end">
                        <?= number_format(
                            $methods['Tunai']['jumlah'],
                            0,
                            ',',
                            '.'
                        ) ?>
                    </td>

                    <td class="text-end">
                        <?= rupiah($methods['Tunai']['total']) ?>
                    </td>

                </tr>

            </tbody>


            <!-- Total Keseluruhan -->
            <tfoot>

                <tr>

                    <th>Total</th>

                    <th class="text-end">
                        <?= number_format(
                            $jumlah,
                            0,
                            ',',
                            '.'
                        ) ?>
                    </th>

                    <th class="text-end">
                        <?= rupiah($total) ?>
                    </th>

                </tr>

            </tfoot>

        </table>

    </div>

</section>


<?php include __DIR__ . '/../includes/footer.php'; ?>
