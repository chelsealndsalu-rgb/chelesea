
<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';
require_once __DIR__ . '/../classes/Transaksi.php';

require_login();

if (
    empty($_SESSION['report_verified']) ||
    time() - $_SESSION['report_verified'] > 300
) {
    go('laporan/verifikasi.php');
}

// Filter tanggal
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');

$date = DateTime::createFromFormat('!Y-m-d', $tanggal);

if (!$date || $date->format('Y-m-d') !== $tanggal) {
    $tanggal = date('Y-m-d');
    $date = new DateTime($tanggal);
}

$awal = $date->format('Y-m-d') . ' 00:00:00';
$akhir = (clone $date)->modify('+1 day')->format('Y-m-d') . ' 00:00:00';

// Ambil data laporan
$transaksi = new Transaksi();
$report = $transaksi->getReport($awal, $akhir);

$jumlah = $report['jumlah'];
$total = $report['total'];
$methods = $report['methods'];

$namaTanggal = $date->format('d/m/Y');

$pageTitle = 'Rekap Pendapatan';
$active = 'laporan';

include __DIR__ . '/../includes/header.php';
?>

<div class="page-heading">
    <div>
        <h1>Rekap Pendapatan</h1>
        <p class="muted">Hanya transaksi berstatus lunas yang dihitung.</p>
    </div>
</div>

<section class="panel mb-3">

    <div class="report-tabs">
        <a href="harian.php" class="report-tab active">Hari Ini</a>
        <a href="bulanan.php" class="report-tab">Bulan Ini</a>
        <a href="tahunan.php" class="report-tab">Tahun Ini</a>
    </div>

    <h2 class="report-filter-title">Pilih Periode</h2>

    <form method="GET" class="report-filter-form">

        <div class="report-filter-field">
            <label for="tanggal">Tanggal</label>

            <input
                type="date"
                name="tanggal"
                id="tanggal"
                class="form-control"
                value="<?= e($tanggal) ?>"
                required
            >
        </div>

        <button type="submit" class="btn btn-primary">
            Tampilkan
        </button>

    </form>

    <p class="muted mt-3 mb-0">
        Periode laporan: Tanggal <?= e($namaTanggal) ?>
    </p>

</section>

<div class="row g-3">

    <div class="col-md-6">
        <section class="panel">
            <div class="muted">Jumlah Transaksi</div>
            <div class="stat-value">
                <?= number_format($jumlah, 0, ',', '.') ?>
            </div>
        </section>
    </div>

    <div class="col-md-6">
        <section class="panel">
            <div class="muted">Total Pendapatan</div>
            <div class="stat-value">
                <?= rupiah($total) ?>
            </div>
        </section>
    </div>

</div>

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
                <tr>
                    <td>QRIS</td>
                    <td class="text-end">
                        <?= $methods['QRIS']['jumlah'] ?>
                    </td>
                    <td class="text-end">
                        <?= rupiah($methods['QRIS']['total']) ?>
                    </td>
                </tr>

                <tr>
                    <td>Tunai</td>
                    <td class="text-end">
                        <?= $methods['Tunai']['jumlah'] ?>
                    </td>
                    <td class="text-end">
                        <?= rupiah($methods['Tunai']['total']) ?>
                    </td>
                </tr>
            </tbody>

            <tfoot>
                <tr>
                    <th>Total</th>
                    <th class="text-end"><?= $jumlah ?></th>
                    <th class="text-end"><?= rupiah($total) ?></th>
                </tr>
            </tfoot>

        </table>
    </div>

</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
