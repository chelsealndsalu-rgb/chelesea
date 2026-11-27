<?php
require_once __DIR__ . '/../config/functions.php';
require_once __DIR__ . '/../classes/Kasir.php';
require_login();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $kasirClass = new Kasir();
    if ($kasirClass->verify($username, $password)) {
        $_SESSION['report_verified'] = time();
        go('laporan/harian.php');
    }

    $error = 'Username atau password tidak valid.';
}

$pageTitle = 'Verifikasi Rekap Pendapatan';
$active = 'laporan';
include __DIR__ . '/../includes/header.php';
?>
<div class="page-heading">
    <div>
        <h1>Verifikasi Rekap Pendapatan</h1>
        <p class="muted">Masukkan ulang username dan password.</p>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-md-6">
        <section class="panel">
            <h2 class="mb-3">Autentikasi Ulang</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= e($error) ?></div>
            <?php endif; ?>
            <form method="post">
                <label class="form-label">Username</label>
                <input class="form-control mb-3" type="text" name="username" required>
                <label class="form-label">Password</label>
                <input class="form-control mb-3" type="password" name="password" required>
                <button type="submit" class="btn btn-primary w-100">Verifikasi</button>
            </form>
        </section>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
