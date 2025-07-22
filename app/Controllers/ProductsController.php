<?php

namespace App\Controllers;

use App\Exceptions\exceptionCustom;
use App\Exceptions\invalidArgumentsForProductsException;
use PDO;

class ProductsController
{
    /** @var array<string> */
    private static array $allowedExtensionImg = ['png', 'jpg', 'jpeg'];
    private static string $uploadDir = "app/Static/uploads/";

    /**
     * @throws exceptionCustom
     */
    public function index():void
    {
        Controller::view("products");
    }

    /**
     * @throws exceptionCustom
     */
    public static function getProdutos(): array
    {
        DbController::getConnection();
        try {
            $pdo = DbController::getPdo();
            $stmt = $pdo->prepare("Select name,price,image from products");
            $stmt->execute();
            /** @var array<int, array{name: string, price: float, image: string}> $produtos */
            $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $produtos;
        } catch (\PDOException $ex){
            throw new exceptionCustom("Erro ao obter os produtos: ", 404, $ex);
        }
    }

    /**
     * @throws exceptionCustom
     */
    public static function criarProduto(): void
    {
        DbController::getConnection();
        /** @var string $prodName */
        $prodNome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
        $prodPreco = filter_input(INPUT_POST, 'preco', FILTER_SANITIZE_NUMBER_FLOAT) ?: '';
        $prodEstoque = filter_input(INPUT_POST, 'estoque', FILTER_SANITIZE_NUMBER_INT) ?: '';

        try {
            $image = null;

            if (!is_dir(self::$uploadDir)) {
                mkdir(self::$uploadDir, 0755, true);
            }

            if (
                isset($_FILES['imagem']) &&
                is_array($_FILES['imagem']) &&
                $_FILES['imagem']['error'] === UPLOAD_ERR_OK
            ) {
                /** @var array{tmp_name: string, name: string, error: int} $imagem */
                $imagem = $_FILES['imagem'];
                $imgTempPath = $imagem['tmp_name'];
                $nameImg = $imagem['name'];
                $extension = strtolower(pathinfo($nameImg, PATHINFO_EXTENSION));
                if (!in_array($extension, self::$allowedExtensionImg)) {
                    throw new invalidArgumentsForProductsException("Imagem não é valida");
                }
                $newNameImg = uniqid('img_', true) . '.' . $extension;
                $imgPath = self::$uploadDir . $newNameImg;
                if (move_uploaded_file($imgTempPath, $imgPath)) {
                    $image =  self::$uploadDir . $newNameImg;
                }
            }
            $sql = "INSERT INTO products (name, price, image) VALUES (?, ?, ?)";
            $stmt = DbController::getPdo()->prepare($sql);
            $stmt->execute([$prodNome, $prodPreco, $image]);
            $prodId = DbController::getPdo()->lastInsertId();
            $sql = "SELECT id FROM products WHERE id = :prodId";
            $stmt = DbController::getPdo()->prepare($sql);
            $stmt->bindParam(':prodId', $prodId);
            $stmt->execute();
            $sql = "INSERT INTO estoque (product_id, quantity) VALUES (?, ?)";
            $stmt = DbController::getPdo()->prepare($sql);
            $stmt->bindParam(':prodName', $prodId);
            $stmt->execute();

            $_SESSION['modal'] = [
                'msg' => "Produto: $prodName cadastrado com sucesso!",
                'statuscode' => 200
            ];
            http_response_code(200);
            header("Location: /produtos");
            exit;
        } catch (\PDOException | invalidArgumentsForProductsException $ex){
            $_SESSION['modal'] = [
                'msg' => "Erro ao cadastrar o Produto $prodNome: " . $ex->getMessage(),
                'statuscode' => 404
            ];
            throw new exceptionCustom("Erro ao criar o produto: ", 404, $ex);
        }
    }
}