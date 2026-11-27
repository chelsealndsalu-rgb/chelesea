
<?php

// Memulai session jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Mengatur zona waktu Indonesia
date_default_timezone_set("Asia/Jakarta");

// Memeriksa apakah kasir sudah login
function require_login()
{
    if (empty($_SESSION['id_kasir'])) {
        header("Location: /laundrypos/auth/login.php");
        exit;
    }
}

// Mengamankan output agar tidak menjalankan kode HTML berbahaya
function e($v)
{
    return htmlspecialchars((string) $v, ENT_QUOTES, "UTF-8");
}

// Mengubah angka menjadi format Rupiah
function rupiah($v)
{
    return "Rp" . number_format((float) $v, 0, ",", ".");
}

// Mengarahkan pengguna ke halaman lain
function go($p)
{
    header("Location: /laundrypos/" . $p);
    exit;
}

?>
