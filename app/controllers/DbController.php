<?php

namespace App\controllers;
use App\exceptions\exceptionCustom;
use PDO;
use PDOException;

class DbController
{
    private static string $host = "";
    private static string $dbname = "";
    private static int $port = 0000;
    private static string $username = "";
    private static string $password = "";
    private static ?PDO $pdo = null;

    private static function env(string $key): string|int
    {
        $envValue = $_ENV[$key] ?? $_SERVER[$key] ?? "";
        if (!is_string($envValue) && !is_int($envValue)) {return "";}
        return $envValue;
    }

    public static function getPdo(): PDO
    {
        return self::getConnection() ?? throw new \RuntimeException("Não foi possível estabelecer conexão com o banco de dados.");
    }

    /**
     * @throws exceptionCustom
     */
    public static function getConnection(): PDO|null
    {
        try {
            if (self::$pdo instanceof PDO) {
                return self::$pdo;
            }

            self::$host = (string) self::env('DB_HOST');
            self::$port = (int) self::env('DB_PORT');
            self::$dbname = (string) self::env('DB_NAME');
            self::$username = (string) self::env('DB_USER');
            self::$password = (string) self::env('DB_PASS');
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
        id_user INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        email varchar(150) NOT NULL,
        cpf varchar(11) NOT NULL UNIQUE
    );
    ';

    $sqlProducts = '
        CREATE TABLE IF NOT EXISTS products (
        id_products INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        image VARCHAR(150) NOT NULL,
        date_created DATETIME DEFAULT CURRENT_TIMESTAMP
    );
    ';

    $sqlStock = '
        CREATE TABLE IF NOT EXISTS stock (
        id_stock INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        quantity INT DEFAULT 0,
        update_in DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id_products)  ON DELETE CASCADE
    );
    ';

    $sqlCupons = '
    CREATE TABLE IF NOT EXISTS cupons (
        id_cupom INT AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(50) NOT NULL UNIQUE,
        discount_percent DECIMAL(5,2) DEFAULT NULL,
        discount_value DECIMAL(10,2) DEFAULT NULL,
        active TINYINT(1) NOT NULL DEFAULT 1,
        expires_at DATETIME DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ';

    $sqlOrders = "
        CREATE TABLE IF NOT EXISTS orders (
            id_orders INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            cupom_id INT DEFAULT NULL,
            total_price DECIMAL(10, 2) NOT NULL,
            shipping_price DECIMAL(10, 2) DEFAULT 0.00,
            status ENUM('PENDENTE', 'PAGO', 'CANCELADO') DEFAULT 'PENDENTE',
            order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            address_street VARCHAR(255) NOT NULL,
            address_number VARCHAR(20) NOT NULL,
            address_complement VARCHAR(100) DEFAULT NULL,
            address_neighborhood VARCHAR(100) NOT NULL,
            address_city VARCHAR(100) NOT NULL,
            address_state VARCHAR(2) NOT NULL,
            address_zipcode VARCHAR(9) NOT NULL,
    FOREIGN KEY (cupom_id) REFERENCES cupons(id_cupom),
    FOREIGN KEY (user_id) REFERENCES users(id_user)                              
    );
    ";

    $sqlOrderItems = '
        CREATE TABLE IF NOT EXISTS items_order (
        id_orderitems INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        unit_price DECIMAL(10, 2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id_orders) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id_products)
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