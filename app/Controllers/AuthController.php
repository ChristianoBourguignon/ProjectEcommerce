<?php

namespace App\controllers;

use App\exceptions\exceptionCustom;
use App\exceptions\invalidParametersAuthException;
use Exception;
use http\Exception\InvalidArgumentException;

class AuthController
{
    /**
     * @throws exceptionCustom
     */
    public function logar(): void
    {
        $username = filter_input(INPUT_POST, 'loginName', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? null;
        if(!isset($username)){
            throw new invalidParametersAuthException("Erro ao armazenar o nome");
        }
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['username'] = $username;
            setcookie("username", $username, time() + (86400 * 30), "/");
            header("Location: /produtos");
            exit;
        } catch (Exception | InvalidArgumentException $ex){
            throw new exceptionCustom("Erro ao acessar a conta: ",404,$ex);
        }
    }

    public function deslogar():void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        setcookie("username", '', time() - 3600, "/");
        header("location: /");
        exit;
    }

}