<?php

namespace App\controllers;

use App\exceptions\cuponsExceptions;
use App\exceptions\exceptionCustom;
use App\exceptions\invalidParametersAuthException;
use DateTime;
use PDO;
use PDOException;

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
    function criarCupom():void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $json = filter_input(INPUT_POST, 'cupom');

        if (!is_string($json)) {
            http_response_code(404);
            echo json_encode(["code"=> 404, "message"=>"Dados ausentes ou inválidos"]);
            exit;
        }

        $data_cupons = json_decode($json, true);

        if (!is_array($data_cupons)) {
            echo json_encode(["code"=> 404, "message"=>"Dados comprometidos"]);
            exit;
        }
        $expiresAt = DateTime::createFromFormat('Y-m-d', (string)$data_cupons['expires_at']);
        if ($expiresAt === false) {
            throw new cuponsExceptions("Data de validade inválida");
        }
        $expiresAt = $expiresAt->format('Y-m-d');
        $cupom = [
            "code" => (string)($data_cupons["code"] ?? ''),
            "discount_percent" => floatval($data_cupons["discount_percent"] ?? 0.0),
            "discount_value" => floatval($data_cupons["discount_value"] ?? 0.0),
            "min_cart_value" => floatval($data_cupons["min_cart_value"] ?? 0.0),
            "expires_at"=> $expiresAt,
            "active" => boolval($data_cupons['active'] ?? false)
        ];
        $now = new DateTime();
        $now = $now->format('Y-m-d');
        try {
            if(!isset($_SESSION['username'])){
                throw new invalidParametersAuthException("Sem acesso ao método");
            }
            if($cupom['expires_at'] < $now){
                throw new cuponsExceptions("Data de validade, deve ser maior que a data atual");
            }
            DbController::getConnection();
            $stmt = DbController::getPdo()->prepare("
                INSERT INTO 
                cupons (code, discount_percent, discount_value,min_cart_value,expires_at,active) 
                values (?,?,?,?,?,?)
            ");
            $stmt->execute([$cupom["code"],$cupom["discount_percent"],$cupom['discount_value'],$cupom["min_cart_value"],$cupom["expires_at"],$cupom["active"]]);
            http_response_code(200);
            echo json_encode(["code"=>200,"message"=>"Cupom criado com sucesso"]);
            exit;
        } catch (cuponsExceptions $e) {
            http_response_code(404);
            echo json_encode(["code"=>404,"message"=>"Erro ao criar o cupom: " . $e->getMessage()]);
            Logger::error($e->getMessage(),404,$e);
        } catch (invalidParametersAuthException $e){
            http_response_code(401);
            echo json_encode(["code"=>401,"Erro ao acessar o método: ". $e->getMessage()]);
            Logger::error($e->getMessage(),401,$e);
            throw new exceptionCustom("Erro ao acessar o método: ", 401, $e);
        }
    }

    /**
     * @throws exceptionCustom
     * @return array<int,array{id: int,code: string, discount_percent: float, discount_value: float, min_cart_value: float, expires_at: string,active:bool,created_at: string}>
     */
    static function obterCupons(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $now = new DateTime();
        $now = $now->format('Y-m-d');
        try {
            if(!isset($_SESSION['username'])){
                throw new invalidParametersAuthException("Sem acesso ao método");
            }
            DbController::getConnection();
            $stmt = DbController::getPdo()->prepare("
                SELECT * FROM cupons
                WHERE expires_at < NOW() AND active = true
                order by created_at limit 10
            ");
            $stmt->execute();
            /* @var array<int,array{id: int,code: string, discount_percent: float, discount_value: float, min_cart_value: float, expires_at: string,active:bool,created_at: string} $cupons*/
            $cupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $cupons;
        } catch (cuponsExceptions $e) {
            http_response_code(404);
            echo json_encode(["code"=>404,"message"=>"Erro ao criar o cupom: " . $e->getMessage()]);
            Logger::error($e->getMessage(),404,$e);
        } catch (invalidParametersAuthException $e){
            http_response_code(401);
            echo json_encode(["code"=>401,"Erro ao acessar o método: ". $e->getMessage()]);
            Logger::error($e->getMessage(),401,$e);
            throw new exceptionCustom("Erro ao acessar o método: ", 401, $e);
        }
        return [];
    }

    /**
     * @throws exceptionCustom
     */
    static function obterIdCupom(string $code): int
    {
        try {
            DbController::getConnection();
            $stmtCupom = DbController::getPdo()->prepare("select id_cupom from cupons where code = ?");
            $stmtCupom->execute([$code]);
            return intval($stmtCupom->fetchColumn());
        } catch (PDOException $e) {
            Logger::error($e->getMessage(),404,$e);
            throw new exceptionCustom("Erro ao conectar no banco de dados: ",404, $e);
        }
    }
}