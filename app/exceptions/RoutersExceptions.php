<?php

namespace App\exceptions;
use RuntimeException;

class routersExceptions extends RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}