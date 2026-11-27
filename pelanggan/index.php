
<?php
require_once __DIR__ . '/../config/functions.php';
require_once __DIR__ . '/../classes/Pelanggan.php';

require_login();

$pelangganClass = new Pelanggan();

/*
|--------------------------------------------------------------------------
| Fungsi keamanan output
|--------------------------------------------------------------------------
*/
if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars(
            (string) ($value ?? ''),
            ENT_QUOTES,
            'UTF-8'
        );
    }
}

/*
|--------------------------------------------------------------------------
| Proses tambah dan edit pelanggan
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_customer'])) {

    $id = (int) ($_POST['id_pelanggan'] ?? 0);
    $nama = trim($_POST['nama'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $telepon = trim($_POST['nomor_telepon'] ?? '');

    if ($nama === '' || $telepon === '') {
        header('Location: index.php?status=invalid');
        exit;
    }

    $saved = $pelangganClass->save(
        $id,
        $nama,
        $alamat,
        $telepon
    );

    header(
        'Location: index.php?status=' .
        ($saved ? 'saved' : 'failed')
    );
    exit;
}

/*
|--------------------------------------------------------------------------
| Proses hapus pelanggan
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_customer'])) {

    $id = (int) ($_POST['id_pelanggan'] ?? 0);

    $deleted = $pelangganClass->delete($id);

    header(
        'Location: index.php?status=' .
        ($deleted ? 'deleted' : 'delete_failed')
    );
    exit;
}

/*
|--------------------------------------------------------------------------
| Pencarian dan pagination
|--------------------------------------------------------------------------
*/
$keyword = trim($_GET['q'] ?? '');
$perPage = 10;

$currentPage = max(1, (int) ($_GET['page'] ?? 1));

$totalCustomers = (int) $pelangganClass->countAll($keyword);

$totalPages = max(
    1,
    (int) ceil($totalCustomers / $perPage)
);

$currentPage = min($currentPage, $totalPages);

$offset = ($currentPage - 1) * $perPage;

$result = $pelangganClass->getAll(
    $keyword,
    $perPage,
    $offset
);

$customers = $result
    ? $result->fetch_all(MYSQLI_ASSOC)
    : [];

/*
|--------------------------------------------------------------------------
| Susun nomor halaman
|--------------------------------------------------------------------------
*/
$pagesToShow = [];

if ($totalPages <= 7) {

    for ($i = 1; $i <= $totalPages; $i++) {
        $pagesToShow[] = $i;
    }

} else {

    $pagesToShow[] = 1;

    $start = max(2, $currentPage - 1);
    $end = min($totalPages - 1, $currentPage + 1);

    if ($currentPage <= 3) {
        $start = 2;
        $end = 4;

    } elseif ($currentPage >= $totalPages - 2) {
        $start = $totalPages - 3;
        $end = $totalPages - 1;
    }

    if ($start > 2) {
        $pagesToShow[] = '...';
    }

    for ($i = $start; $i <= $end; $i++) {
        $pagesToShow[] = $i;
    }

    if ($end < $totalPages - 1) {
        $pagesToShow[] = '...';
    }

    $pagesToShow[] = $totalPages;
}

/*
|--------------------------------------------------------------------------
| Data modal edit
|--------------------------------------------------------------------------
*/
$editData = null;

if (isset($_GET['edit'])) {
    $editData = $pelangganClass->findById(
        (int) $_GET['edit']
    );
}

$pageTitle = 'Pelanggan';
$active = 'pelanggan';

include __DIR__ . '/../includes/header.php';
?>

<!-- HEADER HALAMAN -->
<div class="page-heading">
    <div>
        <h1>Pelanggan</h1>
    </div>

    <button
        type="button"
        class="btn btn-primary"
        data-bs-toggle="modal"
        data-bs-target="#addCustomerModal"
    >
        <i class="bi bi-plus-lg"></i>
        Tambah Pelanggan
    </button>
</div>

