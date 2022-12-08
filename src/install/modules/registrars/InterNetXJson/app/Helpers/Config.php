<?php

namespace App\Helpers;

class Config {
    private static $moduleName          = 'InterNetXJson';
    private static $userAgent          = 'WHMCS Module';
    private static $premiumAddonName    = 'InterNetX';
    private static $registrarNameserver = 'demo.autodns2.de';
    private static $templateDir         = __DIR__ .'/../templates/';

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

    public static function userAgent(){
        return self::$userAgent;
    }
}