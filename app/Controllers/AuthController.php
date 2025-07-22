<?php

namespace App\Controllers;

use App\Exceptions\exceptionCustom;
use App\Exceptions\httpInvalidException;
use App\Exceptions\usersExceptions;
use Exception;
use http\Exception\InvalidArgumentException;
use PDO;

class AuthController
{
    private string $username;
//    /**
//     * @throws exceptionCustom
//     */
//    public static function login(String $email, String $password):void
//    {
//        DbController::getConnection();
//        if (!$_SERVER['REQUEST_METHOD'] === 'GET') {
//            throw new httpInvalidException("Metódo de HTTP inválido para esse tipo de requisição");
//        }
//            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
//            $senha = filter_input(INPUT_POST,'password');
//
//            if (empty($email) || empty($senha)) {
//                $http_response_header(404);
//                exit;
//            }
//
//            try {
//                dbController::getConnection();
//
//                $stmt = dbController::getPdo()->prepare("SELECT email FROM users WHERE email = :email LIMIT 1");
//                $stmt->bindParam(':email', $email);
//                $stmt->execute();
//
//                $user = $stmt->fetch(PDO::FETCH_ASSOC);
//                if ($user === false) {
//                    throw new usersException("Usuário não encontrado.");
//                }
//                /** @var array{id: int, nome: string, email: string, senha: string}|false $user */
//
//                if ($user && password_verify($senha,$user['senha'])) {
//                    $_SESSION['usuario_id'] = $user['id'];
//                    $_SESSION['usuario_nome'] = $user['nome'];
//
//                    $_SESSION['modal'] = [
//                        'msg' => "Seja bem-vindo ". $user['nome'] . '!',
//                        'statuscode' => 200
//                    ];
//                    header("location:" . BASE . "/dashboard");
//                } else {
//                    $_SESSION['modal'] = [
//                        'msg' =>'Usuario ou senha incorreta',
//                        'statuscode' => 404
//                    ];
//                    header("location:" . BASE);
//                    exit;
//                }
//            } catch (PDOException $e) {
//                throw new PDOException("Erro de conexão: ". $e->getMessage());
//            } catch (Exception $e) {
//                echo "Erro na view: " . $e->getMessage();
//                exit;
//            }
//        } else {
//           //
//        }
//    }

    /**
     * @throws exceptionCustom
     */
    public function logar(): void
    {
        $username = filter_input(INPUT_POST, 'loginName', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? null;
        if(!isset($username)){
            throw new InvalidArgumentException("Erro ao armazenar o nome");
        }
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['username'] = $username;
            setcookie("username", $username, time() + (86400 * 30), "/");
            header("Location: /produtos");
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
        header("Location: /",302);
        exit;
    }

}