<!-- NOTIFIKASI -->
<?php if (isset($_GET['status'])): ?>

    <?php
    $messages = [
        'saved' => [
            'success',
            'Data pelanggan berhasil disimpan.'
        ],

        'deleted' => [
            'success',
            'Data pelanggan berhasil dihapus.'
        ],

        'invalid' => [
            'warning',
            'Nama dan nomor telepon wajib diisi.'
        ],

        'failed' => [
            'danger',
            'Data pelanggan gagal disimpan.'
        ],

        'delete_failed' => [
            'danger',
            'Pelanggan tidak dapat dihapus. Pastikan pelanggan belum memiliki transaksi.'
        ],
    ];

    $status = $_GET['status'];

    if (isset($messages[$status])):
    ?>

        <div class="alert alert-<?= e($messages[$status][0]) ?> alert-dismissible fade show">

            <?= e($messages[$status][1]) ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    <?php endif; ?>

<?php endif; ?>


<!-- DAFTAR PELANGGAN -->
<div class="panel customer-list-panel">

    <div class="customer-list-heading">

        <form method="GET" class="customer-search">

            <input
                type="text"
                name="q"
                class="form-control"
                placeholder="Cari nama atau nomor telepon..."
                value="<?= e($keyword) ?>"
            >

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i>
            </button>

            <?php if ($keyword !== ''): ?>

                <a
                    href="index.php"
                    class="btn btn-outline-secondary"
                >
                    Reset
                </a>

            <?php endif; ?>

        </form>

    </div>


    <!-- TABEL -->
    <div class="table-responsive">

        <table class="table customer-table">

            <thead>
                <tr>
                    <th width="60">No.</th>
                    <th>Nama Pelanggan</th>
                    <th>Nomor Telepon</th>
                    <th>Alamat</th>
                    <th width="130" class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>

                <?php if (count($customers) > 0): ?>

                    <?php foreach ($customers as $index => $customer): ?>

                        <?php
                        $editQuery = [
                            'edit' => (int) $customer['id_pelanggan'],
                            'page' => $currentPage,
                        ];

                        if ($keyword !== '') {
                            $editQuery['q'] = $keyword;
                        }
                        ?>

                        <tr>

                            <td>
                                <?= $offset + $index + 1 ?>
                            </td>

                            <td>
                                <strong>
                                    <?= e($customer['nama']) ?>
                                </strong>
                            </td>

                            <td>
                                <?= e($customer['nomor_telepon']) ?>
                            </td>

                            <td>
                                <?= e($customer['alamat'] ?: '-') ?>
                            </td>

                            <td>

                                <div class="customer-action-buttons">

                                    <!-- EDIT -->
                                    <a
                                        href="?<?= e(http_build_query($editQuery)) ?>"
                                        class="btn btn-sm btn-customer-edit"
                                        title="Edit pelanggan"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <!-- HAPUS -->
                                    <form
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus pelanggan ini?')"
                                    >

                                        <input
                                            type="hidden"
                                            name="id_pelanggan"
                                            value="<?= (int) $customer['id_pelanggan'] ?>"
                                        >

                                        <button
                                            type="submit"
                                            name="delete_customer"
                                            class="btn btn-sm btn-customer-delete"
                                            title="Hapus pelanggan"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="5" class="text-center py-4">

                            <?= $keyword !== ''
                                ? 'Pelanggan tidak ditemukan.'
                                : 'Belum ada data pelanggan.' ?>

                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>


    <!-- PAGINATION -->
    <?php if ($totalPages > 1): ?>

        <?php
        $queryParams = [];

        if ($keyword !== '') {
            $queryParams['q'] = $keyword;
        }
        ?>

        <nav
            class="customer-pagination"
            aria-label="Navigasi halaman pelanggan"
        >

            <ul class="pagination-clean">

                <!-- HALAMAN SEBELUMNYA -->
                <?php if ($currentPage > 1): ?>

                    <li class="page-item">

                        <a
                            class="page-link page-arrow"
                            href="?<?= e(http_build_query(array_merge(
                                $queryParams,
                                ['page' => $currentPage - 1]
                            ))) ?>"
                            aria-label="Halaman sebelumnya"
                        >
                            <i class="bi bi-chevron-left"></i>
                        </a>

                    </li>

                <?php endif; ?>


                <!-- NOMOR HALAMAN -->
                <?php foreach ($pagesToShow as $item): ?>

                    <?php if ($item === '...'): ?>

                        <li class="page-item disabled">
                            <span class="page-link page-ellipsis">
                                &hellip;
                            </span>
                        </li>

                    <?php else: ?>

                        <li class="page-item <?= $currentPage === $item ? 'active' : '' ?>">

                            <a
                                class="page-link"
                                href="?<?= e(http_build_query(array_merge(
                                    $queryParams,
                                    ['page' => $item]
                                ))) ?>"
                            >
                                <?= $item ?>
                            </a>

                        </li>

                    <?php endif; ?>

                <?php endforeach; ?>


                <!-- HALAMAN BERIKUTNYA -->
                <?php if ($currentPage < $totalPages): ?>

                    <li class="page-item">

                        <a
                            class="page-link page-arrow"
                            href="?<?= e(http_build_query(array_merge(
                                $queryParams,
                                ['page' => $currentPage + 1]
                            ))) ?>"
                            aria-label="Halaman berikutnya"
                        >
                            <i class="bi bi-chevron-right"></i>
                        </a>

                    </li>

                <?php endif; ?>

            </ul>

        </nav>

    <?php endif; ?>

