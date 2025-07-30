<?php

namespace App\controllers;

use App\exceptions\exceptionCustom;
use App\exceptions\ordersFinishException;
use Exception;
use PDO;
use PDOException;

class OrdersController
{

    /**
     * @throws exceptionCustom
     */
    function index(): void {
        Controller::view("orders");
    }
    /**
     * @throws exceptionCustom
     */
    function finalizarCompra():void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $cart = json_decode(filter_input(INPUT_POST,'cart'),true);
        if(empty($cart)){
            echo json_encode(["status"=>404,"messages"=>"Carrinho está vazio"]);
            exit;
        }
        $ids = array_map('intval', array_column($cart, 'id'));

        try {
            DbController::getConnection();
            $ids_separados = implode(',', array_fill(0, count($ids), '?'));
            if (empty($ids)) {
                echo json_encode(["Erro: "=>$ids_separados]);
                throw new ordersFinishException("Nenhum produto no carrinho para buscar.");
            }
            $sql = "
                SELECT p.id_products, p.name, p.price, s.quantity as stock
                FROM products p
                JOIN stock s ON s.product_id = p.id_products
                WHERE p.id_products IN ($ids_separados)
                ";
            $stmt = DbController::getPdo()->prepare($sql);
            $stmt->execute($ids);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $productHashMap = [];
            foreach ($products as $p) {
                $productHashMap[$p['id_products']] = $p;
            }
            $total = 0;
            $errors = [];

            foreach ($cart as $item) {
                $cart_item_id = intval($item['id']);
                $cart_item_qtnd = intval($item['stock']);
                $cart_item_price = floatval($item['price']);
                if (!isset($productHashMap[$cart_item_id])) {
                    $errors[] = "Produto ID $cart_item_id não encontrado.";
                    continue;
                }
                $product = $productHashMap[$cart_item_id];
                if ($cart_item_qtnd < 1) {
                    $errors[] = "Quantidade inválida para o produto {$product['name']}.";
                    continue;
                }
                if ($cart_item_qtnd > $product['stock']) {
                    $errors[] = "Estoque insuficiente para o produto {$product['name']}.";
                    continue;
                }
                if (floatval($product['price']) !== $cart_item_price) {
                    $errors[] = "Preço inválido para o produto {$product['name']}.";
                    continue;
                }
                $total += $cart_item_qtnd * $product['price'];
            }
            if (!empty($errors)) {
                echo json_encode(['status' => 400,'errors' => $errors]);
                exit;
            }
            $this->criarPedido($cart,$total);
        }catch (PDOException $ex){
            echo json_encode(["code"=>404,"messages"=>"Erro ao finalizar a compra."]);
            throw new exceptionCustom("Erro ao finalizar a compra", 404,$ex);
        }catch (ordersFinishException $ex){
            echo json_encode(["code"=>404,"messages"=>"Erro ao obter os ids: $ex"]);
            throw new exceptionCustom("Erro ao obter os ids: ", 404,$ex);
        }
    }

    /**
     * @throws exceptionCustom
     */
    function criarPedido(Array $cart, float $total): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['userid'] ?? null;
        $cupomId = null;
        $status = 'Pendente';
        $orderDate = date('Y-m-d H:i:s');

        try {
            DbController::getPdo()->beginTransaction();
            if(empty($userId)){
                throw new ordersFinishException("Id so usuario está vazio");
            }
            $stmtOrder = DbController::getPdo()->prepare("
                INSERT INTO orders (user_id, cupom_id, total_price, status, order_date) VALUES (?, ?, ?, ?, ?)");
            $stmtOrder->execute([
                $userId,
                $cupomId,
                $total,
                $status,
                $orderDate
            ]);

            $orderId = DbController::getPdo()->lastInsertId();
            $stmtItem =DbController::getPdo()->prepare("
                INSERT INTO items_order (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
            foreach ($cart as $item) {
                $productId = intval($item['id']);
                $quantity = intval($item['stock']);
                $unitPrice = floatval($item['price']);

                $stmtItem->execute([
                    $orderId,
                    $productId,
                    $quantity,
                    $unitPrice
                ]);
            }

            DbController::getPdo()->commit();
            $this->descontarEstoque($cart);
        } catch (ordersFinishException $e) {
            DbController::getPdo()->rollBack();
            echo json_encode(["code"=>400,"messages"=>"Erro ao obter o id para criar o pedido: $e"]);
            throw new exceptionCustom("Erro ao obter o id para criar o pedido: ",400,$e);
        } catch (Exception $e) {
            DbController::getPdo()->rollBack();
            echo json_encode(["code"=>400,"messages"=>"Erro ao criar o pedido"]);
            throw new exceptionCustom("Erro ao criar o pedido: ",400,$e);
        }
    }

    /**
     * @throws exceptionCustom
     */
    function descontarEstoque(array $cart): void {
        DbController::getConnection();
        try {
        foreach ($cart as $item) {
            $product_id = intval($item['id']);
            $quantity = intval($item['stock']);
            $stmt = DbController::getPdo()->prepare("SELECT id_stock, quantity FROM stock WHERE product_id = ?");
            $stmt->execute([$product_id]);
            $stock = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$stock) {
                throw new ordersFinishException("Não foi possível descontar o estoque do produto $product_id.");
            }

            $estoqueAtual = intval($stock['quantity']);
            $id_stock = $stock['id_stock'];

            if ($estoqueAtual < $quantity) {
                throw new ordersFinishException("Estoque insuficiente para o produto ID $product_id. Em estoque: $estoqueAtual, solicitado: $quantity");
            }
            $stmtUpdate = DbController::getPdo()->prepare("UPDATE stock SET quantity = quantity - ?, update_in = NOW() WHERE id_stock = ?");
            $stmtUpdate->execute([$quantity, $id_stock]);
            echo json_encode(["code"=>200,"messages"=>"Estoque descontado com sucesso"]);
        }
        } catch (Exception | PDOException | exceptionCustom $ex){
            echo json_encode(["code"=>400,"messages"=>"Erro ao criar o pedido"]);
            throw new exceptionCustom("Erro ao criar o pedido: ",400,$ex);
        }
    }

    /**
     * @throws exceptionCustom
     */
    static function getPedidosPorUsuario(int $userid): array{
        try {
            DbController::getConnection();
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            if(!$userid){
                throw new ordersFinishException("Não há pedido para este usuario");
            }
            $stmt = DbController::getPdo()->prepare("
            SELECT 
                o.id_orders AS order_id,
                o.user_id AS user_id,
                o.status AS order_status,
                o.order_date AS order_date,
                io.id_orderitems AS item_id,
                io.product_id AS product_id,
                io.quantity AS item_quantity,
                io.unit_price AS item_unit_price,
                p.name AS product_name,
                p.image AS product_image
            FROM orders o
            INNER JOIN items_order io ON o.id_orders = io.order_id
            INNER JOIN products p ON io.product_id = p.id_products
            WHERE o.user_id = ?
            ORDER BY o.id_orders DESC;
            ");
            $stmt->execute([$userid]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex){
            throw new exceptionCustom("Erro ao obter os pedidos: ", 400,$ex);
        }
    }


}