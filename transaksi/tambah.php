
<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/functions.php';

require_login();

$errors = [];
$nama = '';
$telepon = '';
$alamat = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama = trim($_POST['nama'] ?? '');
    $telepon = trim($_POST['nomor_telepon'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');

    // Validasi input
    if ($nama === '') {
        $errors[] = 'Nama pelanggan wajib diisi.';
    }

    if ($telepon === '') {
        $errors[] = 'Nomor telepon wajib diisi.';
    }

    if ($alamat === '') {
        $errors[] = 'Alamat pelanggan wajib diisi.';
    }

    // Periksa nomor telepon yang sudah terdaftar
    if (empty($errors)) {

        $check = $conn->prepare(
            "SELECT id_pelanggan
             FROM pelanggan
             WHERE nomor_telepon = ?"
        );

        $check->bind_param('s', $telepon);
        $check->execute();

        $existing = $check->get_result()->fetch_assoc();
        $check->close();

        if ($existing) {
            $errors[] = 'Nomor telepon sudah terdaftar.';
        }
    }

    // Simpan pelanggan
    if (empty($errors)) {

        $stmt = $conn->prepare(
            "INSERT INTO pelanggan
             (nama, nomor_telepon, alamat)
             VALUES (?, ?, ?)"
        );

        $stmt->bind_param(
            'sss',
            $nama,
            $telepon,
            $alamat
        );

        if ($stmt->execute()) {

            $stmt->close();

            header(
                'Location: index.php?pelanggan_baru=berhasil'
            );

            exit;

        } else {

            $errors[] = 'Gagal menyimpan pelanggan: ' . $stmt->error;
            $stmt->close();

        }
    }
}

$pageTitle = 'Tambah Pelanggan Baru';
$active = 'transaksi';

require_once __DIR__ . '/../includes/header.php';
?>

<div class="transaction-page">

    <div class="mb-4">
        <h2 class="fw-bold mb-2">Tambah Pelanggan Baru</h2>
        <p class="text-muted mb-0">
            Masukkan informasi pelanggan untuk melanjutkan transaksi.
        </p>
    </div>

    <div class="card border-0 shadow-sm"
         style="border-radius: 18px;">

        <div class="card-body p-4">

            <h5 class="fw-bold mb-4">
                Informasi Pelanggan
            </h5>

            <?php if (!empty($errors)): ?>

                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li>
                                <?= htmlspecialchars($error) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

            <?php endif; ?>

            <form method="POST">

                <div class="mb-4">

                    <label class="form-label fw-semibold">
                        Nama Pelanggan
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        name="nama"
                        class="form-control"
                        placeholder="Masukkan nama pelanggan"
                        value="<?= htmlspecialchars($nama) ?>"
                        required
                    >

                </div>

                <div class="mb-4">

                    <label class="form-label fw-semibold">
                        Nomor Telepon
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="tel"
                        name="nomor_telepon"
                        class="form-control"
                        placeholder="Contoh: 081234567890"
                        value="<?= htmlspecialchars($telepon) ?>"
                        required
                    >

                </div>

                <div class="mb-4">

                    <label class="form-label fw-semibold">
                        Alamat
                        <span class="text-danger">*</span>
                    </label>

                    <textarea
                        name="alamat"
                        class="form-control"
                        rows="3"
                        placeholder="Masukkan alamat pelanggan"
                        required
                    ><?= htmlspecialchars($alamat) ?></textarea>

                </div>

                <div class="d-flex justify-content-end gap-2">

                    <a
                        href="index.php"
                        class="btn btn-light px-4"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary px-4"
                    >
                        Simpan Pelanggan
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
