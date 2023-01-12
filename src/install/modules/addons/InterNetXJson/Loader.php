<?php
/*
 *  * ******************************************************************
 * 
 *    CREATED BY MODULESGARDEN       ->        http://modulesgarden.com
 *    AUTHOR                         ->        Dariusz Bijoś <dariusz.bi@modulesgarden.com>
 *    CONTACT                        ->        contact@modulesgarden.com
 * 
 *   This software is furnished under a license and may be used and copied
 *   only  in  accordance  with  the  terms  of such  license and with the
 *   inclusion of the above copyright notice.  This software  or any other
 *   copies thereof may not be provided or otherwise made available to any
 *   other person.  No title to and  ownership of the  software is  hereby
 *   transferred.
 * 
 *  * ******************************************************************
 */

namespace MGModule\InterNetX;

if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

/**
 * Application Loader
 *
 * @author Dariusz Bijoś <dariusz.bi@modulesgarden.com>
 */
if (!class_exists(__NAMESPACE__.'\Loader')) {

    class Loader
    {
        public static $whmcsDir;
        public static $myName;
        public static $avaiableDirs = array();
        /**
         * Set Paths
         *
         * @param string $dir
         */
        public function __construct($dir = null)
        {
            if (empty($dir)) {
                $checkDirs    = [
                    'modules'.DS.'addons'.DS,
                    //'modules'.DS.'servers'.DS
                ];
                self::$myName = substr(__NAMESPACE__, 9);
                foreach ($checkDirs as $dir) {
                    if ($pos = strpos(__DIR__, $dir.self::$myName)) {
                        self::$whmcsDir = substr(__DIR__, 0, $pos);
                        break;
                    }
                }
                if (self::$whmcsDir) {
                    foreach ($checkDirs as $dir) {
                        $tmp = self::$whmcsDir.$dir.self::$myName;
                        if (file_exists($tmp)) {
                            self::$avaiableDirs[] = $tmp.DS;
                        }
                    }
                }
            } else {
                self::$mainDir = $dir;
            }
            //Run Composer Autoloaders
            if(is_array(self::$avaiableDirs)){
                foreach(self::$avaiableDirs as $avaiableDir){
                    if(file_exists($avaiableDir.DS.'vendor'.DS.'autoload.php')){
                        require_once $avaiableDir.DS.'vendor'.DS.'autoload.php';
                    }
                }
            }
        }
    }
}