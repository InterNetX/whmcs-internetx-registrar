<?php

namespace App\Helpers;

use Domainrobot\Model\Soa;
use Domainrobot\Model\Zone;
use Domainrobot\Model\MainIp;
use Domainrobot\Model\Contact;
use Domainrobot\Model\NameServer;
use App\Helpers\DomainRobotExtension;
use Domainrobot\Model\ContactExtensions;
use Domainrobot\tests\units\Domainrobot;
use Domainrobot\Model\ContactItExtensions;
use Domainrobot\Model\ContactTypeConstants;
use App\Exceptions\RegistrarModuleException;
use Domainrobot\Model\ContactGeneralExtensions;
use Domainrobot\Model\NameserverActionConstants;
use Domainrobot\Model\ContactIdentificationExtensions;
use Domainrobot\Model\ResourceRecord;


class WhmcsBridge
{
    public static function checkRequiredParametersOrFail($requiredParams, $params){
        // Check if param isset and not false
        foreach ($requiredParams as $requiredParam) {
            if (!isset($params[$requiredParam]) || !$params[$requiredParam]){
                throw new RegistrarModuleException("Please Check your Domain Registrar Configuration. Missing Parameter: $requiredParam");
            }
        }
        return true;
    }

    public static function saveParamsLog($functionName, $params){
        $fn = "$functionName.log";
        file_put_contents(__DIR__."/log/$fn", var_export($params, true)."\n\n", FILE_APPEND);
        return true;
    }

    public static function errorResponse($msg){
// debugged my MW        return array("error" => $msg);
        return array("error" => print_r($msg,true));
    }

    public static function containsRegistrarNameServer(Array $nsArray){
        foreach ($nsArray as $ns) {
            if (strpos($ns['name'], Config::registrarNameserver()) !== FALSE){
                return true;
            }
        }
        return false;
    }

    public static function parseDomainZone($params){
        return self::domainZoneFromTemplate($params);
    }

    public static function replaceInTemplate($t, $replaceMap, $replaceMapCache = null){

        // If Patterns Cache is empty, compile the replace Pattern from $replaceMap
        // For the regular Expression that replace partial Matches
        // Only include scalar replace values (preg_replace will throw a notice if you provide someting other than []string)
        if (is_null($replaceMapCache)){
            $replaceMapCache = [];
            foreach ($replaceMap as $pKey => $pValue) {
                if (is_scalar($pValue)){
                    $replaceMapCache['#' . preg_quote($pKey) . '#'] = $pValue ;
                }

            }
        }

        // Recursively loop through key values pairs
        foreach ($t as $key => $value) {
            if (is_array($value)){
                // Recurse on arrays
                $t[$key] = self::replaceInTemplate($value, $replaceMap, $replaceMapCache);
            } elseif (is_string($value)) {
                // If full match (e.g. $t[$key] == "{{domain}}")
                // Replace value with match value
                // This is important to replace e.g. "{{nameservers}}" with the full object
                if (in_array($value, array_keys($replaceMap))){
                    $t[$key] = $replaceMap[$value];
                } else {
                    // Replace Partial Matches using the precompiled replaceMapCache
                     $t[$key] = preg_replace(array_keys($replaceMapCache), $replaceMapCache, $value);
                }


            }
        }
        return $t;
    }

    public static function domainZoneFromTemplate($params, $templateFileName = 'zone.json'){

        // Load the Zone Template
        $template = file_get_contents(Config::templateDir(). $templateFileName);
        $template = json_decode($template, true);

        $mainip = $params['domainIP'];
        $mx = $params['domainMX'];
        $nameservers = self::parseNameservers($params);
        $action = self::containsRegistrarNameServer($nameservers) ? NameserverActionConstants::AUTO : NameserverActionConstants::NONE;

        $template = self::replaceInTemplate($template, [
            '{{domain}}' => self::parseDomain($params),
            '{{nameservers}}' => $nameservers,
            '{{config_ip}}' => $mainip,
            '{{config_mx}}' => $mx ? $mx : 'mail',
            '{{nameserver_action}}' => $action
        ]);

        $zone = new Zone($template);
        // throw new RegistrarModuleException(print_r($zone, true));
        return $zone;
    }

    public static function getFirstConfigNameserver($params){
        if ($params['sandboxMode'] == "on") {
            $zoneNs = explode("\n", trim($params['sandboxNameServers']));
        } else {
            $zoneNs = explode("\n", trim($params['nameServers']));
        }
        return trim($zoneNs[0]);
    }

    public static function whmcs2domainrobotResourceRecords($params){
        $dnsRecords = $params['dnsrecords'];
        $records = [];
        $notSuported = array("MXE", "URL", "FRAME");
        foreach ($dnsRecords as $k => $r) {
            // Skip unusable Entries
            if (empty($r['hostname']) && empty($r['address']))
                continue;

                // Notify of unsupported Types
            if (in_array($r['type'], $notSuported)){
                throw new RegistrarModuleException("Record \"{$r['type']}\" is not supported");
            }

            $records[] = new ResourceRecord([
                "name" => $r['hostname'],
                "type" => $r['type'],
                "value" => $r['address'],
                "pref" => $r['priority'] == "N/A" ? "" : $r['priority'],
            ]);
        }
        return $records;
    }

