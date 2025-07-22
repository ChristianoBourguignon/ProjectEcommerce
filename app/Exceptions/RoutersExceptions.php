<?php

namespace App\Exceptions;
use RuntimeException;

class routersExceptions extends RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}