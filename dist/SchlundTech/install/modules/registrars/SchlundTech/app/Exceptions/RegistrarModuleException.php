<?php

namespace App\Exceptions;

use Exception;

class RegistrarModuleException extends Exception {
    public function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
    }

    public function __toString()
    {
    return "[" . $this->getCode() . "]: " . $this->getMessage();
    }
}