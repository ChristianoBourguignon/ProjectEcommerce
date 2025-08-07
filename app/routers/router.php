<?php
namespace App\exceptions;

use App\controllers\Logger;

/**
 * @throws exceptionCustom
 */
function load(string $controller, string $action ,mixed ...$params):mixed
{
    try {
        // se controller existe
        $controllerNamespace = "App\\controllers\\{$controller}";

        if (!class_exists($controllerNamespace)) {
            throw new controllerException("O controller {$controller} não existe");
        }

        $controllerInstance = new $controllerNamespace();

        if (!method_exists($controllerInstance, $action)) {
            throw new controllerException("O método {$action} não existe no controller {$controller}");
        }
        if (!empty($params)) {
            return $controllerInstance->$action(...$params);
        } else {
            return $controllerInstance->$action((object) $_REQUEST);
        }
    } catch (controllerException $e) {
        Logger::error($e->getMessage(),404,$e);
        throw new exceptionCustom("Erro ao acessar um controller: ",404,$e);
    }
}

$router = [
    "GET" =>[
        "/" => function () {
            return load("HomeController", "index");
        },
        "/produtos" => function () {
            return load("ProductsController", "index");
        },
        "/deslogar" => function () {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            if(isset($_SESSION['username'])){
                return load("AuthController", "deslogar");
            } else {
                return load("HomeController", "notFound");
            }
        },
        "/meus-pedidos" => function () {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if(isset($_SESSION['username'])){
                return load("OrdersController",  "index");
            } else {
                return load("HomeController", "notFound");
            }
        },
        "/cupons" => function () {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if(isset($_SESSION['username'])){
                return load("CuponsController", "index");
            } else {
                return load("HomeController", "notFound");
            }
        },
        "/sobre" => function () {
            return load("HomeController", "sobre");
        },
        "/finalizar-compra" => function (){
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            if(isset($_SESSION['username'])){
                return load("OrdersController", "indexCheckout");
            } else {
                return load("HomeController", "notFound");
            }
        }
    ],
    "POST" => [
        "/logar" => function () {
            return load("AuthController", "logar");
        },
        "/criarConta" => function (){
            return load("AuthController","criarConta");
        },
        "/criarProduto" => function (){
            return load("ProductsController", "criarProduto");
        },
        "/excluirProduto" => function(){
            return load("ProductsController", "excluirProduto");
        },
        "/finalizarCompra" => function(){
            return load("OrdersController","finalizarCompra");
        },
        "/getItemsPedido" => function () {
            return load("OrdersController", "getItemsPedido");
        },
        "/atualizarStatusPedidos" => function (){
            return load("OrdersController","atualizarStatusPedidos");
        },
        "/salvarCupom" => function(){
            return load("CuponsController", "criarCupom");
        },
        "/alterarProduto" => function(){
            return load("ProductsController", "alterarProduto");
        }
    ],
];

