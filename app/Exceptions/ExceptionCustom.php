<?php
namespace App\Exceptions;

use Exception;
use Throwable;

class ExceptionCustom extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        error_log(date("Y-m-d")."- ". $code . " - ". $message ." ". PHP_EOL ." ". $previous,
            3, dirname(__DIR__,1). "/logs/log-".date("Y-m-d").".log");
        parent::__construct($message, $code, $previous);
    }
}

