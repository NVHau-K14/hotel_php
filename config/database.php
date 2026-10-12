<?php
// config/database.php

class Database {
    private static ?PDO $instance = null;

    private const DB_HOST = 'localhost';
    private const DB_PORT = '3306';
    private const DB_NAME = 'QUANLYKHACHSAN';
    private const DB_USER = 'root';
    private const DB_PASS = '';

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $dsn = 'mysql:host=' . (getenv('HOTEL_DB_HOST') ?: self::DB_HOST)
                . ';port=' . (getenv('HOTEL_DB_PORT') ?: self::DB_PORT)
                . ';dbname=' . (getenv('HOTEL_DB_NAME') ?: self::DB_NAME) . ';charset=utf8mb4';
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ];
            try {
                self::$instance = new PDO($dsn, getenv('HOTEL_DB_USER') ?: self::DB_USER, getenv('HOTEL_DB_PASS') ?: self::DB_PASS, $options);
                self::$instance->exec("SET time_zone = '+07:00'");
                self::$instance->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED');
            } catch (PDOException $e) {
                error_log($e->getMessage());
                throw new RuntimeException('Không thể kết nối cơ sở dữ liệu. Vui lòng kiểm tra cấu hình máy chủ.');
            }
        }
        return self::$instance;
    }
}

function getDB(): PDO {
    return Database::getConnection();
}

/** Share an existing transaction; only its owner may commit or roll it back. */
function db_transaction(callable $callback): mixed {
    $db = getDB();
    $owner = !$db->inTransaction();
    if ($owner) $db->beginTransaction();
    try {
        $result = $callback($db);
        if ($owner) $db->commit();
        return $result;
    } catch (Throwable $error) {
        if ($owner && $db->inTransaction()) $db->rollBack();
        throw $error;
    }
}
