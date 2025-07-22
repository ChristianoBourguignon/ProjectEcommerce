<?php
namespace App\Controllers;

use App\Exceptions\exceptionCustom;
use App\Exceptions\viewsControllerException;
use League\Plates\Engine;

class Controller
{
    /**
     * @param array<string, mixed> $data
     * @throws exceptionCustom
     */
    public static function view(string $view, array $data = []): void
    {
        try {
            $viewsPath = dirname(__DIR__) . "/Views";

            if (!file_exists($viewsPath . DIRECTORY_SEPARATOR . $view . ".php")) {
                throw new viewsControllerException("A view {$view} não existe");
            }

            $templates = new Engine($viewsPath);
            echo $templates->render($view, $data);
        } catch (viewsControllerException $e) {
            throw new exceptionCustom("Erro ao acessar a view: ",404,$e);

        }
    }
}