<?php
require_once __DIR__ . "/Database.php";

class Layanan
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll(): mysqli_result
    {
        return $this->db->query("SELECT * FROM layanan ORDER BY id_layanan");
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM layanan WHERE id_layanan = ? LIMIT 1"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();
        return $data ?: null;
    }
}
