<?php

namespace App;

use App\Helpers\Config;
use Domainrobot\Domainrobot;

use Domainrobot\Model\Domain;
use Domainrobot\Model\Contact;
use Domainrobot\Model\NameServer;
use Domainrobot\Lib\DomainrobotAuth;
use App\Providers\WhoisProxyProvider;
use Domainrobot\Lib\DomainrobotException;
use App\Contracts\RegistrarModuleInterface;
use Domainrobot\Model\ContactTypeConstants;
use WHMCS\Domains\DomainLookup\ResultsList;
use App\Exceptions\RegistrarModuleException;
use WHMCS\Domains\DomainLookup\SearchResult;
use App\Providers\WhmcsModuleLogProvider as Logger;
use Illuminate\Database\Capsule\Manager as Capsule;

class RegistrarModule implements RegistrarModuleInterface {

    /*
     * wrapper for all function to provide meaningfull error Responses as handled by whmcs
     */
    public static function call($functionName, $params = false){
        try {
            return call_user_func('self::'.$functionName, $params);
        } catch (RegistrarModuleException $th) {
            return self::errorResponse($th->getMessage());
          }
          catch (DomainrobotException $th) {
            //TODO: Move output to Module log
            return self::errorResponse("[".$th->getStatusCode()."] ". json_encode($th->getError(), JSON_PRETTY_PRINT));
          }
          catch (\Throwable $th) {
            return self::errorResponse($th->getMessage());
          }


    }

    private static function errorResponse($msg){
        return array("error" => $msg);
    }

    private static function checkRequiredParametersOrFail($requiredParams, $params){
        // Check if param isset and not false
        foreach ($requiredParams as $requiredParam) {
            if (!isset($params[$requiredParam]) || !$params[$requiredParam]){
                throw new RegistrarModuleException("Please Check your Domain Registrar Configuration. Missing Parameter: $requiredParam");
            }
        }
        return true;
    }

    private static function getApi($params){

        // Use Sandbox Mode (Testmode)
        if ($params['sandboxMode'] == 'on'){
            self::checkRequiredParametersOrFail(['sandboxServerUsername', 'sandboxServerPassword', 'sandboxServerContext'], $params);

            $domainrobot = new Domainrobot([
                "url" => "https://api.demo.autodns.com/v1",
                "auth" => new DomainrobotAuth([
                    "user"     => $params['sandboxServerUsername'],
                    "password" => $params['sandboxServerPassword'],
                    "context"  => $params['sandboxServerContext']
                ])
            ]);
        // Live Mode
        } else {
            self::checkRequiredParametersOrFail(['serverUsername', 'serverPassword', 'serverContext'], $params);
            $domainrobot = new Domainrobot([
                // server Host can be manually set but default to the production Endpoint
                "url" => $params['serverHost'] ? $params['serverHost'] : "https://api.autodns.com/v1",
                "auth" => new DomainrobotAuth([
                    "user"     => $params['serverUsername'],
                    "password" => $params['serverPassword'],
                    "context"  => $params['serverContext']
                ])
            ]);
        }

        return $domainrobot;
    }

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
                "Description" => "If left empty https://api.autodns.com/v1 will be used by default. Provide your API url if you use something different."
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

    private static function whmcs2domainrobotContact($prefix, $params){

        $contact = new Contact();
        // CompanyName is set
        if ($params[$prefix . 'companyname']){
            $contact->setType(ContactTypeConstants::ORG);
            $contact->setOrganization($params[$prefix . 'companyname']);
        } else {
            $contact->setType(ContactTypeConstants::PERSON);
        }

        $contact->setCity($params[$prefix.'city']);
        $contact->setCountry($params[$prefix.'country']);
        $contact->setState($params[$prefix.'state']);
        $contact->setAddress([
            $params[$prefix.'address1'],
            $params[$prefix.'address2']
        ]);
        $contact->setPcode($params[$prefix.'postcode']);
        $contact->setFname($params[$prefix.'firstname']);
        $contact->setLname($params[$prefix.'lastname']);
        $contact->setEmail($params[$prefix.'email']);
        $contact->setPhone($params[$prefix.'phonenumber']);
        // $contact->setExtensions(new ContactExtensions([
        //     'it' => new ContactItExtensions([
        //         'entityType' => 1 // Italian and foreign natural persons
        //     ])
        // ]));
        return $contact;
    }

