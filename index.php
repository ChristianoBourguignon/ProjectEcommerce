<?php
namespace app\exceptions;

use app\controllers\DbController;
use Dotenv\Dotenv;
use App\exceptions\exceptionCustom;

require_once "vendor/autoload.php";
require_once "App/routers/router.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$dotenv = Dotenv::createImmutable(dirname(__FILE__));
$dotenv->load();

try {
    $uri = parse_url($_SERVER["REQUEST_URI"])["path"];
    $request = $_SERVER["REQUEST_METHOD"];

    if (!isset($router[$request])) {
        throw new routersExceptions("A rota não existe");
    }

    if (!array_key_exists($uri, $router[$request])) {
        throw new routersExceptions("A rota não existe");
    }

    $controller = $router[$request][$uri];
    $controller();
} catch (routersExceptions $e) {
    throw new exceptionCustom("Erro ao acessar a rota: ",404, $e);
}

