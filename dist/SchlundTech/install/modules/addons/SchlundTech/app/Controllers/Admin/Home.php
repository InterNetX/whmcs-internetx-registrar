<?php

namespace MGModule\InterNetX\Addon\Controllers\Admin;

use MGModule\InterNetX\Addon\Core\extensions\Smarty;
use Illuminate\Database\Capsule\Manager as Capsule;


class Home
{

    public function indexHTML($input, $vars = [])
    {
        return [
            'tpl' => 'index',
            'vars' => $vars,
            'input' => $input
        ];
    }
}
