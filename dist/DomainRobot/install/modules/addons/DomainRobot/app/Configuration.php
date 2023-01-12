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
namespace MGModule\InterNetX\Addon;

use MGModule\InterNetX as main;
use Illuminate\Database\Capsule\Manager as Capsule;
use MGModule\InterNetX\Addon\Core\process\AbstractConfiguration;

/**
 * Application Configuration
 * @author Michal Czech <michael@modulesgarden.com>
 * @edited Dariusz Bijoś <dariusz.bi@modulesgarden.com>
 */
class Configuration extends AbstractConfiguration
{
    public $debug = false;
    public $systemName = 'InterNetX';
    public $name = 'InterNetX Premium Domains Addon';
    public $description = 'InterNetX Premium Domains Addon allows to set custom pricing to all pricing classes.';
    public $clientareaName = 'InterNetX Client Area';
    public $encryptHash = 'uUc1Y8cWxDOAzlq11lBwelqzo6PGMTA0dbHaKQ109psefoJgIFMOgmReKCZbpCYpDSnrtfjmCIUyplaBJaUh40auDALprOHtj1g92ZRBS6S94IbZWaeZRYkG1f81h6qLMYEOr016RurCnmodFCWdMkTqrlVBvH249gzXPduKQVXpN9hooComaRPY5jZD6s8GdfR5E_BNP3v8Ui8RrdqMPST_8quMW48LhHY88xCvSWwDNjkC2tCAaK67Id2NjzIdoNTHUMISRg81nHX8ZGcbP74mxixo_ASd8YoWnDCAs8yiT4t0PwKRO_y3C1kDo69Nxz1YYt4tY1VzOD_DFBulAA5NCJLfogroo';
    public $version = '1.0.2';
    public $author = '<a href="http://www.modulesgarden.com" targer="_blank">ModulesGarden</a>';
    public $tablePrefix = 'mgint_';
    public $language = "english";

    public function __construct()
    {
    }

    /**
     * Application Menu in Admin Area
     * @author Michal Czech <michael@modulesgarden.com>
     * @since 1.0.0
     * @return array
     */
    public function getAddonMenu()
    {
        return [
            'home' => [
                'icon' => 'glyphicon glyphicon-home',
                'breadcrumb' => 'Home',
                'buttons' => [],
                'formAdvanced' => [],
                'icons' => [],
                'panels' => [],
                'tableAjax' => [],
                'tableResponsive' => [],
                'tables' => [],
                'typography' => []
            ]
        ];
    }

    /**
     * Application Menu in Client Area
     * @author Michal Czech <michael@modulesgarden.com>
     * @since 1.0.0
     * @return array
     */
    public function getClienMenu()
    {
        return [
            'home' => ['icon' => 'glyphicon glyphicon-home']
        ];
    }

    /**
     * Application Server Menu
     * @author Dariusz Bijoś <dariusz.bi@modulesgarden.com>
     * @since 1.0.0
     * @return array
     */
    public function getServerMenu()
    {
        return ['configuration' => ['icon' => 'glyphicon glyphicon-cog']];
    }

    /**
     * Return names of WHMCS product config fields
     * required if you want to use default WHMCS product configuration
     * @author Michal Czech <michael@modulesgarden.com>
     * @since 1.0.0
     * @max 20 fields
     * @return array
     */
    public function getServerWHMCSConfig()
    {
        return [];
    }

    /**
     * Addon module configuration visible in admin area. This is standard WHMCS configuration
     * @author Michal Czech <michael@modulesgarden.com>
     * @since 1.0.0
     * @return array
     */
    public function getAddonWHMCSConfig()
    {
        return [];
    }

    /**
     * Run When Module Install
     * @author Michal Czech <michael@modulesgarden.com>
     * @since 1.0.0
     * @return array
     */
    public function activate()
    {
        \MGModule\InterNetX\Addon\Models\Migrations\CreateInterNetXPremiumDomiansClassesTable::up();
        \MGModule\InterNetX\Addon\Models\Migrations\CreateInterNetXPremiumDomiansPricingTable::up();
        Capsule::table('tbldomainpricing_premium')->update(['markup' => 0]);

        return [
            'status' => 'success',
            'description' => 'The selected addon module was activated successfully. You may now configure it below.'
        ];
    }

    /**
     * Do something after module deactivate. You can status and description
     * @author Michal Czech <michael@modulesgarden.com>
     * @since 1.0.0
     * @return array
     */
    public function deactivate()
    {
        //\MGModule\InterNetX\Addon\Models\Migrations\CreateInterNetXPremiumDomiansClassesTable::down();
        //\MGModule\InterNetX\Addon\Models\Migrations\CreateInterNetXPremiumDomiansPricingTable::down();
        
        return [
            'status' => 'success',
            'description' => 'The selected addon module was deactivated successfully.'
        ];
    }

    /**
     * Do something after module upgrade
     * @author Michal Czech <michael@modulesgarden.com>
     * @since 1.0.0
     * @param array $vars
     */
    public function upgrade($vars)
    {
        //$version = $vars['version'];
    }
}
