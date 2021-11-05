<?php

namespace App;

use App\Contracts\RegistrarModuleInterface;

use Illuminate\Database\Capsule\Manager as Capsule;
use WHMCS\Domains\DomainLookup\ResultsList;
use WHMCS\Domains\DomainLookup\SearchResult;

class RegistrarModule implements RegistrarModuleInterface {

    public static function getConfigArray(){
        $adminEmails = Capsule::table('tbladmins')->select('email')->orderBy('email', 'asc')->pluck('email')->toArray();

        $configArray = array
            (
            "FriendlyName" => array(
                "Type" => "System",
                "Value" => "InterNetX Json",
            ),
            "Description" => array(
                "Type" => "System",
                "Value" => "Get your free InterNetX Reseller Account here: <a href=\"https://www.internetx.com\" target=\"_blank\">https://www.internetx.com</a>.",
            ),
            "serverUsername" => array
                (
                'FriendlyName' => 'Username',
                "Type" => "text",
                "Size" => "20",
                "Description" => "Enter your username here.",
            ),
            "serverPassword" => array
                (
                'FriendlyName' => 'Password',
                "Type" => "password",
                "Size" => "20",
                "Description" => "Enter your password here.",
            ),
            "serverContext" => array
                (
                'FriendlyName' => 'Context',
                "Type" => "text",
                "Size" => "20",
                "Description" => "Enter your context here.",
            ),
            'serverHost' => array
                (
                'FriendlyName' => 'Server',
                'Type' => 'text',
                'Size' => '20',
                "Description" => "Provide your API url. Eg. https://gateway.autodns.com",
            ),
            'nameServers' => array
                (
                'FriendlyName' => 'Default Nameservers',
                "Type" => "textarea",
                "Rows" => "3",
                "Cols" => "18",
                "Default" => "",
                "Description" => "Provide your Default Nameservers (for Zone) Eg. ns1.demo.autodns2.de",
            ),
            "sandboxServerUsername" => array
                (
                'FriendlyName' => 'Sandbox Username',
                "Type" => "text",
                "Size" => "20",
                "Description" => "Enter your sandbox username here.",
            ),
            "sandboxServerPassword" => array
                (
                'FriendlyName' => 'Sandbox Password',
                "Type" => "password",
                "Size" => "20",
                "Description" => "Enter your sandbox password here.",
            ),
            "sandboxServerContext" => array
                (
                'FriendlyName' => 'Sandbox Context',
                "Type" => "text",
                "Size" => "20",
                "Description" => "Enter your sandbox context here.",
            ),
            'sandboxNameServers' => array
                (
                'FriendlyName' => 'Sandbox Default Nameservers',
                "Type" => "textarea",
                "Rows" => "3",
                "Cols" => "18",
                "Default" => "ns1.demo.autodns2.de\nns2.demo.autodns2.de",
                "Description" => "Provide your Default Nameservers (for Zone) Eg. ns1.demo.autodns2.de",
            ),
            'replyTo' => array
                (
                'FriendlyName' => 'Reply To',
                'Type' => 'dropdown',
                "Description" => "",
                "Options" => implode(",", $adminEmails),
                "Description" => "Contact address for support team in case of errors.",
            ),
            'domainMX' => array
                (
                'FriendlyName' => 'MX Record',
                "Type" => "text",
                "Size" => "20",
                "Description" => "MX record (mailserver). Enter the complete domain host name of the mailserver.",
            ),
            'domainIP' => array
                (
                'FriendlyName' => 'IP Address',
                "Type" => "text",
                "Size" => "20",
                "Description" => "IP address of the zone (A Record).",
            ),
            'autodelete' => array
                (
                'FriendlyName' => 'Auto Delete',
                'Type' => 'yesno'
            ),
            'sandboxMode' => array
                (
                'FriendlyName' => 'Test Mode',
                'Type' => 'yesno'
            ),
            'debug' => array
                (
                'FriendlyName' => 'Debug Mode',
                'Type' => 'yesno',
                "Description" => "Logs on \"Module Log\"",
            ),
            'adminContact' => array
                (
                'FriendlyName' => 'Admin Contact',
                'Type' => 'yesno',
                "Description" => "Specify that the owner will be OwnerC and AdminC and reseller will be Billing/TechC",
            ),
        );
    }

    public static function RegisterDomain($params){
        return false;
    }

    public static function TransferDomain($params){
        return false;
    }

    public static function RenewDomain($params){
        return false;
    }

    public static function GetNameservers($params){
        return false;
    }

    public static function SaveNameservers($params){
        return false;
    }

    public static function GetDNS($params){
        return false;
    }

    public static function SaveDNS($params){
        return false;
    }

    public static function GetContactDetails($params){
        return false;
    }

    public static function SaveContactDetails($params){
        return false;
    }

    public static function GetEPPCode($params){
        return false;
    }

    public static function Sync($params){
        return false;
    }

    public static function TransferSync($params){
        return false;
    }

    public static function IDProtectToggle($params){
        return false;
    }

    public static function GetEmailForwarding($params){
        return false;
    }

    public static function SaveEmailForwarding($params){
        return false;
    }

    public static function CheckAvailability($params){
        return false;
    }

    public static function GetDomainSuggestions($params){
        return false;
    }

}