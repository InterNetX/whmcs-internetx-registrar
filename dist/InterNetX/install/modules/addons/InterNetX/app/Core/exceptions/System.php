<?php

namespace MGModule\InterNetX\Addon\Core\exceptions;


class System extends Base
{

    
    public function __construct($message, $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}