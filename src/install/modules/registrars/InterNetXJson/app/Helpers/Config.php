<?php

namespace App\Helpers;

class Config {
    private static $moduleName = 'InterNetXJson';
    private static $premiumAddonName = 'InterNetX';

    public static function moduleName(){
        return self::$moduleName;
    }

    public static function premiumAddonName(){
        return self::$premiumAddonName;
    }
}