</div>


<!-- ==================================================
     MODAL TAMBAH PELANGGAN
================================================== -->

<div
    class="modal fade"
    id="addCustomerModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <form method="POST">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Tambah Pelanggan
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                    ></button>

                </div>


                <div class="modal-body">

                    <div class="customer-field mb-3">

                        <label>
                            Nama Pelanggan <span>*</span>
                        </label>

                        <input
                            type="text"
                            name="nama"
                            class="form-control"
                            placeholder="Masukkan nama pelanggan"
                            required
                        >

                    </div>


                    <div class="customer-field mb-3">

                        <label>
                            Nomor Telepon <span>*</span>
                        </label>

                        <input
                            type="text"
                            name="nomor_telepon"
                            class="form-control"
                            placeholder="Contoh: 081234567890"
                            required
                        >

                    </div>


                    <div class="customer-field">

                        <label>Alamat</label>

                        <textarea
                            name="alamat"
                            class="form-control"
                            rows="3"
                            placeholder="Masukkan alamat pelanggan"
                        ></textarea>

                    </div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        name="save_customer"
                        class="btn btn-primary customer-save-button"
                    >
                        Simpan
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<!-- ==================================================
     MODAL EDIT PELANGGAN
================================================== -->

<?php if ($editData): ?>

    <?php
    $cancelQuery = [
        'page' => $currentPage
    ];

    if ($keyword !== '') {
        $cancelQuery['q'] = $keyword;
    }
    ?>

    <div
        class="modal fade"
        id="editCustomerModal"
        tabindex="-1"
        aria-hidden="true"
    >

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <form method="POST">

                    <div class="modal-header">

                        <h5 class="modal-title">
                            Edit Pelanggan
                        </h5>

                        <a
                            href="index.php?<?= e(http_build_query($cancelQuery)) ?>"
                            class="btn-close"
                        ></a>

                    </div>


                    <div class="modal-body">

                        <input
                            type="hidden"
                            name="id_pelanggan"
                            value="<?= (int) $editData['id_pelanggan'] ?>"
                        >


                        <div class="customer-field mb-3">

                            <label>
                                Nama Pelanggan <span>*</span>
                            </label>

                            <input
                                type="text"
                                name="nama"
                                class="form-control"
                                value="<?= e($editData['nama']) ?>"
                                required
                            >

                        </div>


                        <div class="customer-field mb-3">

                            <label>
                                Nomor Telepon <span>*</span>
                            </label>

                            <input
                                type="text"
                                name="nomor_telepon"
                                class="form-control"
                                value="<?= e($editData['nomor_telepon']) ?>"
                                required
                            >

                        </div>


                        <div class="customer-field">

                            <label>Alamat</label>

                            <textarea
                                name="alamat"
                                class="form-control"
                                rows="3"
                            ><?= e($editData['alamat']) ?></textarea>

                        </div>

                    </div>


                    <div class="modal-footer">

                        <a
                            href="index.php?<?= e(http_build_query($cancelQuery)) ?>"
                            class="btn btn-light"
                        >
                            Batal
                        </a>

                        <button
                            type="submit"
                            name="save_customer"
                            class="btn btn-primary customer-save-button"
                        >
                            Simpan Perubahan
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    <!-- Tampilkan modal edit otomatis -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {

        const modalElement = document.getElementById('editCustomerModal');

        if (modalElement && typeof bootstrap !== 'undefined') {

            const editModal = new bootstrap.Modal(modalElement);

            editModal.show();

        }

    });
    </script>

<?php endif; ?>


<?php include __DIR__ . '/../includes/footer.php'; ?>
