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

        $formDataRaw = filter_input(INPUT_POST, 'formCheckout');
        if (!is_string($formDataRaw)) {
            throw new exceptionCustom("Erro ao receber o JSON bruto", 400, new ordersFinishException("Dados ausentes ou inválidos"));
        }
        /**
         * @var array{
         *     zipcode: string,
         *     street: string,
         *     number: string,
         *     complement: string,
         *     neighborhood: string,
         *     city: string,
         *     state: string,
         *     frete: string,
         *     total: string,
         *     cupom?: string,
         *     cart: array<array{
         *         id: int,
         *         name: string,
         *         price: float,
         *         stock: int,
         *         image: string,
         *         max_estoque: int
         *     }>
         * } $formData
         */

        $formData = json_decode($formDataRaw, true);


        try {
            $ids = array_map('intval', array_column($formData['cart'], 'id'));

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
                /** @var array{id_products: int,name: string,price: float,quantity: int,stock: int,image: string ,max_estoque: int} $p */
                $productHashMap[$p['id_products']] = $p;
            }
            $total = 0;
            $errors = [];

            foreach ($formData['cart'] as $item) {
                /** @var array{
                 * id: int,
                 * name: string,
                 * price: float,
                 * stock: int,
                 * image: string,
                 * max_estoque: int
                 * } $item
                 */
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
            echo json_encode(["code"=>$ex->getCode(),"message"=>$ex->getMessage()]);
            throw new exceptionCustom("Erro ao obter os ids: ", 404,$ex);
        }
    }

    /**
     * @param array{
     *     zipcode: string,
     *     street: string,
     *     number: string,
     *     complement: string,
     *     neighborhood: string,
     *     city: string,
     *     state: string,
     *     frete: float,
     *     total: string,
     *     cupom?: string,
     *     cart: array<array{
     *         id: int,
     *         name: string,
     *         price: float,
     *         stock: int,
     *         image: string,
     *         max_estoque: int
     *     }>
     * } $formData
     * @throws exceptionCustom
     */
    function criarPedido(array $formData, float $total): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $userId = $_SESSION['userid'] ?? null;
        $cupomId = null;
        $orderDate = date('Y-m-d H:i:s');
        $frete = $formData['frete'];
        $zipcode = $formData['zipcode'];
        $street = $formData['street'];
        $number = $formData['number'];
        $complement = $formData['complement'];
        $neighborhood = $formData['neighborhood'];
        $cupom = $formData['cupom'] ?? '';
        $city = $formData['city'];
        $state = $formData['state'];
        if(empty($userId)){
            throw new ordersFinishException("Usuário não encontrado.");
        }
        try {
            DbController::getPdo()->beginTransaction();
            if(empty($zipcode) || empty($street) || empty($city) || empty($state)){
                echo json_encode(["code"=>404,"message"=>"Erro ao obter o endereço do cliente para criar o pedido"]);
                throw new ordersFinishException("Não obtido dados do endereço do cliente.");
            }

            $cupomId = CuponsController::obterIdCupom($cupom);
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
            echo json_encode(["code"=>$e->getCode(),"message"=>"Erro ao criar o pedido: " . $e->getMessage()]);
            Logger::error($e->getMessage(),404,$e);
            exit;
        } catch (Exception $e) {
            DbController::getPdo()->rollBack();
            echo json_encode(["code"=>$e->getCode(),"message"=>"Erro no pedido: ".$e->getMessage()]);
            Logger::error($e->getMessage(),404,$e);
            exit;
        }
    }

    /**
     * @param array<array{
     *     id: int,
     *     name: string,
     *     price: float,
     *     stock: int,
     *     image: string,
     *     max_estoque: int
     * }> $cart
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
                /* @var array{id_stock: int, quantity: int} $stock */
                $stock = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$stock || !is_array($stock)) {
                    throw new ordersFinishException("Não foi possível descontar o estoque do produto $product_id.");
                }
                /* @var int $estoqueAtual */
                $estoqueAtual = $stock['quantity'];
                $id_stock = $stock['id_stock'];
                if ($estoqueAtual < $quantity) {
                    if(is_scalar($estoqueAtual)) {
                        throw new ordersFinishException("Estoque insuficiente para o produto ID $product_id. Em estoque: $estoqueAtual, solicitado: $quantity");
                    } else {
                        throw new ordersFinishException("Estoque insuficiente para realizar o pedido");
                    }
                }
                $stmtUpdate = DbController::getPdo()->prepare("UPDATE stock SET quantity = quantity - ?, update_in = NOW() WHERE id_stock = ?");
                $stmtUpdate->execute([$quantity, $id_stock]);
            }
        } catch (ordersFinishException $ex){
            echo json_encode(["code"=>$ex->getCode(),"message"=>"Erro ao criar o pedido"]);
            Logger::error($ex->getMessage(),404,$ex);
            exit;
        } catch (PDOException $ex){
            echo json_encode(["code"=>$ex->getCode(),"message"=>"Erro ao criar o pedido"]);
            throw new exceptionCustom("Erro ao criar o pedido: ",404,$ex);
        }
    }

    /**
     * @return array<int, array{order_id:int, order_status:string, order_date:string, total_price:float}>
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
            /* @var array<int, array{order_id:int, order_status:string, order_date:string, total_price:float}> $pedidos */
            $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $orders = [];
            foreach ($pedidos as $order) {
                /** @var array{order_id:string, order_status:string, order_date:string, total_price:string} $order */
                $orders[] = [
                    'order_id' => (int)$order['order_id'],
                    'order_status' => (string)$order['order_status'],
                    'order_date' => (string)$order['order_date'],
                    'total_price' => (float)$order['total_price'],
                ];
            }
            return $orders;
    }  catch (PDOException $e){
            Logger::error($e->getMessage(),404,$e);
            throw new exceptionCustom("Erro na conexão: ", 400,$e);
        } catch (ordersFinishException $ex){
            return [];
        }
    }

    /**
     * @return array<int, array{
     * order_id:int,
     * order_status:string,
     * order_date:string,
     * total_price:float,
     * shipping_price:float,
     * item_id:int,
     * product_id:int,
     * item_quantity:int,
     * item_unit_price:float,
     * product_name:string,
     * product_image:string
     * }>
     * @throws exceptionCustom
     */
    static function getItemsPedido(): array
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
            $items = [];

            foreach ($result as $item) {
                /** @var array<string, string> $item */
                $items[] = [
                    'order_id' => (int)$item['order_id'],
                    'order_status' => (string)$item['order_status'],
                    'order_date' => (string)$item['order_date'],
                    'total_price' => (float)$item['total_price'],
                    'shipping_price' => (float)$item['shipping_price'],
                    'item_id' => (int)$item['item_id'],
                    'product_id' => (int)$item['product_id'],
                    'item_quantity' => (int)$item['item_quantity'],
                    'item_unit_price' => (float)$item['item_unit_price'],
                    'product_name' => (string)$item['product_name'],
                    'product_image' => (string)$item['product_image'],
                ];
            }
            http_response_code(200);
            echo json_encode(["code"=>200,"products"=>$items]);
            return $items;
        } catch (exceptionCustom $e) {
            http_response_code(404);
            echo json_encode(["code"=>$e->getCode(),"message"=>"Erro ao obter o produto do pedido: " . $e->getMessage()]);
            throw new exceptionCustom("Erro ao obter o produto: ", 404, $e);
        } catch (PDOException $e){
            Logger::error($e->getMessage(),404,$e);
            throw new exceptionCustom("Erro na conexão: ", 400,$e);
        }
    }

    /**
     * @throws exceptionCustom
     */
    static function atualizarStatusPedidos():void
    {
        $json = filter_input(INPUT_POST, 'orders');

        if (!is_string($json)) {
            echo json_encode(["code"=> 404, "message"=>"Dados ausentes ou inválidos"]);
            exit;
        }

        $data_orders = json_decode($json, true);

        if (!is_array($data_orders)) {
            echo json_encode(["code"=> 404, "message"=>"Dados comprometidos"]);
            exit;
        }
        $pedidos = [];

        foreach ($data_orders as $pedido) {
            if (!is_array($pedido) || !isset($pedido['order_id']) || !isset($pedido['status'])) {
                continue;
            }
            /** @var int $order_id */
            $order_id = filter_var($pedido['order_id'], FILTER_SANITIZE_NUMBER_INT);
            /** @var string $status */
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
        } catch (ordersFinishException $e) {
            http_response_code(404);
            echo $e->getMessage();
            throw new exceptionCustom("Erro ao atualizar status do pedido: ",404,$e);
        } catch (PDOException $e){
            Logger::error($e->getMessage(),404,$e);
            throw new exceptionCustom("Erro na conexão: ", 400,$e);
        }
    }
}