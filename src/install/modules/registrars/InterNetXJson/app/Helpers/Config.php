<?php

namespace App\Helpers;

class Config {
    private static $moduleName = 'InterNetXJson';
    private static $premiumAddonName = 'InterNetX';
    private static $registrarNameserver = 'ns14.net';
    private static $templateDir = __DIR__ .'/../templates/';

    public static function moduleName(){
        return self::$moduleName;
    }

    public static function premiumAddonName(){
        return self::$premiumAddonName;
    }

    public static function registrarNameserver(){
        return self::$registrarNameserver;
    }

    public static function templateDir(){
        return self::$templateDir;
    }
}