    public static function whmcs2domainrobotContact($prefix, $params){

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

        // Get tld specific Extensions From params
        $extensions = DomainRobotExtension::assign($params['tld'], $params['additionalfields']);

        if ($extensions){
            // Holds the Extensions to add to the contact
            $extensionsToAdd = array();

            $tldString = ucfirst(strtolower($params['tld']));
            $className = "Domainrobot\Model\Contact{$tldString}Extensions";

            // If the TLD Specific Extension Exists as a class
            // Instantiate it and fill with data
            if (class_exists($className)){
                $extensionClass = new $className($extensions['handle']);
                // This might not be needed
                // but unnecessary keys are ignored by the API anyways
                // So just in Case the SDK definition misses a required Field
                // We just manually set it here using the ArrayAccess::setOffset
                // Provided by the extension class
                foreach ($extensions['handle'] as $key => $value) {
                    $extensionClass[$key] = $value;
                }

                $extensionsToAdd[strtolower($params['tld'])] = $extensionClass;
            }

            // see if some Fields are general Extension material
            // e.g. vatNumber is a general Extension and not TLD Specific
            // by constructing the Class and then check what "sticks".
            // The Constructur only handles spefific keys, so we can omit
            // a check and just see if anything got set by counting the toArray() result
            // Identification Fields
            $generalExtension = new ContactGeneralExtensions($extensions['handle']);

            // Optionally add the Identification stuff (e.g. idnumber)
            $identificationExtension = new ContactIdentificationExtensions($extensions['handle']);
            if (count($identificationExtension->toArray())){
                $generalExtension['identification'] = $identificationExtension;
            }

            // add the resulting object
            if (count($generalExtension->toArray())){
                $extensionsToAdd['general'] = $generalExtension;
            }


            // Finally check if any Extensions where generated and add them to Contact
            if (count($extensionsToAdd)){
                $contact->setExtensions(new ContactExtensions($extensionsToAdd));
            }

        }

        return $contact;
    }

    public static function parseContacts($params){

        // WHMCS Params include 2 Contacts. Registrant and Admin.
        // Registrant params are without prefix e.g. $params['firstname'] while admin params are prefixed with admin e.g. $params['adminfirstname'];
        // We will parse both and return the DomainRobot\Contact type
        $contacts = [
            'registrant' => self::whmcs2domainrobotContact('', $params),
            'admin'      => self::whmcs2domainrobotContact('admin', $params)
        ];
        return $contacts;
    }

    public static function parseNameservers($params){

        // WHMCS Params include up to 6 Nameservers in Domain related Requests. $params['ns1'..'ns6']
        // We will parse them and return an Array of DomainRobot\Nameserver (s)
        $ns = array();
        for ($i = 1; $i < 6; $i++) {
            // Skip if ns$i is empty or notset
            if (!isset($params["ns$i"]) || $params["ns$i"] == ''){
                continue;
            }

            // We support passing IP Adresses (e.g. for glue records)
            // So ns1 might be something like:
            // "ns1.domainshop.test 127.0.0.1 2001:0db8:3c4d:0015:0000:0000:1a2f:1a2b"

            $details = explode(" ", preg_replace('/\s+/', ' ', $params["ns$i"]));

            // Either way the first element will be the nameserver name
            $name = array_shift($details);
            $nameserverObject = new NameServer([]);
            $nameserverObject->setName($name);
            // Add ip Adresses if applicable
            if (count($details)){
                $nameserverObject->setIpAddresses($details);
            }
            // Push to result Array
            $ns[] = $nameserverObject;
        }
        return $ns;
    }

    public static function parseDomain($params){
        return $params['domain_punycode'];
    }

    public static function isPremiumDomainSetupOk($params){

        // Is WHMCS Setting "Premium Domains Enabled" On?
        $isPremiumDomainsOn = (bool) $params['premiumEnabled'];
        if (!$isPremiumDomainsOn) {
            return false;
        }

        // Is our Premium Domains Addon Active?
        $isAddonActive  = Capsule::table('tbladdonmodules')->select('value')->where('module', '=', Config::premiumAddonName())->exists();
        if (!$isAddonActive){
            return false;
        }

        // Are we the lookup provider?
        $lookupProvider = Capsule::table('tblconfiguration')->select('value')->where('setting', '=', 'domainLookupRegistrar')->first();
        if ($lookupProvider->value != Config::moduleName()){
            return false;
        }

        return true;
    }

    public static function getRegistarConfig(){
        return getRegistrarConfigOptions(Config::moduleName());
    }

}
