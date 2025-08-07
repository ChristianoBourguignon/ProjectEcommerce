<?php
namespace App\controllers;

use App\exceptions\exceptionCustom;
use App\exceptions\viewsControllerException;
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
            $viewsPath = dirname(__DIR__) . "/views";

            if (!file_exists($viewsPath . DIRECTORY_SEPARATOR . $view . ".php")) {
                throw new viewsControllerException("A view {$view} não existe");
            }

            $templates = new Engine($viewsPath);
            echo $templates->render($view, $data);
        } catch (viewsControllerException $e) {
            Logger::error($e->getMessage(),404,$e);
            throw new exceptionCustom("Erro ao acessar a view: ",404,$e);
        }
    }
}