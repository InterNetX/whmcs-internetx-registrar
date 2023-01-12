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

/**
 * AbstractConfiguration
 * @author Michal Czech <michael@modulesgarden.com>
 * @edited Dariusz Bijoś <dariusz.bi@modulesgarden.com>
 */
abstract class AbstractConfiguration
{
    public $debug           = false;
    public $systemName      = false;
    public $name            = false;
    public $description     = false;
    public $clientareaName  = false;
    public $encryptHash     = false;
    public $version         = false;
    public $author          = '<a href="http://www.modulesgarden.com" targer="_blank">ModulesGarden</a>';
    public $tablePrefix     = false;
    private $_customConfigs = array();

    public function __isset($name)
    {
        return isset($this->_customConfigs[$name]);
    }

    public function __get($name)
    {
        if (isset($this->_customConfigs[$name])) {
            return $this->_customConfigs[$name];
        }
    }

    public function __set($name, $value)
    {
        $this->_customConfigs[$name] = $value;
    }

    public function getAddonMenu()
    {
        return array();
    }

    public function getAddonWHMCSConfig()
    {
        return array();
    }

    public function getServerConfigController()
    {
        return 'configuration';
    }

    public function getServerActionsController()
    {
        return 'actions';
    }

    public function getServerCAController()
    {
        return 'home';
    }

    public function getAddonAdminController()
    {
        return 'actions';
    }

    public function getAddonCAController()
    {
        return 'home';
    }
}