
<?php

require_once __DIR__ . '/../config/functions.php';

require_login();

// Halaman Daftar Layanan sudah tidak digunakan
header('Location: /laundrypos/transaksi/index.php');
exit;

?>
