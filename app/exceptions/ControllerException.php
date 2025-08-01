<?php
namespace App\exceptions;

use RuntimeException;

class controllerException extends RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}

