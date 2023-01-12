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

use MGModule\InterNetX\Addon\Core\App;

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

require_once __DIR__.DS.'Loader.php';
new MGModule\InterNetX\Loader();

/**
 * Addon Configuration
 * @author Dariusz Bijoś <dariusz.bi@modulesgarden.com>
 * @since 1.0.0
 * @return Array
 */
function InterNetX_config()
{
    return App::config();
    //return MGModule\InterNetX\Addon\Core\App::config();
}

/**
 * Addon Activation
 * @author Dariusz Bijoś <dariusz.bi@modulesgarden.com>
 * @since 1.0.0
 * @return Array
 */
function InterNetX_activate()
{
    return MGModule\InterNetX\Addon\Core\App::activate();
}

/**
 * Addon Deactivation
 * @author Dariusz Bijoś <dariusz.bi@modulesgarden.com>
 * @since 1.0.0
 * @return Array
 */

function InterNetX_deactivate()
{
    return MGModule\InterNetX\Addon\Core\App::deactivate();
}

/**
 * Addon Upgrades
 * @author Dariusz Bijoś <dariusz.bi@modulesgarden.com>
 * @since 1.0.0
 * @param array $vars
 */
function InterNetX_upgrade(/*$vars*/)
{
    return MGModule\InterNetX\Addon\Core\App::upgrade();
}

/**
 * Addon AdminArea Content/Output
 * @author Dariusz Bijoś <dariusz.bi@modulesgarden.com>
 * @since 1.0.0
 * @param array $vars
 * @return HTML
 */
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

/**
 * Addon ClientArea Content/Output
 * @author Dariusz Bijoś <dariusz.bi@modulesgarden.com>
 * @since 1.0.0
 * @param array $vars
 * @return HTML
 */
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
