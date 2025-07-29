<?php

namespace App\controllers;

use _PHPStan_5878035a0\Nette\Utils\Json;
use App\exceptions\addToCartExceptions;
use App\exceptions\exceptionCustom;
use App\exceptions\invalidArgumentsForProductsException;
use PDO;
use PDOException;

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
            $stmt = $pdo->prepare("
                SELECT
                p.id_products,
                p.name,
                p.price,
                p.image,
                e.quantity
                FROM products p
                INNER JOIN stock e ON p.id_products = e.product_id
                where e.quantity > 0;
            ");
            $stmt->execute();
            /** @var array<int, array{id: int,name: string, price: float, image: string, quantity:int}> $produtos */
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
        $prodPreco = (float)filter_input(INPUT_POST, 'preco', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
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
            $sql = "INSERT INTO stock (product_id, quantity) VALUES (?, ?)";
            $stmt = DbController::getPdo()->prepare($sql);
            $stmt->execute([$prodId, $prodEstoque]);

            http_response_code(200);
            header("Location: /produtos");
            exit;
        } catch (\PDOException | invalidArgumentsForProductsException $ex){
            throw new exceptionCustom("Erro ao criar o produto: ", 404, $ex);
        }
    }

    /**
     * @throws exceptionCustom
     */
    public function excluirProduto(): void
    {
        dbController::getConnection();
        $id = filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT);

        try {
            $stmt = dbController::getPdo()->prepare("SELECT image FROM products WHERE id_products = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            /** @var array<string, array{image: string}> $prod */
            $prod = $stmt->fetch(PDO::FETCH_ASSOC);
            if (isset($prod['image'])) {
                $caminhoRelativo = str_replace('/', DIRECTORY_SEPARATOR, $prod['image']);

                /** @var string $caminhoAbsoluto */
                $caminhoAbsoluto = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . $caminhoRelativo;
                if (file_exists($caminhoAbsoluto)) {
                    unlink($caminhoAbsoluto);
                }
            }
            $stmt = dbController::getPdo()->prepare("DELETE FROM stock WHERE product_id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $stmt = dbController::getPdo()->prepare("DELETE FROM products WHERE id_products = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            http_response_code(200);
            header("Location: /produtos");
        } catch (PDOException $ex) {
            new exceptionCustom("Erro ao deletar o produto: ", 404, $ex);
        }
    }
    public function atualizarCarrinho():Json
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        header('Content-Type: application/json');

        $id_product = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT) ?: '';
        $nome_product = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
        $preco_product = (float)filter_input(INPUT_POST, 'preco', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
        $estoque_product = filter_input(INPUT_POST, 'estoque', FILTER_SANITIZE_NUMBER_INT) ?: 1;
        $estoque_product_max = filter_input(INPUT_POST, 'max_estoque', FILTER_SANITIZE_NUMBER_INT) ?: 1;
        $imagem_product = filter_input(INPUT_POST, 'imagem', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';


        if (!empty($id_product) || !empty($nome_product) || !empty($preco_product) || !empty($estoque_product)) {

            $_SESSION['cart'][$id_product] = [
                [
                'id' => (int)$id_product,
                'name' => $nome_product,
                'price' => (float)$preco_product,
                'stock' => (int)$estoque_product,
                'image' => $imagem_product,
                'stock_max' => (int)$estoque_product_max
                ]
            ];
            http_response_code(200);
            echo json_encode(['status' => 'ok', 'mensagem' => 'Produto adicionado no carrinho']);
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'erro', 'mensagem' => 'Dados inválidos ou faltantes.']);
        }
        exit;
    }
}