<?php

namespace App\Controllers;
use App\Exceptions\exceptionCustom;
use PDO;
use PDOException;

class DbController
{
    private static string $host = "";
    private static string $dbname = "";
    private static string $port = "";
    private static string $username = "";
    private static string $password = "";
    private static ?PDO $pdo = null;

    private static function env(string $key): mixed
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? null;
    }

    public static function getPdo(): PDO
    {
        return self::$pdo;
    }

    /**
     * @throws exceptionCustom
     */
    public static function getConnection(): PDO
    {
        try {
            if (self::$pdo instanceof PDO) {
                return self::$pdo;
            }

            self::$host = self::env('DB_HOST');
            self::$port = self::env('DB_PORT');
            self::$dbname = self::env('DB_NAME');
            self::$username = self::env('DB_USER');
            self::$password = self::env('DB_PASS');
            $dsn = "mysql:host=" . self::$host . ";port=" . self::$port . ";dbname=" . self::$dbname . ";charset=utf8mb4";
            self::$pdo = new PDO($dsn, self::$username, self::$password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            self::$pdo = new PDO("mysql:host=" . self::$host . ";dbname=" . self::$dbname . ";charset=utf8", self::$username, self::$password);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            self::createTables(self::$pdo);

        } catch (PDOException $e) {
            throw new exceptionCustom("Erro ao conectar ao banco de dados: ", 404, $e);
        }
        return self::$pdo;
    }
    private static function createTables(PDO $pdo): void
    {
    $sqlUsers = '
        CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(150) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL
    );
    ';

    $sqlProducts = '
        CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        image VARCHAR(150) NOT NULL,
        date_created DATETIME DEFAULT CURRENT_TIMESTAMP
    );
    ';

    $sqlStock = '
        CREATE TABLE IF NOT EXISTS estoque (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        quantity INT DEFAULT 0,
        update_in DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    );
    ';

    $sqlOrders = "
        CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        cupom_id INT DEFAULT NULL,
        total_price DECIMAL(10, 2) NOT NULL,
        status ENUM('PENDENTE', 'PAGO', 'CANCELADO') DEFAULT 'PENDENTE',
        order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (cupom_id) REFERENCES cupons(id)
    );
    ";

    $sqlOrderItems = '
    CREATE TABLE IF NOT EXISTS items_order (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        unit_price DECIMAL(10, 2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id)
    );
    ';
    $sqlCupons = '
    CREATE TABLE IF NOT EXISTS cupons (
        id INT AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(50) NOT NULL UNIQUE,
        discount_percent DECIMAL(5,2) DEFAULT NULL,
        discount_value DECIMAL(10,2) DEFAULT NULL,
        active TINYINT(1) NOT NULL DEFAULT 1,
        expires_at DATETIME DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ';
        $pdo->exec($sqlUsers);
        $pdo->exec($sqlProducts);
        $pdo->exec($sqlStock);
        $pdo->exec($sqlCupons);
        $pdo->exec($sqlOrders);
        $pdo->exec($sqlOrderItems);

    }

}