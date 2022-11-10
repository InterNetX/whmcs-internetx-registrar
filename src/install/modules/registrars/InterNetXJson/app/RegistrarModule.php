<?php

namespace App;

use DateTime;
use Exception;
use App\Helpers\Config;

use App\Helpers\WhmcsBridge;
use Domainrobot\Domainrobot;
use Domainrobot\Model\Domain;
use Domainrobot\Model\Contact;
use Domainrobot\Model\NameServer;
use Domainrobot\Lib\DomainrobotAuth;
use App\Providers\WhoisProxyProvider;
use Domainrobot\Model\TransferAnswer;
use App\Providers\LegacyApi\LegacyApi;
use App\Providers\LegacyApi\InterNetXExtension;
use Domainrobot\Lib\DomainrobotException;
use Domainrobot\Model\ContactItExtensions;
use App\Contracts\RegistrarModuleInterface;
use Domainrobot\Model\ContactTypeConstants;
use WHMCS\Domains\DomainLookup\ResultsList;
use App\Exceptions\RegistrarModuleException;
use WHMCS\Domains\DomainLookup\SearchResult;
use Domainrobot\Model\RegistryStatusConstants;

use function PHPUnit\Framework\throwException;
use App\Providers\WhmcsModuleLogProvider as Logger;
use Illuminate\Database\Capsule\Manager as Capsule;

class RegistrarModule implements RegistrarModuleInterface
{

    /*
     * wrapper for all function to provide meaningfull error Responses as handled by whmcs
     */
    public static function call($functionName, $params = false)
    {
        try {
            WhmcsBridge::saveParamsLog($functionName, $params);
            return call_user_func('self::' . $functionName, $params);
        } catch (RegistrarModuleException $th) {
            return WhmcsBridge::errorResponse($th->getMessage());
        } catch (DomainrobotException $th) {
            //TODO: Move output to Module log
            return WhmcsBridge::errorResponse("[" . $th->getStatusCode() . "] " . json_encode($th->getError(), JSON_PRETTY_PRINT));
        } catch (\Throwable $th) {
            return WhmcsBridge::errorResponse($th->getMessage());
        }
    }

