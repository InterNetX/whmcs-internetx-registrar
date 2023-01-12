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

namespace MGModule\InterNetX\Addon\Core\process;

use MGModule\InterNetX\Addon\Core\exceptions\System;

/**
 * Description of mainController
 *
 *  @author Michal Czech <michael@modulesgarden.com>
 */
class MainInstance
{
    /**
     *
     * @var abstractMainDriver
     */
    static private $_instanceName;

    public static function setInstanceName($instance)
    {
        self::$_instanceName = $instance;
    }

    public static function __callStatic($name, $arguments)
    {
        return call_user_func(array(self::$_instanceName, $name), $arguments);
    }

    /**
     *
     * @return main\mgLibs\process\AbstractMainDriver
     * @throws exceptions\System
     */
    static function I()
    {
        if (empty(self::$_instanceName)) {
            throw new System('Instance is not set');
        }
        return call_user_func(array(self::$_instanceName, 'I'));
    }
}