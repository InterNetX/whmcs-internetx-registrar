<?php

declare(strict_types=1);

use App\RegistrarModule;
use PHPUnit\Framework\TestCase;

final class SaveContactDetailsTest extends TestCase
{
    private static function getTestDomainSld(){
        return "whmcs-module-" . uniqid();
    }

    public function testItCanUpdateContacts(): void
    {
        $sld = 'adw81281238283';
        $tld = 'com';
        $domainName = "$sld.$tld";

        $params = array (
          'whmcsVersion' => '8.2.1-release.1',
          'sandboxServerUsername' => '0000_Domainpoint_cb',
          'sandboxServerPassword' => 'domains',
          'sandboxServerContext' => '1',
          'sandboxNameServers' => "ns1.demo.autodns2.de\nns2.demo.autodns2.de",
          'replyTo' => 'mail@example.com',
          'domainMX' => 'mail',
          'domainIP' => '85.236.38.35',
          'autodelete' => '',
          'sandboxMode' => 'on',
          'debug' => 'on',
          'adminContact' => 'on',
          'domainname' => $domainName,
          'domain' => $domainName,
          'domain_punycode' => $domainName,
          'is_idn' => false,
          'sld' => $sld,
          'sld_punycode' => $sld,
          'tld' => $tld,
          'tld_punycode' => $tld,
          'regtype' => 'Register',
          'regperiod' => 1,
          'registrar' => 'InterNetXJson',
          'dnsmanagement' => false,
          'emailforwarding' => false,
          'idprotection' => false,
          'premiumEnabled' => true,
          'userid' => '1',
          'idnLanguage' => '',
          'adminfirstname' => 'Test2',
          'adminlastname' => 'Kunde',
          'admincompanyname' => '',
          'adminemail' => 'test@example.com',
          'adminaddress1' => 'Kirchstr. 24',
          'adminaddress2' => '',
          'admincity' => 'Münster',
          'adminfullstate' => 'Nordrhein-Westfalen',
          'adminstate' => 'Nordrhein-Westfalen',
          'adminpostcode' => '48145',
          'admincountry' => 'DE',
          'adminphonenumber' => '15127027754',
          'adminphonecc' => '49',
          'adminfullphonenumber' => '+49.15127027754',
          'firstname' => 'Test2',
          'lastname' => 'Kunde',
          'companyname' => '',
          'email' => 'test@example.com',
          'address1' => 'Kirchstr. 24',
          'address2' => '',
          'city' => 'Münster',
          'fullstate' => 'Nordrhein-Westfalen',
          'state' => 'Nordrhein-Westfalen',
          'postcode' => '48145',
          'country' => 'DE',
          'phonenumber' => '15127027754',
          'phonecc' => '49',
          'fullphonenumber' => '+49.15127027754',
          'ns1' => 'ns1.demo.autodns2.de',
          'ns2' => 'ns2.demo.autodns2.de',
          'ns3' => '',
          'ns4' => '',
          'ns5' => '',
          'additionalfields' => [],
          'contactdetails' =>
          array (
            'Registrant' =>
            array (
              'First Name' => 'FirstName',
              'Last Name' => 'Kunde',
              'Company Name' => '',
              'City' => 'Munster',
              'Address 1' => 'Kirchstr. 24',
              'Address 2' => 'Oben bei Paul',
              'State' => 'Nordrhein-Westfalen',
              'Postcode' => '48155',
              'Country' => 'DE',
              'Phone Country Code' => '49',
              'Phone Number' => '+49.1127027754',
              'Email' => 'test@example.com',
              'phone-normalised' => true,
            ),
          ),
        );


        // Try to Register
        $rm = RegistrarModule::call('SaveContactDetails', $params);
        // $this->assertInstanceOf(Domainrobot\Model\ObjectJob::class, $rm);
    }


}
