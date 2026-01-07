<?php

namespace App\Helpers;

class Config {
    private static $moduleName          = 'SchlundTech';
    private static $userAgent          = 'WHMCS Module';
    private static $premiumAddonName    = 'SchlundTech';
    private static $registrarNameserver = 'schlundtech.de';
    private static $templateDir         = __DIR__ .'/../templates/';
    private static $version             = "1.0.0-pre";


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

    public static function version(){
        return self::$version;
    }

    public static function userAgent(){
        return self::$userAgent;
    }
}