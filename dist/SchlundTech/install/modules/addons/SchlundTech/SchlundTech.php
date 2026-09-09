<?php

use MGModule\InterNetX\Addon\Core\App;

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

require_once __DIR__.DS.'Loader.php';
new MGModule\InterNetX\Loader();


function InterNetX_config()
{
    return App::config();
    //return MGModule\InterNetX\Addon\Core\App::config();
}


function InterNetX_activate()
{
    return MGModule\InterNetX\Addon\Core\App::activate();
}



function InterNetX_deactivate()
{
    return MGModule\InterNetX\Addon\Core\App::deactivate();
}


function InterNetX_upgrade(/*$vars*/)
{
    return MGModule\InterNetX\Addon\Core\App::upgrade();
}


function InterNetX_output($vars)
{
    MGModule\InterNetX\Addon\Core\App::I(false, $vars);
    if (!empty($_REQUEST['json'])) {
        ob_clean();
        header('Content-Type: text/plain');
        echo MGModule\InterNetX\Addon\Core\App::getJSONAdminPage($_REQUEST, $vars);
        die();
    }

    if (!empty($_REQUEST['customPage'])) {
        ob_clean();
        echo MGModule\InterNetX\Addon\Core\App::getHTMLAdminCustomPage($_REQUEST, $vars);
        die();
    }
    echo MGModule\InterNetX\Addon\Core\App::getHTMLAdminPage($_REQUEST, $vars);
}


function InterNetX_clientarea($vars)
{
    if (!empty($_REQUEST['json'])) {
        ob_clean();
        header('Content-Type: text/plain');
        echo \MGModule\InterNetX\Addon\Core\App::getJSONClientAreaPage($_REQUEST, $vars);
        die();
    }

    return \MGModule\InterNetX\Addon\Core\App::getHTMLClientAreaPage($_REQUEST, $vars);
}
