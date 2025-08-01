<?php

namespace App\controllers;

use _PHPStan_5878035a0\Nette\Utils\Json;
use App\exceptions\exceptionCustom;
use App\exceptions\invalidParametersAuthException;
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
    function indexCheckout(): void {
        Controller::view("checkout");
    }
    /**
     * @throws exceptionCustom
     */
    function finalizarCompra():void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $formData = json_decode(filter_input(INPUT_POST,'formCheckout'),true);
        if(empty($formData)){
            echo json_encode(["code"=>404,"message"=>"Formulário com dados ausentes está vazio"]);
            exit;
        }
        $ids = array_map('intval', array_column($formData['cart'], 'id'));

        try {
            DbController::getConnection();
            $ids_separados = implode(',', array_fill(0, count($ids), '?'));
            if (empty($ids)) {
                echo json_encode(["code"=>404,"message"=>"Erro ao tentar separar os id's: ".$ids_separados]);
                throw new ordersFinishException("Nenhum produto no formulario.");
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

            foreach ($formData['cart'] as $item) {
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
            $frete = floatval(preg_replace("/[^-0-9.]/",".",$formData['frete']));
            $formData['frete'] = $frete;
            $total += $frete;
            $this->criarPedido($formData,$total);
        }catch (PDOException $ex){
            echo json_encode(["code"=>404,"message"=>"Erro ao finalizar a compra."]);
            throw new exceptionCustom("Erro ao finalizar a compra", 404,$ex);
        }catch (ordersFinishException $ex){
            echo json_encode(["code"=>404,"message"=>"Erro ao obter os ids: $ex"]);
            throw new exceptionCustom("Erro ao obter os ids: ", 404,$ex);
        }
    }

    /**
     * @throws exceptionCustom
     */
    function criarPedido(Array $formData, float $total): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['userid'] ?? null;
        $cupomId = null;
        $orderDate = date('Y-m-d H:i:s');
        $frete = $formData['frete'] ?? null;
        $zipcode = $formData['zipcode'] ?? null;
        $street = $formData['street'] ?? null;
        $number = $formData['number'] ?? null;
        $complement = $formData['complement'] ?? null;
        $neighborhood = $formData['neighborhood'] ?? null;
        $city = $formData['city'] ?? null;
        $state = $formData['state'] ?? null;
        if(empty($userId)){
            throw new ordersFinishException("Usuário não encontrado.");
        }

        try {
            DbController::getPdo()->beginTransaction();
            if(empty($zipcode) || empty($street) || empty($city) || empty($state)){
                echo json_encode(["code"=>404,"message"=>"Erro ao obter o endereço do cliente para criar o pedido"]);
                throw new ordersFinishException("Não obtido dados do endereço do cliente.");
            }
            $stmtOrder = DbController::getPdo()->prepare("
                INSERT INTO orders (
                                    user_id,
                                    cupom_id,
                                    total_price,
                                    order_date,
                                    shipping_price,
                                    address_street,
                                    address_number,
                                    address_complement,
                                    address_neighborhood,
                                    address_city,
                                    address_state,
                                    address_zipcode
                                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)");
            $stmtOrder->execute([
                $userId,
                $cupomId,
                $total,
                $orderDate,
                $frete,
                $street,
                $number,
                $complement,
                $neighborhood,
                $city,
                $state,
                $zipcode
            ]);

            $orderId = DbController::getPdo()->lastInsertId();
            $stmtItem =DbController::getPdo()->prepare("
                INSERT INTO items_order (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
            foreach ($formData['cart'] as $item) {
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
            echo json_encode(["code"=>200, "message"=>"Pedido criado com sucesso"]);
            $this->descontarEstoque($formData['cart']);
        } catch (ordersFinishException $e) {
            DbController::getPdo()->rollBack();
            echo json_encode(["code"=>404,"message"=>"Erro ao criar o pedido: " . $e->getMessage()]);
            throw new exceptionCustom("Erro ao criar o pedido: ",400,$e);
        } catch (Exception $e) {
            DbController::getPdo()->rollBack();
            echo json_encode(["code"=>404,"message"=>"Erro no pedido: ".$e->getMessage()]);
            throw new exceptionCustom("Erro ao criar o pedido: ",400,$e);
        }
    }

    /**
     * @throws exceptionCustom
     */
    function descontarEstoque(array $cart): void {

        try {
            if(empty($cart)){
                throw new ordersFinishException("Itens do carrinho não foram enviados com sucesso");
            }
            DbController::getConnection();
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
        }
        } catch (PDOException | ordersFinishException $ex){
            echo json_encode(["code"=>400,"message"=>"Erro ao criar o pedido"]);
            throw new exceptionCustom("Erro ao criar o pedido: ",400,$ex);
        }
    }

    /**
     * @throws exceptionCustom
     */
    static function getPedidosPorUsuario(int $userid): array{
        try {
            if(!$userid){
                throw new ordersFinishException("Não há pedido para este usuario");
            }
            DbController::getConnection();
            $stmt = DbController::getPdo()->prepare("
            SELECT 
                o.id_orders AS order_id,
                o.status AS order_status,
                o.order_date AS order_date,
                o.total_price as total_price
            FROM orders o
            WHERE o.user_id = ?
            ORDER BY o.id_orders DESC LIMIT 10
            ;
            ");
            $stmt->execute([$userid]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex){
            throw new exceptionCustom("Erro ao obter os pedidos: ", 400,$ex);
        } catch (ordersFinishException $ex){
            throw new exceptionCustom("Erro ao obter o id do usuario: ", 404, $ex);
        }
    }

    /**
     * @throws exceptionCustom
     */
    static function getItemsPedido(): Json
    {
        $order_id = filter_input(INPUT_POST,'order_id',FILTER_SANITIZE_NUMBER_INT);
        try {
            DbController::getConnection();

            $sql = "
                SELECT 
                    o.id_orders AS order_id,
                    o.status AS order_status,
                    o.order_date AS order_date,
                    o.total_price AS total_price,
                    o.shipping_price as shipping_price,
                    io.id_orderitems AS item_id,
                    io.product_id AS product_id,
                    io.quantity AS item_quantity,
                    io.unit_price AS item_unit_price,
                    p.name AS product_name,
                    p.image AS product_image
                FROM orders o
                INNER JOIN items_order io ON o.id_orders = io.order_id
                INNER JOIN products p ON io.product_id = p.id_products
                WHERE o.id_orders = ?
            ";

            $stmt = DbController::getPdo()->prepare($sql);
            $stmt->execute([$order_id]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(["code"=>200,"products"=>$result]);
            exit;
        } catch (PDOException | exceptionCustom $e) {
            echo json_encode(["code"=>404,"message"=>"Erro ao obter o produto do pedido: " . $e->getMessage()]);
            throw new exceptionCustom("Erro ao obter o produto: ", 404, $e);
        }
    }

    /**
     * @throws exceptionCustom
     */
    static function atualizarStatusPedidos():Json
    {
        $data_orders = json_decode(filter_input(INPUT_POST,'orders'), true);

        if (!is_array($data_orders)) {
            echo json_encode(["code"=> 404, "message"=>"Dados comprometidos"]);
            exit;
        }
        $pedidos = [];

        foreach ($data_orders as $pedido) {
            $order_id = filter_var($pedido['order_id'], FILTER_SANITIZE_NUMBER_INT);
            $status = filter_var($pedido['status'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if (!isset($pedidos[$status])) {
                $pedidos[$status] = [];
            }
            $pedidos[$status][] = $order_id;
        }

        try {
            DbController::getConnection();

            $cases = [];
            $ids = [];

            foreach ($pedidos as $status => $order_ids) {
                foreach ($order_ids as $id) {
                    $id = (int)$id;
                    $cases[] = "WHEN $id THEN '$status'";
                    $ids[] = $id;
                }
            }

            $caseSql = implode("\n", $cases);
            $idList = implode(',', $ids);
            $sql = "
                UPDATE orders
                SET status = CASE id_orders
                    $caseSql
                END
                WHERE id_orders IN ($idList)
            ";
            DbController::getPdo()->exec($sql);
            echo json_encode(["code" => 200, "message" => count($pedidos) . " Pedidos atualizados."]);
            exit;
        } catch (PDOException | ordersFinishException $e) {
            http_response_code(404);
            echo $e->getMessage();
            throw new exceptionCustom("Erro ao atualizar status do pedido: ",404,$e);
        }
    }
}