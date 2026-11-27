
<?php

require_once __DIR__ . '/Database.php';

class Pelanggan
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // Menghitung jumlah pelanggan berdasarkan pencarian
    public function countAll(string $keyword = ''): int
    {
        $search = '%' . $keyword . '%';

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS total
             FROM pelanggan
             WHERE nama LIKE ?
             OR nomor_telepon LIKE ?"
        );

        $stmt->bind_param('ss', $search, $search);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $stmt->close();

        return (int) $row['total'];
    }

    // Mengambil data pelanggan dengan pagination
    public function getAll(
        string $keyword = '',
        int $limit = 10,
        int $offset = 0
    ): mysqli_result {

        $search = '%' . $keyword . '%';

        // Membatasi nilai pagination
        $limit = max(1, min($limit, 100));
        $offset = max(0, $offset);

        $stmt = $this->db->prepare(
            "SELECT *
             FROM pelanggan
             WHERE nama LIKE ?
             OR nomor_telepon LIKE ?
             ORDER BY id_pelanggan DESC
             LIMIT ? OFFSET ?"
        );

        $stmt->bind_param(
            'ssii',
            $search,
            $search,
            $limit,
            $offset
        );

        $stmt->execute();

        return $stmt->get_result();
    }

    // Mengambil satu data pelanggan berdasarkan ID
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT *
             FROM pelanggan
             WHERE id_pelanggan = ?"
        );

        $stmt->bind_param('i', $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        $stmt->close();

        return $data ?: null;
    }

    // Menambah atau memperbarui pelanggan
    public function save(
        int $id,
        string $nama,
        string $alamat,
        string $telepon
    ): bool {

        if ($id > 0) {

            $stmt = $this->db->prepare(
                "UPDATE pelanggan
                 SET nama = ?,
                     alamat = ?,
                     nomor_telepon = ?
                 WHERE id_pelanggan = ?"
            );

            $stmt->bind_param(
                'sssi',
                $nama,
                $alamat,
                $telepon,
                $id
            );

        } else {

            $stmt = $this->db->prepare(
                "INSERT INTO pelanggan
                 (nama, alamat, nomor_telepon)
                 VALUES (?, ?, ?)"
            );

            $stmt->bind_param(
                'sss',
                $nama,
                $alamat,
                $telepon
            );
        }

        $success = $stmt->execute();

        $stmt->close();

        return $success;
    }

    // Menghapus pelanggan yang belum memiliki transaksi
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM pelanggan
             WHERE id_pelanggan = ?
             AND NOT EXISTS (
                 SELECT 1
                 FROM transaksi
                 WHERE transaksi.id_pelanggan = pelanggan.id_pelanggan
             )"
        );

        $stmt->bind_param('i', $id);
        $stmt->execute();

        $deleted = $stmt->affected_rows > 0;

        $stmt->close();

        return $deleted;
    }
}
