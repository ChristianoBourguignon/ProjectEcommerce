<?php

namespace App\exceptions;

use http\Exception\RuntimeException;

class invalidArgumentsForProductsException extends RuntimeException
{

    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}