<?php

namespace App\Exceptions;
use RuntimeException;

class RoutersExceptions extends RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}