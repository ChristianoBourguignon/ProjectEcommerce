<?php
namespace App\Exceptions;

use Exception;
use Throwable;

class exceptionCustom extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        if (!is_dir("./logs")) {
            date_default_timezone_set('America/Sao_Paulo');
            mkdir("./logs", 0755, true);
        }
        error_log("\n".date("Y-m-d H:i:s")."- ". $code . " - ". $message ." ". PHP_EOL ." ". $previous,
            3, "./logs/log-".date("Y-m-d").".log");
        parent::__construct($message, $code, $previous);
    }
}

