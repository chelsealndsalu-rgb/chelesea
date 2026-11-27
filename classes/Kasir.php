<?php
require_once __DIR__ . "/Database.php";

class Kasir
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function login(string $username, string $password): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT id_kasir, username, password FROM kasir WHERE username = ? LIMIT 1"
        );
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $kasir = $stmt->get_result()->fetch_assoc();

        // Mengikuti skema akun demo tugas yang saat ini menyimpan password teks biasa.
        if ($kasir && $password === $kasir["password"]) {
            return $kasir;
        }

        return null;
    }

    public function verify(string $username, string $password): bool
    {
        return $this->login($username, $password) !== null;
    }
}
