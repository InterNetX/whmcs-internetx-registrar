<?php

namespace App\Helpers;

class Config {
    private static $moduleName = 'InterNetXJson';

    public static function moduleName(){
        return self::$moduleName;
    }
}