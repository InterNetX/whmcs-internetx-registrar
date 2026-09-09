<?php

namespace MGModule\InterNetX\Addon;

use MGModule\InterNetX as main;
use Illuminate\Database\Capsule\Manager as Capsule;
use MGModule\InterNetX\Addon\Core\process\AbstractConfiguration;


class Configuration extends AbstractConfiguration
{
    public $debug = false;
    public $systemName = 'InterNetX';
    public $name = 'InterNetX Premium Domains Addon';
    public $description = 'InterNetX Premium Domains Addon allows to set custom pricing to all pricing classes.';
    public $clientareaName = 'InterNetX Client Area';
    public $encryptHash = 'uUc1Y8cWxDOAzlq11lBwelqzo6PGMTA0dbHaKQ109psefoJgIFMOgmReKCZbpCYpDSnrtfjmCIUyplaBJaUh40auDALprOHtj1g92ZRBS6S94IbZWaeZRYkG1f81h6qLMYEOr016RurCnmodFCWdMkTqrlVBvH249gzXPduKQVXpN9hooComaRPY5jZD6s8GdfR5E_BNP3v8Ui8RrdqMPST_8quMW48LhHY88xCvSWwDNjkC2tCAaK67Id2NjzIdoNTHUMISRg81nHX8ZGcbP74mxixo_ASd8YoWnDCAs8yiT4t0PwKRO_y3C1kDo69Nxz1YYt4tY1VzOD_DFBulAA5NCJLfogroo';
    public $version = '1.0.2';
    public $author = '<a href="https://www.internetx.com" target="_blank">InterNetX GmbH</a>';
    public $tablePrefix = 'mgint_';
    public $language = "english";

    public function __construct()
    {
    }

    
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

    
    public function getClienMenu()
    {
        return [
            'home' => ['icon' => 'glyphicon glyphicon-home']
        ];
    }

    
    public function getServerMenu()
    {
        return ['configuration' => ['icon' => 'glyphicon glyphicon-cog']];
    }

    
    public function getServerWHMCSConfig()
    {
        return [];
    }

    
    public function getAddonWHMCSConfig()
    {
        return [];
    }

    
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

    
    public function deactivate()
    {
        //\MGModule\InterNetX\Addon\Models\Migrations\CreateInterNetXPremiumDomiansClassesTable::down();
        //\MGModule\InterNetX\Addon\Models\Migrations\CreateInterNetXPremiumDomiansPricingTable::down();
        
        return [
            'status' => 'success',
            'description' => 'The selected addon module was deactivated successfully.'
        ];
    }

    
    public function upgrade($vars)
    {
        //$version = $vars['version'];
    }
}
