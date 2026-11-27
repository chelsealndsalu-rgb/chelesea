<?php
require_once __DIR__ . '/../config/functions.php';

require_login();

$pageTitle = $pageTitle ?? 'Fresh Laundry';
$active = $active ?? '';

$baseUrl = '/laundrypos';

$menuItems = [
    [
        'key'   => 'dashboard',
        'label' => 'Beranda',
        'icon'  => 'house',
        'url'   => $baseUrl . '/dashboard.php',
    ],
    [
        'key'   => 'pelanggan',
        'label' => 'Pelanggan',
        'icon'  => 'people',
        'url'   => $baseUrl . '/pelanggan/index.php',
    ],
    [
        'key'   => 'transaksi',
        'label' => 'Transaksi',
        'icon'  => 'plus',
        'url'   => $baseUrl . '/transaksi/index.php',
    ],
    [
        'key'   => 'riwayat',
        'label' => 'Riwayat Transaksi',
        'icon'  => 'clock-history',
        'url'   => $baseUrl . '/transaksi/riwayat.php',
    ],
    [
        'key'   => 'laporan',
        'label' => 'Laporan',
        'icon'  => 'list-ul',
        'url'   => $baseUrl . '/laporan/verifikasi.php',
    ],
];

$topbarIcon = 'grid';
foreach ($menuItems as $item) {
    if ($active === $item['key']) {
        $topbarIcon = $item['icon'];
        break;
    }
}

$cssPath = __DIR__ . '/../assets/css/style.css';
$cssVer  = is_file($cssPath) ? filemtime($cssPath) : time();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | Fresh Laundry</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Custom CSS (otomatis refresh saat file diubah) -->
    <link href="<?= e($baseUrl) ?>/assets/css/style.css?v=<?= $cssVer ?>" rel="stylesheet">
</head>
<body>

<div class="app-layout">

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <a href="<?= e($baseUrl) ?>/dashboard.php" class="brand">
            <span class="brand-mark">
                <i class="bi bi-basket2"></i>
            </span>
            <span class="brand-text">
                <strong>FRESH LAUNDRY</strong>
                <small>Laundry POS</small>
            </span>
        </a>

        <nav class="sidebar-nav">
            <?php foreach ($menuItems as $item): ?>
                <a href="<?= e($item['url']) ?>"
                   class="nav-link <?= $active === $item['key'] ? 'active' : '' ?>">
                    <i class="bi bi-<?= e($item['icon']) ?>"></i>
                    <span><?= e($item['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="sidebar-bottom">
            <a href="<?= e($baseUrl) ?>/auth/logout.php" class="nav-link logout">
                <i class="bi bi-arrow-left"></i>
                <span>Keluar</span>
            </a>
        </div>
    </aside>

    <!-- Overlay mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- MAIN WRAPPER -->
    <div class="main-wrapper">

        <!-- TOPBAR -->
        <header class="topbar">
            <div class="topbar-left">
                <button type="button" class="mobile-menu-button" id="mobileMenuBtn" aria-label="Buka menu">
                    <i class="bi bi-list"></i>
                </button>

                <span class="topbar-icon">
                    <i class="bi bi-<?= e($topbarIcon) ?>"></i>
                </span>

                <h1><?= e($pageTitle) ?></h1>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <main class="main-content">