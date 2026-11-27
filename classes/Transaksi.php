
<?php
require_once __DIR__ . "/Database.php";

class Transaksi
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // Mengambil ringkasan transaksi dan pendapatan hari ini
    public function getDashboardSummary(): array
    {
        $today = date("Y-m-d");

        // Menghitung jumlah transaksi hari ini
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS jumlah
             FROM transaksi
             WHERE DATE(tanggal_transaksi) = ?"
        );

        $stmt->bind_param("s", $today);
        $stmt->execute();

        $count = (int) $stmt->get_result()->fetch_assoc()["jumlah"];
        $stmt->close();

        // Menghitung pendapatan dari transaksi yang sudah lunas
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(total_pembayaran), 0) AS total
             FROM transaksi
             WHERE DATE(tanggal_transaksi) = ?
               AND status_pembayaran = 'Lunas'"
        );

        $stmt->bind_param("s", $today);
        $stmt->execute();

        $income = (float) $stmt->get_result()->fetch_assoc()["total"];
        $stmt->close();

        return [
            "jumlah" => $count,
            "total" => $income
        ];
    }

    // Menghitung jumlah transaksi yang masih dalam proses
    public function getInProgressCount(): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS jumlah
             FROM transaksi
             WHERE status_pengerjaan = 'Dalam Proses'"
        );

        $stmt->execute();

        $count = (int) $stmt->get_result()->fetch_assoc()["jumlah"];
        $stmt->close();

        return $count;
    }

    // Mengambil transaksi terbaru
    public function getRecent(int $limit = 5): mysqli_result
    {
        $limit = max(1, min($limit, 50));

        return $this->db->query(
            "SELECT
                t.*,
                p.nama,
                l.nama_layanan
             FROM transaksi t
             JOIN pelanggan p
                ON t.id_pelanggan = p.id_pelanggan
             JOIN layanan l
                ON t.id_layanan = l.id_layanan
             ORDER BY t.tanggal_transaksi DESC
             LIMIT " . $limit
        );
    }

    // Mengambil laporan pendapatan berdasarkan periode
    public function getReport(
        string $startDateTime,
        string $endDateTime
    ): array {

        // Menghitung jumlah transaksi dan total pendapatan
        $stmt = $this->db->prepare(
            "SELECT
                COUNT(*) AS jumlah,
                COALESCE(SUM(total_pembayaran), 0) AS total
             FROM transaksi
             WHERE tanggal_transaksi >= ?
               AND tanggal_transaksi < ?
               AND status_pembayaran = 'Lunas'"
        );

        $stmt->bind_param("ss", $startDateTime, $endDateTime);
        $stmt->execute();

        $summary = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // Menghitung pendapatan berdasarkan metode pembayaran
        $stmt = $this->db->prepare(
            "SELECT
                metode_pembayaran,
                COUNT(*) AS jumlah,
                COALESCE(SUM(total_pembayaran), 0) AS total
             FROM transaksi
             WHERE tanggal_transaksi >= ?
               AND tanggal_transaksi < ?
               AND status_pembayaran = 'Lunas'
             GROUP BY metode_pembayaran"
        );

        $stmt->bind_param("ss", $startDateTime, $endDateTime);
        $stmt->execute();

        $result = $stmt->get_result();

        $methods = [
            "QRIS" => [
                "jumlah" => 0,
                "total" => 0
            ],
            "Tunai" => [
                "jumlah" => 0,
                "total" => 0
            ]
        ];

        while ($row = $result->fetch_assoc()) {
            $method = $row["metode_pembayaran"];

            if (isset($methods[$method])) {
                $methods[$method] = [
                    "jumlah" => (int) $row["jumlah"],
                    "total" => (float) $row["total"]
                ];
            }
        }

        $stmt->close();

        return [
            "jumlah" => (int) $summary["jumlah"],
            "total" => (float) $summary["total"],
            "methods" => $methods
        ];
    }
}
