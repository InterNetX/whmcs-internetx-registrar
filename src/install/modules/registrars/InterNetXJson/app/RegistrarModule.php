<?php

namespace App;

use App\Helpers\Config;
use App\Providers\WhmcsModuleLogProvider as Logger;
use App\Providers\WhoisProxyProvider;
use App\Contracts\RegistrarModuleInterface;
use WHMCS\Domains\DomainLookup\ResultsList;
use WHMCS\Domains\DomainLookup\SearchResult;
use Illuminate\Database\Capsule\Manager as Capsule;

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

        return $configArray;
    }

    public static function RegisterDomain($params){
        return array("error" => 'müll Domain');
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

    // TODO: check registrarmodule_DomainSuggestionOptions() {
    // https://github.com/WHMCS/sample-registrar-module/blob/master/modules/registrars/registrarmodule/registrarmodule.php
    public static function GetDomainSuggestions($params){

        if ($params['isIdnDomain']) {
            $sld = empty($params['punyCodeSearchTerm']) ? strtolower($params['searchTerm']) : strtolower($params['punyCodeSearchTerm']);
        } else {
            $sld = strtolower($params['searchTerm']);
        }

        // Return only Available Domains by setting $includeUnavailableDomains = false
        return self::checkDomains($sld, $params, false);
    }


    public static function CheckAvailability($params){
       return self::checkDomains($params['sld'], $params);
    }

    private static function checkDomains($sld, $params, $includeUnavailableDomains = true)
    {
        if (empty($params['tldsToInclude'])){
            return false;
        }
        $tlds = $params['tldsToInclude'];

        // Initiate WHMCS Class ResultsList
        $results = new ResultsList();
        $checkResult = WhoisProxyProvider::whois($sld, $tlds, $params['debug']);

        foreach ($checkResult as $domain => $details) {
            $domainParts = explode('.', $domain);
            $sld = $domainParts[0];
            unset($domainParts[0]);
            $tld = implode('.', $domainParts);
            $searchResult = new SearchResult($sld, '.'.$tld);

            // Determine the appropriate status to return
            switch ($details['status']) {
                case 'free':
                    $status = SearchResult::STATUS_NOT_REGISTERED;
                    break;
                case 'assigned':
                    $status = SearchResult::STATUS_REGISTERED;
                    break;
                case 'reserved':
                    $status = SearchResult::STATUS_RESERVED;
                    break;
                case 'premium':
                    $status = SearchResult::STATUS_NOT_REGISTERED;
                    break;
                default:
                    $status = SearchResult::STATUS_TLD_NOT_SUPPORTED;
                    break;
            }
            $searchResult->setStatus($status);

            // Handle Premium Domains
            if ($details['premium']) {
                //check if premium domains addon and Lookup Provider are set
                $lookupProvider       = Capsule::table('tblconfiguration')->select('value')->where('setting', '=', 'domainLookupRegistrar')->first();
                $isAddonActive        = Capsule::table('tbladdonmodules')->select('value')->where('module', '=', 'InterNetX')->exists();
                // TODO:Can probably be read directly from $params (premiumdomainsEnabled or similar)
                $isPremiumDomainsOn = Capsule::table('tblconfiguration')->where('setting', '=', 'PremiumDomains')->value('value');
                $isPremiumClassExists = Capsule::table('mod_InterNetXtblpremiumclasses')->select('id')->where('class_name', '=', $details['premiumClass'])->exists();

                if($lookupProvider->value == Config::moduleName() && $isAddonActive && $isPremiumDomainsOn == '1' && $isPremiumClassExists){
                    $className = $details['premiumClass'];
                    $premiumclassID = Capsule::table('mod_InterNetXtblpremiumclasses')->select('id')->where('class_name', '=', $className)->first();
                    $domainPricing = Capsule::select('SELECT * FROM mod_InterNetXtblpremiumpricing WHERE relid='.$premiumclassID->id.' AND currency=1');
                    $searchResult->setPremiumDomain(true);

                    foreach ($domainPricing as $pricing) {
                        if($pricing->type == 'domainregister'){
                            $registerPrices = $pricing->{'1years'};
                            $currencyCodes = Capsule::table('tblcurrencies')->select('code')->where('id', '=', $pricing->currency)->first();
                        }elseif ($pricing->type == 'domaintransfer'){
                            $transferPrices = $pricing->{'1years'};
                        }elseif ($pricing->type == 'domainrenew'){
                            $renewPrices = $pricing->{'1years'};
                        }
                    }

                    if($registerPrices == '0.00') {
                        $searchResult->setStatus(SearchResult::STATUS_RESERVED);
                    }

                    $searchResult->setPremiumCostPricing([
                        'register' => $registerPrices,
                        'renew' => $transferPrices,
                        'transfer' => $renewPrices,
                        'CurrencyCode' => $currencyCodes->code,
                        'skipMarkup' => true,
                    ]);

                } else { //END: if Premium Stuff Enabled Check
                    // Important!! Otherwise Premium Domains will be shown as available for the normal TLD price
                    $searchResult->setStatus(SearchResult::STATUS_UNKNOWN);
                }
            } //END: if ($details['premium'])

            // How to handle Unavailable Domains?
            if ($searchResult->getStatus() != SearchResult::STATUS_NOT_REGISTERED){
                // Defaults to include them But DomainSuggestions set includeUnavailableDomains to false
                if ($includeUnavailableDomains){
                    $results->append($searchResult);
                }
            } else {
                $results->append($searchResult);
            }
        }

        if ($params['debug']) Logger::log('Method checkAvailability', print_r($_REQUEST, true), 'sld: ' . $sld . "\nTLDS: " . implode(', ', $tlds )."\n". print_r($checkResult, true));
        return $results;
    }
}
