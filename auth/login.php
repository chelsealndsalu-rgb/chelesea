
<?php
require_once __DIR__ . '/../config/functions.php';
require_once __DIR__ . '/../classes/Kasir.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $kasirClass = new Kasir();
    $kasir = $kasirClass->login($username, $password);

    if ($kasir) {
        session_regenerate_id(true);

        $_SESSION['id_kasir'] = $kasir['id_kasir'];
        $_SESSION['username'] = $kasir['username'];

        go('dashboard.php');
    }

    $error = 'Username atau password salah.';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login - Fresh Laundry</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body class="login-page">

    <main class="login-container">

        <form class="login-card" method="POST">

            <div class="login-logo">F</div>

            <h1>Fresh Laundry</h1>

            <p class="login-subtitle">
                Masuk ke akun kasir Anda
            </p>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <label for="username" class="form-label">
                    Username
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    class="form-control"
                    placeholder="Masukkan username"
                    required
                >
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control"
                    placeholder="Masukkan password"
                    required
                >
            </div>

            <button type="submit" class="btn login-button w-100">
                Masuk
            </button>

            <p class="login-footer">
                Fresh Laundry - Sistem Kasir Laundry
            </p>

        </form>

    </main>

</body>
</html>
