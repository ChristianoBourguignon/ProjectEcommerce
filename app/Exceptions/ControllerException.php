<?php
namespace app\Exceptions;

use RuntimeException;

class ControllerException extends RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}

