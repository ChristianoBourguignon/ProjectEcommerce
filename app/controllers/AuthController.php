<?php

namespace App\controllers;

use App\exceptions\exceptionCustom;
use App\exceptions\invalidParametersAuthException;
use Exception;
use PDO;

class AuthController
{

    public function criarConta():void {
        $usercpf = filter_input(INPUT_POST, 'registerCpf', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? null;
        $username = filter_input(INPUT_POST, 'registerName', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? null;
        $useremail= filter_input(INPUT_POST, 'registerEmail', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? null;
        if(empty($usercpf) || empty($username) || empty($useremail)){
            throw new invalidParametersAuthException("Dados vazios não são permitidos. por favor refaça a operação.");
        }
        if(is_string($usercpf) && strlen($usercpf) != 11 ){
            throw new invalidParametersAuthException("CPF não pode ser menor que 11 caracteres, por favor digite sem as pontuações.");
        }
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            DbController::getConnection();
            $stmt = DbController::getPdo()->prepare("SELECT cpf FROM users WHERE cpf = :cpf");
            $stmt->bindParam(':cpf', $usercpf, PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if($user){
                throw new invalidParametersAuthException("Já existe conta com esse CPF");
            }
            $stmt = DbController::getPdo()->prepare("INSERT INTO users (name,email,cpf) VALUES (:name,:email,:cpf)");
            $stmt->bindParam(':name', $username);
            $stmt->bindParam(':email', $useremail);
            $stmt->bindParam(':cpf', $usercpf);
            $stmt->execute();
            $userid = (int)DbController::getPdo()->lastInsertId();
            $_SESSION['username'] = $username;
            $_SESSION['userid'] = $userid;
            $this->setCookie("username",$_SESSION['username']);
            $this->setCookie("userid",$_SESSION['userid']);
            http_response_code(200);
            header("location: /produtos");
        }catch (Exception | invalidParametersAuthException $ex){
            throw new exceptionCustom("Erro ao acessar a conta: ",404,$ex);
        }
    }
    /**
     * @throws exceptionCustom
     */
    public function logar(): void
    {
        $usercpf = filter_input(INPUT_POST, 'loginCpf', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? null;

        if(!is_string($usercpf) || empty($usercpf)){
            throw new invalidParametersAuthException("Erro ao obter o cpf");
        }
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            DbController::getConnection();
            $stmt = DbController::getPdo()->prepare("SELECT id_user,name FROM users WHERE cpf = :cpf");
            $stmt->bindParam(':cpf', $usercpf, PDO::PARAM_INT);
            $stmt->execute();
            /** @var array{id_user:int , name:string} $user */
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user == null) {
                throw new InvalidParametersAuthException("Não há uma conta atrelada a esse CPF.");
            }
            $_SESSION['username'] = $user['name'];
            $_SESSION['userid'] = $user['id_user'];
            $this->setCookie("username",$user['name']);
            $this->setCookie("userid",$user['id_user']);
            header("Location: /produtos");
            exit;
        } catch (Exception | invalidParametersAuthException $ex){
            throw new exceptionCustom("Erro ao acessar a conta: ",404,$ex);
        }
    }

    public function deslogar():void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['username']) && is_string($_SESSION['username'])) {
            $this->setCookie("username", $_SESSION['username'], 0);
        }
        if (isset($_SESSION['userid']) && is_int($_SESSION['userid'])) {
            $this->setCookie("userid", $_SESSION['userid'], 0);
        }
        $_SESSION = [];
        session_destroy();
        header("Location: /");
        exit;
    }

    function setCookie(string $name, mixed $value, int $days = 30): void {
        if (!(isset($value) && is_string($value))) { return;}
        setcookie($name, $value, [
            'expires' => time() + (86400 * $days),
            'path' => '/',
        ]);
    }
}