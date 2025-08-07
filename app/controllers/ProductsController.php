<?php

namespace App\controllers;

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
     * @return array<int, array{id: int,name: string, price: float, image: string, quantity:int}>
     */
    public static function getProdutos(): array
    {
        try {
            DbController::getConnection();

            $stmt = DbController::getPdo()->prepare("
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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if(!isset($_SESSION['username'])){
            echo json_encode(["code"=>404,"Você não tem acesso para realizar essa operação"]);
            throw new exceptionCustom("Erro ao criar o produto: ",404,new invalidArgumentsForProductsException("Sem acesso"));
        }
        DbController::getConnection();

        /** @var string $prodNome */
        $prodNome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
        $inputPreco = filter_input(INPUT_POST, 'preco', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $prodPreco = floatval(is_scalar($inputPreco) ? $inputPreco : 0.0);
        $prodEstoque = filter_input(INPUT_POST, 'estoque', FILTER_SANITIZE_NUMBER_INT) ?: '';
        try {
            if($prodPreco == 0.0){
                throw new invalidArgumentsForProductsException("Produto sem preço");
            }
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
            header("location: /produtos");
            exit;
        } catch (invalidArgumentsForProductsException $ex){
            throw new exceptionCustom("Erro ao criar o produto: ", 404, $ex);
        } catch (PDOException $e){
            Logger::error($e->getMessage(),404,$e);
            throw new exceptionCustom("Erro ao obter itens do pedido: ", 400,$e);
        }
    }

    /**
     * @throws exceptionCustom
     */
    public function excluirProduto(): void
    {
        $id = filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT);

        try {
            DbController::getConnection();
            $stmt = DbController::getPdo()->prepare("SELECT image FROM products WHERE id_products = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            /** @var array<string, array{image: string}> $prod */
            $prod = $stmt->fetch(PDO::FETCH_ASSOC);
            if (isset($prod['image'])) {
                /** @var string $caminhoRelativo */
                $caminhoRelativo = str_replace('/', DIRECTORY_SEPARATOR, $prod['image']);
                $caminhoAbsoluto = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . $caminhoRelativo;
                if (file_exists($caminhoAbsoluto)) {
                    unlink($caminhoAbsoluto);
                }
            }
            $stmt = DbController::getPdo()->prepare("DELETE FROM stock WHERE product_id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $stmt = DbController::getPdo()->prepare("DELETE FROM products WHERE id_products = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            http_response_code(200);
            header("location: /produtos");
            exit;
        } catch (PDOException $ex) {
            http_response_code(404);
            header("location: /produtos");
            throw new exceptionCustom("Erro ao deletar o produto: ", 404, $ex);
        }
    }
}