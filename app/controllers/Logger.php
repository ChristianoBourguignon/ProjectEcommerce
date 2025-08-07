<?php

namespace App\controllers;

use Throwable;

class Logger {
    public static function error(string $message = "", int $code = 0, Throwable $previous = null): void
    {
        if (!is_dir("./logs")) {
            mkdir("./logs", 0755, true);
        }
        date_default_timezone_set('America/Sao_Paulo');
        error_log("\n".date("Y-m-d H:i:s")."- ". $code . " - ". $message ." ". PHP_EOL ." ". $previous,
            3, "./logs/log-".date("Y-m-d").".log");
    }
}