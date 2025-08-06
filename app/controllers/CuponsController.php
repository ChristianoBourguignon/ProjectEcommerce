<?php

namespace App\controllers;

use App\exceptions\cuponsExceptions;
use App\exceptions\exceptionCustom;

class CuponsController
{
    /**
     * @throws exceptionCustom
     */
    function index(): void
    {
        Controller::view("cupons");
    }

    /**
     * @throws exceptionCustom
     */
//    function criarCupom():void
//    {
//        if (session_status() === PHP_SESSION_NONE) {
//            session_start();
//        }
//
//        try {
//            if(!isset($_SESSION['username'])){
//                echo json_encode(["code"=>404,"Você não tem acesso para realizar essa operação"]);
//                throw new exceptionCustom("Erro ao criar o produto: ",404,new cuponsExceptions("Sem acesso"));
//            }
//
//            DbController::getConnection();
//            foreach ($cart as $item) {
//                $product_id = intval($item['id']);
//                $quantity = intval($item['stock']);
//                $stmt = DbController::getPdo()->prepare("SELECT id_stock, quantity FROM stock WHERE product_id = ?");
//                $stmt->execute([$product_id]);
//                /* @var array{id_stock: int, quantity: int} $stock */
//                $stock = $stmt->fetch(PDO::FETCH_ASSOC);
//                if (!$stock || !is_array($stock)) {
//                    throw new cuponsExceptions("Não foi possível descontar o estoque do produto $product_id.");
//                }
//                /* @var int $estoqueAtual */
//                $estoqueAtual = $stock['quantity'];
//                $id_stock = $stock['id_stock'];
//                if ($estoqueAtual < $quantity) {
//                    if(is_scalar($estoqueAtual)) {
//                        throw new cuponsExceptions("Estoque insuficiente para o produto ID $product_id. Em estoque: $estoqueAtual, solicitado: $quantity");
//                    } else {
//                        throw new cuponsExceptions("Estoque insuficiente para realizar o pedido");
//                    }
//                }
//                $stmtUpdate = DbController::getPdo()->prepare("UPDATE stock SET quantity = quantity - ?, update_in = NOW() WHERE id_stock = ?");
//                $stmtUpdate->execute([$quantity, $id_stock]);
//            }
//        } catch (cuponsExceptions $e) {
//                throw new exceptionCustom("Erro ao criar o cupom: ", 404, $e);
//            }
//    }

}