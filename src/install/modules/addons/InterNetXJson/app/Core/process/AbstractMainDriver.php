<?php

namespace MGModule\InterNetX\Addon\Core\process;

use MGModule\InterNetX\Addon\Core\exceptions\System;
use MGModule\InterNetX\Addon\Core\extensions\Smarty;


abstract class AbstractMainDriver
{
    /**
     * Single Ton Instance
     *
     * @var mainController
     */
    private static $_instance;
    private static $type;

    /**
     * This Var define Debug Mode in Module
     *
     * @var boolean
     */
    public $_debug = false;

    /**
     * Mark
     *
     * $var boolean
     */
    public $isLoaded;

    /**
     * Load Configuration
     *
     * @var Configuration
     */
    private $_configuration;

    /**
     * Is Loaded From Admin Area
     *
     * @var boolean
     */
    private $_isAdmin = false;

    /**
     * Key For Data Encryption
     *
     * @var string
     */
    private $encryptSecureKey;

    /**
     * Main Namespace
     *
     * @var string
     */
    private $_mainNamespace;
    private $_mainDIR;

    public function __construct($type)
    {
        self::$type = $type;
    }

    private function __clone()
    {
        ;
    }

    /**
     * Get SingleTon Instance
     *
     * @return AbstractMainDriver
     */
    public static function I($force = false, $configs = array())
    {
        if (empty(self::$_instance) || $force) {
            $class = get_called_class();
            MainInstance::setInstanceName($class);
            self::$_instance = new $class();

            list($moduleNameSpace, $moduleName) = explode("\\", $class);

            self::$_instance->_mainNamespace = $moduleNameSpace."\\".$moduleName."\\".self::$type;
            self::$_instance->_mainDIR = call_user_func(array($class, 'getMainDIR'));
            $class = self::$_instance->_mainNamespace.'\Configuration';

            self::$_instance->_configuration = new $class();

            foreach ($configs as $name => $value) {
                self::$_instance->_configuration->{$name} = $value;
            }

            self::$_instance->isLoaded = true;

            if (!empty(self::$_instance->_configuration->debug)) {
                self::$_instance->_debug = true;
            }
        }

        return self::$_instance;
    }

    /**
     *
     * @return main\Configuration
     */
    public function configuration()
    {
        return $this->_configuration;
    }

    public function isAdmin($status = null)
    {
        if ($status !== null) {
            $this->_isAdmin = $status;
        }
        return $this->_isAdmin;
    }

    public function isDebug()
    {
        return $this->_debug;
    }

    /**
     * Return Main Namespace
     *
     * @return string
     */
    function getMainNamespace()
    {
        return $this->_mainNamespace;
    }

    /**
     * Return Enrypt Key
     *
     * @return string
     */
    public function getEncryptKey()
    {

        if (empty($this->encryptSecureKey)) {
            $this->encryptSecureKey = hash('sha256', $GLOBALS['cc_encryption_hash'].$this->configuration()->encryptHash, TRUE);
        }

        return $this->encryptSecureKey;
    }

    /**
     * Process Controllers
     *
     * @param string $controller controller name
     * @param array $input input array
     * @param string $type type of request
     * @return array
     * @throws main\mgLibs\exceptions\System
     * @throws main\mgLibs\exceptions\System
     */
    function runControler($controller, $action = 'index', $input = [], $type = 'HTML', $vars = [])
    {
        try {
            $className = $this->getMainNamespace()."\\Controllers\\".($this->_isAdmin ? 'Admin' : 'ClientArea')."\\".$controller;
            if (!class_exists($className)) {
                throw new System("Unable to find page");
            }
            $controllerOBJ = new $className($input);
            // display the page or not
            if (method_exists($controllerOBJ, "isActive") && !$controllerOBJ->{"isActive"}()) throw new System("No access to this page");

            if (!method_exists($controllerOBJ, $action.$type)) {
                throw new System("Unable to find Action: ".$action.$type);
            }

            Smarty::I()->setTemplateDir(self::I()->getModuleTemplatesDir().DS.'pages'.DS.lcfirst($controller));
            $result = $controllerOBJ->{$action.$type}($input, $vars);
            switch ($type) {
                case 'HTML':
                    if (empty($result['tpl'])) {
                        throw new System("Provide Template Name");
                    }

                    $success = isset($result['vars']['success']) ? $result['vars']['success'] : false;
                    $error = isset($result['vars']['error']) ? $result['vars']['error'] : false;
                    $result = Smarty::I()->view($result['tpl'], $result['vars']);
                    break;
                default:
                    $success = isset($result['success']) ? $result['success'] : false;
                    $error = isset($result['error']) ? $result['error'] : false;
            }

            return array(
                $result
                , $success
                , $error
            );
        } catch (\Exception $ex) {
            throw $ex;
            return false;
        }
    }

    /**
     * Dump data
     *
     * @param mixed $input
     */
    static function dump($input)
    {
        if (self::I()->isDebug()) {
            echo "<pre>";
            print_r($input);
            echo "</pre>";
        }
    }

    abstract public function getAssetsURL();

    public static function getMainDIR()
    {
        return false;
    }

    public static function getUrl()
    {
        return false;
    }

    public function isActive()
    {
        return true;
    }
}