    private static function getApi($params)
    {

        // Use Sandbox Mode (Testmode)
        if ($params['sandboxMode'] == 'on') {
            WhmcsBridge::checkRequiredParametersOrFail(['sandboxServerUsername', 'sandboxServerPassword', 'sandboxServerContext'], $params);

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
            WhmcsBridge::checkRequiredParametersOrFail(['serverUsername', 'serverPassword', 'serverContext'], $params);
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

    public static function getConfigArray()
    {
        $adminEmails = Capsule::table('tbladmins')->select('email')->orderBy('email', 'asc')->pluck('email')->toArray();

        $configArray = array(
            "FriendlyName" => array(
                "Type" => "System",
                "Value" => "InterNetX Json",
            ),
            "Description" => array(
                "Type" => "System",
                "Value" => "Get your free InterNetX Reseller Account here: <a href=\"https://www.internetx.com\" target=\"_blank\">https://www.internetx.com</a>.",
            ),
            "serverUsername" => array(
                'FriendlyName' => 'Username',
                "Type" => "text",
                "Size" => "20",
                "Description" => "Enter your username here.",
            ),
            "serverPassword" => array(
                'FriendlyName' => 'Password',
                "Type" => "password",
                "Size" => "20",
                "Description" => "Enter your password here.",
            ),
            "serverContext" => array(
                'FriendlyName' => 'Context',
                "Type" => "text",
                "Size" => "20",
                "Description" => "Enter your context here.",
            ),
            'serverHost' => array(
                'FriendlyName' => 'Server',
                'Type' => 'text',
                'Size' => '20',
                "Description" => "If left empty https://api.autodns.com/v1 will be used by default. Provide your API url if you use something different."
            ),
            'nameServers' => array(
                'FriendlyName' => 'Default Nameservers',
                "Type" => "textarea",
                "Rows" => "3",
                "Cols" => "18",
                "Default" => "",
                "Description" => "Provide your Default Nameservers (for Zone) Eg. ns1.demo.autodns2.de",
            ),
            "sandboxServerUsername" => array(
                'FriendlyName' => 'Sandbox Username',
                "Type" => "text",
                "Size" => "20",
                "Description" => "Enter your sandbox username here.",
            ),
            "sandboxServerPassword" => array(
                'FriendlyName' => 'Sandbox Password',
                "Type" => "password",
                "Size" => "20",
                "Description" => "Enter your sandbox password here.",
            ),
            "sandboxServerContext" => array(
                'FriendlyName' => 'Sandbox Context',
                "Type" => "text",
                "Size" => "20",
                "Description" => "Enter your sandbox context here.",
            ),
            'sandboxNameServers' => array(
                'FriendlyName' => 'Sandbox Default Nameservers',
                "Type" => "textarea",
                "Rows" => "3",
                "Cols" => "18",
                "Default" => "ns1.demo.autodns2.de\nns2.demo.autodns2.de",
                "Description" => "Provide your Default Nameservers (for Zone) Eg. ns1.demo.autodns2.de",
            ),
            'replyTo' => array(
                'FriendlyName' => 'Reply To',
                'Type' => 'dropdown',
                "Description" => "",
                "Options" => implode(",", $adminEmails),
                "Description" => "Contact address for support team in case of errors.",
            ),
            'domainMX' => array(
                'FriendlyName' => 'MX Record',
                "Type" => "text",
                "Size" => "20",
                "Description" => "MX record (mailserver). Enter the complete domain host name of the mailserver.",
            ),
            'domainIP' => array(
                'FriendlyName' => 'IP Address',
                "Type" => "text",
                "Size" => "20",
                "Description" => "IP address of the zone (A Record).",
            ),
            'autodelete' => array(
                'FriendlyName' => 'Auto Delete',
                'Type' => 'yesno'
            ),
            'sandboxMode' => array(
                'FriendlyName' => 'Test Mode',
                'Type' => 'yesno'
            ),
            'debug' => array(
                'FriendlyName' => 'Debug Mode',
                'Type' => 'yesno',
                "Description" => "Logs on \"Module Log\"",
            ),
            'adminContact' => array(
                'FriendlyName' => 'Admin Contact',
                'Type' => 'yesno',
                "Description" => "Specify that the owner will be OwnerC and AdminC and reseller will be Billing/TechC",
            ),
        );

        return $configArray;
    }

    public static function AdminCustomButtonArray()
    {
        $buttonArray = array(
            "Transfer Out ACK" => "transferOutAck",
            "Transfer Out NACK" => "transferOutNack",
        );
        return $buttonArray;
    }

    private static function prepareDomain($params, $domainrobot = false)
    {

        // Get API if not provided ny parameter
        if (!$domainrobot) {
            $domainrobot = self::getApi($params);
        }

        //Parse Contacts
        $contacts = WhmcsBridge::parseContacts($params);

        //Create Registrant and AdminContact
        $contacts['admin']      = $domainrobot->contact->create($contacts['admin']);
        $contacts['registrant'] = $domainrobot->contact->create($contacts['registrant']);
        // Keep only the 'id' everything else is not needed for the requests
        $contacts['admin']      = ['id' => $contacts['admin']['id']];
        $contacts['registrant'] = ['id' => $contacts['registrant']['id']];

        $nameservers = WhmcsBridge::parseNameservers($params);
        //Create Domain
        $domain = new Domain();
        $domain->setName(WhmcsBridge::parseDomain($params));
        $domain->setNameServers($nameservers);
        $domain->setOwnerc($contacts['registrant']);
        $domain->setAdminc($params['adminContact'] == 'on' ? $contacts['registrant'] : $contacts['admin']);
        $domain->setTechc($contacts['admin']);
        $domain->setZonec($contacts['admin']);
        $domain->setPrivacy($params['idprotection']);

        // Check for Premium Domain
        $isPremiumDomain = false;
        $premiumDomainPriceClass = null;

        try {
            $domainPremium = $domainrobot->domainPremium->info(WhmcsBridge::parseDomain($params));

            if (is_object($domainPremium) && $priceClass = $domainPremium->getPriceClass()) {
                $premiumDomainPriceClass = $priceClass;
                $isPremiumDomain = true;
            }
        } catch (DomainrobotException $th) {
            // domainPremium->info throws an exception if the domain is not Premium
            $isPremiumDomain = false;
            $premiumDomainPriceClass = null;
        }


        if ($isPremiumDomain) {
            if (WhmcsBridge::isPremiumDomainSetupOk($params)) {
                $domain->setPriceClass($premiumDomainPriceClass);
            } else {
                // Bail out on error here
                throw new RegistrarModuleException('Cannot register premium domain. Please make sure that your Premium Domains addon module is active and configured correctly.');
            }
        }
        // Add zone if nameservers are ours
        if (WhmcsBridge::containsRegistrarNameServer($nameservers)) {
            $zone = WhmcsBridge::parseDomainZone($params);
            // Initially i had to manually create the zone before the registration
            // This works now directly with the registration request, by using nameser->action = 'AUTO'
            // $createdZone = $domainrobot->zone->create($zone);
            $domain->setZone($zone);
        }
        return $domain;
    }

    private static function getDomainInfo($params)
    {
        $domainrobot = self::getApi($params);
        return $domainrobot->domain->info(WhmcsBridge::parseDomain($params));
    }

    public static function RegisterDomain($params)
    {
        // return WhmcsBridge::errorResponse('RegisterDomain not implemented' . json_encode($params, JSON_PRETTY_PRINT) . '</pre>');
        //Get API
        $domainrobot = self::getApi($params);
        $domain = self::prepareDomain($params, $domainrobot);

        // TODO: Check if needed
        //$domain->setIgnoreWhois(true);
        $objectJob = $domainrobot->domain->create($domain);
        return $objectJob;
    }

    public static function TransferDomain($params)
    {
        $domainrobot = self::getApi($params);
        $domain = self::prepareDomain($params, $domainrobot);
        $domain->setAuthinfo($params['eppcode']);
        $domain->setConfirmOwnerConsent(1);
        $objectJob = $domainrobot->domain->transfer($domain);
    }

    public static function RenewDomain($params)
    {
        // By Default, Accounts automatically renew Domains
        // but some Accounts have this disabled
        // So we HAVE TO manually renew every domain
        // if the Setting "autdelete" is 'on'
        // AutoDelete means "This is an Account with Auto Delete activated" as opposed to
        // "An Account that automatically renews domains"

        $doSendTheRenewRequest = false;

        // We need to manually renew the domain because the setting indiciates an account with autodelete on
        if (isset($params['autodelete']) && $params['autodelete'] == 'on') {
            $doSendTheRenewRequest = true;
        } else {
            // Autodelete is off so we normally don't have to send a renew request
            // Still the admin panel has the "renew" button that allows manual invocation of
            // this function. So we check for this and send out the renew anyway
            if (strpos($_SERVER['PHP_SELF'], 'clientsdomains.php') !== false) {
                $doSendTheRenewRequest = true;
            }
        }

        if ($doSendTheRenewRequest) {
            $domainrobot = self::getApi($params);
            $domain = $domainrobot->domain->info(WhmcsBridge::parseDomain($params));
            $domain->setRemoveCancelation(true);
            $domainrobot->domain->renew($domain);
        }
        return true;
    }

    public static function GetNameservers($params)
    {

        $domainrobot = self::getApi($params);
        $domain = $domainrobot->domain->info(WhmcsBridge::parseDomain($params));

        if (isset($domain['nameServers']) && is_array($domain['nameServers'])) {
            $result = [];
            foreach ($domain['nameServers'] as $key => $ns) {
                $result['ns' . ($key + 1)] = $ns['name'];
            }
            return $result;
        }
    }

    public static function SaveNameservers($params)
    {
        $domainrobot = self::getApi($params);
        $domain = $domainrobot->domain->info(WhmcsBridge::parseDomain($params));
        $domain->setNameServers(WhmcsBridge::parseNameservers($params));
        $domainrobot->domain->update($domain);
    }


    public static function GetDNS($params)
    {
        // return WhmcsBridge::errorResponse('GetDNS not implemented' . json_encode($params, JSON_PRETTY_PRINT) . '</pre>');
        $domainrobot = self::getApi($params);
        $domain = $domainrobot->domain->info(WhmcsBridge::parseDomain($params));
        $zone = $domainrobot->zone->info(WhmcsBridge::parseDomain($params), $domain['nameServers'][0]['name']);

        $hostRecords = array();
        // Include mainIP as Resource Record
        // If it exists
        if ($main = $zone->getMain()) {
            if ($mainIP = $main->getAddress()) {
                $hostRecords[] = array(
                    "hostname" => '',    // eg. www
                    "type"     => 'A',    // eg. A
                    "address"  => $mainIP,   // eg. 10.0.0.1
                    "priority" => 'N/A',    // eg. 10 (N/A for non-MX records)
                );
                // Also add www-Entry if wwwInclude is set
                if ($zone->getWwwInclude()) {
                    $hostRecords[] = array(
                        "hostname" => 'www',    // eg. www
                        "type"     => 'A',    // eg. A
                        "address"  => $mainIP,   // eg. 10.0.0.1
                        "priority" => 'N/A',    // eg. 10 (N/A for non-MX records)
                    );
                }
            }
        }

        // Include all "real ResourceRecords"
        foreach ($zone['resourceRecords'] as $record) {
            $hostRecords[] = array(
                "hostname" => $record['name'],    // eg. www
                "type"     => $record['type'],    // eg. A
                "address"  => $record['value'],   // eg. 10.0.0.1
                "priority" => $record['pref'],    // eg. 10 (N/A for non-MX records)
            );
        }

        return $hostRecords;
    }

    public static function SaveDNS($params)
    {
        // return WhmcsBridge::errorResponse('SaveDNS not implemented' . json_encode($params, JSON_PRETTY_PRINT) . '</pre>');
        $domainrobot = self::getApi($params);

        // Get Current Zone From API
        $domain = $domainrobot->domain->info(WhmcsBridge::parseDomain($params));
        $zone = $domainrobot->zone->info(WhmcsBridge::parseDomain($params), $domain['nameServers'][0]['name']);
        $newResourceRecords = WhmcsBridge::whmcs2domainrobotResourceRecords($params);
        $zone->setResourceRecords($newResourceRecords);

        // If our custom form was used to set the A Entry. Use it.
        // Else set MainIP to null
        if (isset($_REQUEST['zone']['main']['value'])) {
            $mainIP = $_REQUEST['zone']['main']['value'];
            $currentMain = $zone->getMain();
            $currentMain->setAddress($mainIP);
            $zone->setMain($currentMain);
        } else {
            $zone->setMain(null);
        }

        // If our custom form was used to use www_include use it
        // Else set wwwInclude to False
        if (isset($_REQUEST['zone']['www_include'])) {
            $www_include = (int) $_REQUEST['zone']['www_include'];
            $zone->setWwwInclude($www_include);
        } else {
            $zone->setWwwInclude(false);
        }
        $domainrobot->zone->update($zone);
    }

    public static function GetContactDetails($params)
    {
        // return WhmcsBridge::errorResponse('GetContactDetails not implemented'.json_encode($params, JSON_PRETTY_PRINT).'</pre>');
        $domainrobot = self::getApi($params);
        // Get Domain Info
        $domain = $domainrobot->domain->info(WhmcsBridge::parseDomain($params));
        // Extract the Contacts
        $contacts = array(
            'Registrant' => $domain["ownerc"],
            'Admin'      => $domain["adminc"],
            'Tech'       => $domain["techc"],
            'Zone'       => $domain["zonec"],
        );
        // Remove Doubles
        $contacts = array_unique($contacts);
        // return $contacts;
        $whmcsContacts = [];
        foreach ($contacts as $key => $contact) {
            // Get full information from API and update Array value
            $contactDetails = $domainrobot->contact->info($contact['id']);

            // API returns an array of addresses up to n long
            // We just want to show two fields
            // so we combine everything > 2
            if (count($contactDetails["address"]) > 2) {
                $contactDetails["address"] = [
                    $contactDetails["address"][0],
                    implode(', ', array_slice($contactDetails["address"], 1))
                ];
            }

            $whmcsContacts[$key]['First Name']   = $contactDetails["fname"];
            $whmcsContacts[$key]['Last Name']    = $contactDetails["lname"];
            $whmcsContacts[$key]['Company Name'] = $contactDetails["organization"];
            $whmcsContacts[$key]['City']         = $contactDetails["city"];
            $whmcsContacts[$key]['Address 1']    = $contactDetails["address"][0];
            $whmcsContacts[$key]['Address 2']    = $contactDetails["address"][1];
            $whmcsContacts[$key]['State']        = $contactDetails["state"];
            $whmcsContacts[$key]['Postcode']     = $contactDetails["pcode"];
            $whmcsContacts[$key]['Country']      = $contactDetails["country"];
            $whmcsContacts[$key]['Phone Number'] = $contactDetails["phone"];
            $whmcsContacts[$key]['Email']        = $contactDetails["email"];
        }
        return $whmcsContacts;
    }

    public static function SaveContactDetails($params)
    {

        WhmcsBridge::checkRequiredParametersOrFail(['contactdetails'], $params);

        $domainrobot = self::getApi($params);
        // Get Domain Info
        $domain = $domainrobot->domain->info(WhmcsBridge::parseDomain($params));
        // Extract the Contacts
        $contacts = array(
            'Registrant' => $domain["ownerc"],
            'Admin'      => $domain["adminc"],
            'Tech'       => $domain["techc"],
            'Zone'       => $domain["zonec"],
        );

        $whmcsContacts = $params['contactdetails'];


        foreach ($whmcsContacts as $key => $contact) {
            // Only Update if it exists
            if (!isset($contacts[$key])) {
                echo "Skipping $key\n";
                continue;
            }
            $liveContact = $domainrobot->contact->info($contacts[$key]['id']);
            // $liveContact["fname"] = ($whmcsContacts[$key]['First Name']);
            // $liveContact["lname"] = ($whmcsContacts[$key]['Last Name']);
            $liveContact["organization"] = ($whmcsContacts[$key]['Company Name']);
            $liveContact["city"] = ($whmcsContacts[$key]['City']);

            // Make Adresses Array
            $addresses = [$whmcsContacts[$key]['Address 1']];
            if ($whmcsContacts[$key]['Address 2']) {
                $addresses[] = $whmcsContacts[$key]['Address 2'];
            }
            $liveContact["address"] =  $addresses;
            $liveContact["state"] = ($whmcsContacts[$key]['State']);
            $liveContact["pcode"] = ($whmcsContacts[$key]['Postcode']);
            $liveContact["country"] = ($whmcsContacts[$key]['Country']);
            $liveContact["phone"] = ($whmcsContacts[$key]['Phone Number']);
            $liveContact["email"] = ($whmcsContacts[$key]['Email']);

            // Update the contact
            $r = $domainrobot->contact->update($liveContact);
        }
    }

    public static function GetEPPCode($params)
    {
        $domainrobot = self::getApi($params);

        // Get Domaininfo
        $domain = $domainrobot->domain->info(WhmcsBridge::parseDomain($params));

        // SET Domain Status to Active if not already
        if ($domain->getRegistryStatus() != RegistryStatusConstants::ACTIVE) {
            $domain->setRegistryStatus(RegistryStatusConstants::ACTIVE);
            $domainrobot->domain->update($domain);
        }
        // .de & .eu -> authinfo1Create
        if (strpos($params['tld'], 'de') !== false || strpos($params['tld'], 'eu') !== false) {
            $domainrobot->domain->createAuthinfo1($domain);
        }

        $domain = $domainrobot->domain->info(WhmcsBridge::parseDomain($params));
        return [
            'eppcode' => $domain->getAuthinfo()
        ];
    }

    public static function Sync($params)
    {
        $domainrobot = self::getApi($params);
        // Get Domaininfo
        try {
            $domain = $domainrobot->domain->info(WhmcsBridge::parseDomain($params));
            $expiryDate =  new DateTime($domain->getPayable());
            $returnData =  [
                // Expiry = Payable
                'expirydate' => $expiryDate->format('Y-m-d')
            ];
            // Expired
            if ($expiryDate < new DateTime()) {
                $returnData['active'] = false;
                $returnData['cancelled'] = true;
            } else {
                // Active
                $returnData['active'] = true;
            }
            return $returnData;
        } catch (DomainrobotException $th) {
            $error = $th->getError();
            switch ($error['status']['code']) {
                case 'EF01012':
                    return ['expired' => true];
                    break;

                case 'E0105':
                    return ['cancelled' => true];
                    break;

                default:
                    return WhmcsBridge::errorResponse($error);
                    break;
            }

            return ['cancelled' => true];
        }
        // return WhmcsBridge::errorResponse('Sync not implemented'.json_encode($params, JSON_PRETTY_PRINT).'</pre>');
    }

    public static function TransferSync($params)
    {
        // Correct Return Values
        // Per Option
        // 1= Transfer has been completed
        // return array(
        //         'completed' => true, // Return as true upon successful completion of the transfer
        //         'expirydate' => (string)$result->expiration, // The expiry date of the domain
        //     );
        // 2) Transfer has Actually failed (As in we got a failed reason)
        // return array(
        //     'failed' => true,
        //     'reason' => $status
        // );
        // 3) Not sure what happened. No Error no Completion details
        // Can Resync later
        // return array(
        //     'completed' => false,
        //     'failed' => false
        // );
        // 4) Real Error (errorResponse()...)


        $domainrobot = self::getApi($params);
        // Get Domaininfo
        try {
            $domain = $domainrobot->domain->info(WhmcsBridge::parseDomain($params));
            $expiryDate =  new DateTime($domain->getPayable());

            $returnData =  [
                // Expiry = Payable
                'completed' => true,
                'expirydate' => $expiryDate->format('Y-m-d')
            ];
            return $returnData;
        } catch (DomainrobotException $th) {
            $error = $th->getError();
            return WhmcsBridge::errorResponse($error);
        }
    }

    public static function IDProtectToggle($params)
    {
        // return WhmcsBridge::errorResponse('GetEmailForwarding not implemented' . json_encode($params, JSON_PRETTY_PRINT) . '</pre>');
        $domainrobot = self::getApi($params);
        $domain = $domainrobot->domain->info(WhmcsBridge::parseDomain($params));

        //On
        if ($params['protectenable'] == 1) {
            $domain->setPrivacy(true);
            $domainrobot->domain->update($domain);
            //off
        } else {
            $domain->setPrivacy(false);
            $domainrobot->domain->update($domain);
        }
    }

    public static function GetEmailForwarding($params)
    {
        return WhmcsBridge::errorResponse('GetEmailForwarding not implemented' . json_encode($params, JSON_PRETTY_PRINT) . '</pre>');
    }

    public static function SaveEmailForwarding($params)
    {
        return WhmcsBridge::errorResponse('SaveEmailForwarding not implemented' . json_encode($params, JSON_PRETTY_PRINT) . '</pre>');
    }

    // TODO: check registrarmodule_DomainSuggestionOptions() {
    // https://github.com/WHMCS/sample-registrar-module/blob/master/modules/registrars/registrarmodule/registrarmodule.php
    public static function GetDomainSuggestions($params)
    {

        if ($params['isIdnDomain']) {
            $sld = empty($params['punyCodeSearchTerm']) ? strtolower($params['searchTerm']) : strtolower($params['punyCodeSearchTerm']);
        } else {
            $sld = strtolower($params['searchTerm']);
        }

        // Return only Available Domains by setting $includeUnavailableDomains = false
        return self::checkDomains($sld, $params, false);
    }


    public static function CheckAvailability($params)
    {
        return self::checkDomains($params['sld'], $params);
    }

    private static function checkDomains($sld, $params, $includeUnavailableDomains = true)
    {
        if (empty($params['tldsToInclude'])) {
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
            $searchResult = new SearchResult($sld, '.' . $tld);

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
                //check if premium domain Class Pricing is configured
                $isPremiumClassExists = Capsule::table('mod_InterNetXtblpremiumclasses')->select('id')->where('class_name', '=', $details['premiumClass'])->exists();

                if ($isPremiumClassExists && WhmcsBridge::isPremiumDomainSetupOk($params)) {
                    $className = $details['premiumClass'];
                    $premiumclassID = Capsule::table('mod_InterNetXtblpremiumclasses')->select('id')->where('class_name', '=', $className)->first();
                    $domainPricing = Capsule::select('SELECT * FROM mod_InterNetXtblpremiumpricing WHERE relid=' . $premiumclassID->id . ' AND currency=1');
                    $searchResult->setPremiumDomain(true);

                    foreach ($domainPricing as $pricing) {
                        if ($pricing->type == 'domainregister') {
                            $registerPrices = $pricing->{'1years'};
                            $currencyCodes = Capsule::table('tblcurrencies')->select('code')->where('id', '=', $pricing->currency)->first();
                        } elseif ($pricing->type == 'domaintransfer') {
                            $transferPrices = $pricing->{'1years'};
                        } elseif ($pricing->type == 'domainrenew') {
                            $renewPrices = $pricing->{'1years'};
                        }
                    }

                    if ($registerPrices == '0.00') {
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
            if ($searchResult->getStatus() != SearchResult::STATUS_NOT_REGISTERED) {
                // Defaults to include them But DomainSuggestions set includeUnavailableDomains to false
                if ($includeUnavailableDomains) {
                    $results->append($searchResult);
                }
            } else {
                $results->append($searchResult);
            }
        }

        if ($params['debug']) Logger::log('Method checkAvailability', print_r($_REQUEST, true), 'sld: ' . $sld . "\nTLDS: " . implode(', ', $tlds) . "\n" . print_r($checkResult, true));
        return $results;
    }


    /**
     * FUNCTION InterNetX_AdminCustomButtonArray
     * Transfer out domain ACK
     * @param type $params
     * @return string
     */
    private static function TransferOutRequest($params, $doTransfer = false)
    {
        $domainrobot = self::getApi($params);
        $answer = $doTransfer ? TransferAnswer::ACK : TransferAnswer::NACK;
        // The reason. Possible values are : 1 = Evidence of fraud / 2 = Current UDRP action / 3 = Court order / 4 = Identity dispute / 5 = No payment for previous registration period / 6 = Express written objection to the transfer from the transfer contact.
        // Set to "null" for ACK and to  "7" for NACK (The former Task used 7)
        $reason = $doTransfer ? null : 7;
        $objectJob = $domainrobot->transferOut->answer(WhmcsBridge::parseDomain($params), $answer, $reason);
        return $objectJob;
    }


    public static function TransferOutNack($params)
    {
        return self::TransferOutRequest($params, false);
    }

    public static function TransferOutAck($params)
    {
        return self::TransferOutRequest($params, true);
    }

    public static function ClientAreaCustomButtonArray($params)
    {
        return array(
            //TODO: use LANG
            // Lang::trans('Domain Redirect') => 'DomainForwarding',
            // Lang::trans('Owner Change') => 'ChangeOwner',
            'Domain Redirect' => 'DomainForwarding',
            'Owner Change' => 'ChangeOwner',
        );
    }

    private static function requireOldApi()
    {
        require_once(__DIR__ . '/OldApi/class.InterNetX_API.php');
    }

    /**
     * FUNCTION InterNetX_domainForwarding
     * Manage domain forwarding in client area
     * @param array $params
     * @return array $return
     */
    public static function DomainForwarding($params)
    {

        try {


            $API = new LegacyApi($params['serverHost'], $params['serverUsername'], $params['serverPassword'], $params['serverContext'], $params['sandboxServerUsername'], $params['sandboxServerPassword'], $params['sandboxServerContext'], $params['debug'], $params['sandboxMode']);
            $API->setReplyTo($params['replyTo']);
            if (isset($params['original']))
                $params = $params['original'];

            $domain = WhmcsBridge::parseDomain($params);

            $errors = array();


            if (!empty($_POST)) {

                $domainForwarding = $_POST['domainForwarding'];
                foreach ($domainForwarding as $k => $df) {

                    $id = $df['id'];
                    $source = $df['source'];
                    $sourceDomain = "{$source}.{$domain}";
                    $target = $df['target'];

                    //delete
                    if (empty($df['source']) || empty($target)) {
                        $API->redirectDelete($id, "domain");
                        if ($API->hasError())
                            $errors[] = $API->getErrors();
                        //update
                    } else if ($sourceDomain == $id) {
                        $API->updateDomainRedirect($df['source'], $target, $df['mode'], $df['title']);
                        if ($API->hasError())
                            $errors[] = $API->getErrors();

                        //delete and create new
                    } else {
                        $API->redirectDelete($id, "domain");
                        $API->createDomainRedirect($df['source'], $target, $df['mode'], $df['title']);
                        if ($API->hasError())
                            $errors[] = $API->getErrors();
                    }
                }

                //adding new
                $newDomainForwarding = $_POST['newDomainForwarding'];
                if (!empty($newDomainForwarding['source']) && !empty($newDomainForwarding['target'])) {
                    $source = $newDomainForwarding['source'] . ".{$domain}";
                    $API->createDomainRedirect($newDomainForwarding['source'], $newDomainForwarding['target'], $newDomainForwarding['mode'], $newDomainForwarding['title']);
                    if ($API->hasError())
                        $errors[] = $API->getErrors();
                }
            }
            $result = $API->redirectInfo($domain);

            if ($API->hasError())
                $errors[] = $API->getErrors();

            $redirects = array();
            foreach ($result->result->data->redirect as $key => $rd) {
                if ($rd->mode == "single")
                    continue;
                $surce = str_replace("." . $domain, "", (string) $rd->source);
                $redirects[] = array(
                    "id" => $rd->source,
                    "source" => (string) $rd->source,
                    "target" => (string) $rd->target,
                    "mode" => (string) $rd->mode,
                    "title" => $rd->title,
                );
            }
        } catch (Exception $ex) {

            $errrors[] = $ex->getMessage();
        }

        if (!empty($errors))
            $call['errors'] = $errors;

        $call['redirects'] = $redirects;
        $call['domain'] = $params['sld'] . '.' . $params['tld'];
        $call['domainId'] = $params['domainid'];


        return array(
            'templatefile' => 'domainForwarding',
            'breadcrumb' => ' > <a href="#">Domain Forwarding</a>',
            'vars' => $call
        );
    }

    /**
     * FUNCTION InterNetX_changeOwner
     * Change owner domain in client area
     * @param array $params
     * @return array $return
     */
    public static function ChangeOwner($params)
    {
        try {
            $countryData = json_decode(file_get_contents(ROOTDIR . DS . 'resources' . DS . 'country' . DS . 'dist.countries.json'), true);
            $API = new LegacyApi($params['serverHost'], $params['serverUsername'], $params['serverPassword'], $params['serverContext'], $params['sandboxServerUsername'], $params['sandboxServerPassword'], $params['sandboxServerContext'], $params['debug'], $params['sandboxMode']);
            $API->setReplyTo($params['replyTo']);
            if (isset($params['original']))
                $params = $params['original'];


            $domain = WhmcsBridge::parseDomain($params);
            $errors = array();
            $info = $API->domainInfo($domain);
            if ($API->hasError())
                $errors[] = $API->getErrors();
            $fields = (array) Capsule::table('tbldomainsadditionalfields')->join('tbldomains', 'tbldomains.id', '=', 'tbldomainsadditionalfields.domainid')
                ->select('tbldomainsadditionalfields.*')->where('tbldomains.domain', '=', $domain)->orderBy('id', 'asc')->get();
            foreach ($fields as $field) {
                $params['additionalfields'][$field->name] = $field->value;
            }

            $extensions = InterNetXExtension::assing($params['tld'], $params['additionalfields']);

            if (!empty($_POST)) {
                foreach ($_POST['domainContacts'] as $key => $val) {
                    if (empty($val)) {
                        $contact[$key] = (string) $info->result->data->domain->$key;
                    } else {
                        $details = (array) Capsule::table('tblcontacts')->where('id', '=', $val)->where('userid', '=', (int) $_SESSION['uid'])->first();

                        $type = ($key == "ownerc") ? null : "admin";

                        if (!empty($details['address1']) && !empty($details['address2']))
                            $address = $details['address1'] . " " . $details['address2'];
                        else
                            $address = $details['address1'];

                        $user = array(
                            'id' => $contact,
                            'type' => ($details['companyname'] == "") ? "PERSON" : "ORG",
                            'firstName' => $details['firstname'],
                            'lastName' => $details['lastname'],
                            'organization' => $details['companyname'],
                            'address' => $address,
                            'city' => $details['city'],
                            'state' => $details['state'],
                            'postCode' => $details['postcode'],
                            'country' => $details['country'],
                            'countryName' => $countryData[$details['country']]['name'],
                            'phone' => InterNetX_FormatPhone($details['fullphonenumber']),
                            //'fax' => InterNetX_FormatPhone($details['fullphonenumber']),
                            'email' => $details['email'],
                        );

                        if ($key != "ownerc" && ($params['tld'] == "de" || $params['tld'] == "es"))
                            $user['type'] = "PERSON";

                        $result = $API->contactCreate($user, $extensions);
                        if ($API->hasError()) {
                            $errors[] = $API->getErrors();
                        } else {
                            $contact[$key] = (string) $result->result->status->object->value;
                        }
                    }
                }

                $client = internetx_getClientDetails($params['domainid']);
                $domainContacts = $_POST['domainContacts'];
                $autoinfo = (string) $info->result->data->domain->authinfo;
                $change = $API->domainOwnerChange($domain, $contact, $autoinfo, $client['email']);
                if ($API->hasError())
                    $errors[] = $API->getErrors();
                else
                    $infoMsg = (string) $change->result->status->text;
            } else {

                $domainContacts = array(
                    "ownerc" => (string) $info->result->data->domain->ownerc,
                    "adminc" => (string) $info->result->data->domain->adminc,
                    "techc" => (string) $info->result->data->domain->techc,
                    "zonec" => (string) $info->result->data->domain->zonec,
                );
            }
        } catch (Exception $ex) {
            $errrors[] = $ex->getMessage();
        }

        if (!empty($errors))
            $call['errors'] = $errors;

        if ($infoMsg)
            $call['info'] = $infoMsg;

        $call['domain'] = $params['sld'] . '.' . $params['tld'];
        $call['domainContacts'] = $domainContacts;
        $call['domainId'] = $params['domainid'];
        $call['contacts2'] = "<option value=''>Do not change Contact</option>";
        $query = Capsule::table('tblcontacts')->where('userid', '=', (int) $_SESSION['uid'])->get();
        foreach ($query as $row) {
            $call['contacts2'] .= "<option value='" . $row->id . "'>" . $row->firstname . ' ' . $row->lastname . '</option>';
        }

        //$call['contacts2'] .= '<option value="---addnew---">Add new Contact..</option>';
        return array(
            'templatefile' => 'changeOwner',
            'breadcrumb' => ' > <a href="#">Change Owner</a>',
            'vars' => $call
        );
    }
}
