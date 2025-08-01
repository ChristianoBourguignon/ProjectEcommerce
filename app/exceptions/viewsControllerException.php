<?php
namespace App\exceptions;

use RuntimeException;

class viewsControllerException extends RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}

