<?php
class Database
{
    private static ?mysqli $connection = null;

    public static function getConnection(): mysqli
    {
        if (self::$connection === null) {
            $host = "localhost";
            $username = "root";
            $password = "";
            $database = "laundrypos";

            self::$connection = new mysqli($host, $username, $password, $database);

            if (self::$connection->connect_error) {
                die("Koneksi database gagal: " . self::$connection->connect_error);
            }

            self::$connection->set_charset("utf8mb4");
        }

        return self::$connection;
    }
}
