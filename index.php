<?php
namespace app\Exceptions;

require_once "vendor/autoload.php";
require_once "app/Routers/router.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $uri = parse_url($_SERVER["REQUEST_URI"])["path"];
    $request = $_SERVER["REQUEST_METHOD"];

    if (!isset($router[$request])) {
        throw new RoutersExceptions("A rota não existe");
    }

    if (!array_key_exists($uri, $router[$request])) {
        throw new RoutersExceptions("A rota não existe");
    }

    $controller = $router[$request][$uri];
    $controller();
} catch (RoutersExceptions $e) {
    throw new ExceptionCustom("Erro ao acessar a rota: ",404, $e);
}
?>
<h1>Teste</h1>