    private static function parseContacts($params){

        // WHMCS Params include 2 Contacts. Registrant and Admin.
        // Registrant params are without prefix e.g. $params['firstname'] while admin params are prefixed with admin e.g. $params['adminfirstname'];
        // We will parse both and return the DomainRobot\Contact type
        $contacts = [
            'registrant' => self::whmcs2domainrobotContact('', $params),
            'admin'      => self::whmcs2domainrobotContact('admin', $params)
        ];
        return $contacts;
    }

    public static function RegisterDomain($params){
        // return self::errorResponse('RegisterDomain not implemented' . json_encode($params, JSON_PRETTY_PRINT) . '</pre>');
        //Get API
        $domainrobot = self::getApi($params);

        //Parse Contacts
        $contacts = self::parseContacts($params);

        //Create Registrant and AdminContact
        $contacts['admin']      = $domainrobot->contact->create($contacts['admin']);
        $contacts['registrant'] = $domainrobot->contact->create($contacts['registrant']);
        // return self::errorResponse('Contacts' . print_r($contacts, true));

        //Create Domain
        $domain = new Domain();
        $domain->setName($params['domain_punycode']);
        $domain->setNameServers([
            new NameServer([
                "name" => "ns1.example.com"
            ]),
            new NameServer([
                "name" => "ns2.example.com"
            ])
        ]);
        $domain->setOwnerc($contacts['registrant']);
        $domain->setAdminc($params['adminContact'] == 'on' ? $contacts['registrant'] : $contacts['admin'] );
        $domain->setTechc($contacts['admin']);
        $domain->setZonec($contacts['admin']);

        //$domain->setIgnoreWhois(true);
        // TODO: Extension
        // $contact->setExtensions(new ContactExtensions([
        //     'it' => new ContactItExtensions([
        //         'entityType' => 1 // Italian and foreign natural persons
        //     ])
        // ]));

        //TODO: Premiums


        $objectJob = $domainrobot->domain->create($domain);
    }

    public static function TransferDomain($params){
        return self::errorResponse('TransferDomain not implemented'.json_encode($params, JSON_PRETTY_PRINT).'</pre>');
    }

    public static function RenewDomain($params){
        return self::errorResponse('RenewDomain not implemented<br/><pre>'.json_encode($params, JSON_PRETTY_PRINT).'</pre>');
    }

    public static function GetNameservers($params){
        return self::errorResponse('GetNameservers not implemented'.json_encode($params, JSON_PRETTY_PRINT).'</pre>');
    }

    public static function SaveNameservers($params){
        return self::errorResponse('SaveNameservers not implemented'.json_encode($params, JSON_PRETTY_PRINT).'</pre>');
    }

    public static function GetDNS($params){
        return self::errorResponse('GetDNS not implemented'.json_encode($params, JSON_PRETTY_PRINT).'</pre>');
    }

    public static function SaveDNS($params){
        return self::errorResponse('SaveDNS not implemented'.json_encode($params, JSON_PRETTY_PRINT).'</pre>');
    }

    public static function GetContactDetails($params){
        return self::errorResponse('GetContactDetails not implemented'.json_encode($params, JSON_PRETTY_PRINT).'</pre>');
    }

    public static function SaveContactDetails($params){
        return self::errorResponse('SaveContactDetails not implemented'.json_encode($params, JSON_PRETTY_PRINT).'</pre>');
    }

    public static function GetEPPCode($params){
        return self::errorResponse('GetEPPCode not implemented'.json_encode($params, JSON_PRETTY_PRINT).'</pre>');
    }

    public static function Sync($params){
        return self::errorResponse('Sync not implemented'.json_encode($params, JSON_PRETTY_PRINT).'</pre>');
    }

    public static function TransferSync($params){
        return self::errorResponse('TransferSync not implemented'.json_encode($params, JSON_PRETTY_PRINT).'</pre>');
    }

    public static function IDProtectToggle($params){
        return self::errorResponse('IDProtectToggle not implemented'.json_encode($params, JSON_PRETTY_PRINT).'</pre>');
    }

    public static function GetEmailForwarding($params){
        return self::errorResponse('GetEmailForwarding not implemented'.json_encode($params, JSON_PRETTY_PRINT).'</pre>');
    }

    public static function SaveEmailForwarding($params){
        return self::errorResponse('SaveEmailForwarding not implemented'.json_encode($params, JSON_PRETTY_PRINT).'</pre>');
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
                    // Although we do not have everything needed enabled
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
