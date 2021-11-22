<?php

declare(strict_types=1);

use Domainrobot\Model\Zone;
use App\Helpers\WhmcsBridge;
use PHPUnit\Framework\TestCase;
use App\Helpers\DomainRobotExtension;
use Domainrobot\Model\ContactItExtensions;
use Domainrobot\Model\ContactTypeConstants;
use App\Exceptions\RegistrarModuleException;
use Domainrobot\Model\ContactGeneralExtensions;
use Domainrobot\Model\NameserverActionConstants;
use Domainrobot\Model\ContactIdentificationExtensions;

final class WhmcsBridgeTest extends TestCase
{


    // checkRequiredParametersOrFail($requiredParams, $params){
    public function testcheckRequiredParametersOrFailThrowsExceptionWhenParameterIsMissing(): void
    {
        $this->expectException(RegistrarModuleException::class);
        WhmcsBridge::checkRequiredParametersOrFail(['required'], []);
    }

    public function testcheckRequiredParametersOrFailThrowsExceptionWhenParameterValueIsFalsy(): void
    {
        $this->expectException(RegistrarModuleException::class);
        WhmcsBridge::checkRequiredParametersOrFail(['required'], ['required'  => '']);
    }

    public function testcheckRequiredParametersOrFailReturnsTrueIfAllParametersExistAndAreNotFalsy(): void
    {
        $this->assertTrue(
            WhmcsBridge::checkRequiredParametersOrFail(['required', 'also'], ['required' => 'exists', 'also' => 'exists', 'other'])
        );

    }

    // errorResponse($msg){
    public function testErrorResponseReturnCorrectFormatForWhmcs(): void
    {
        $this->assertEquals(
            [ 'error' => 'Error Message' ],
            WhmcsBridge::errorResponse('Error Message')
        );
    }

    // static function parseDomain($params){
    public function testParseDomainReturnCorrectValue(): void
    {
        $this->assertEquals(
           'example.com',
            WhmcsBridge::parseDomain(['domain_punycode' => 'example.com'])
        );
    }

    // function parseNameservers($params)
    public function testparseNameserversReturnsArray(): void
    {
        $this->assertIsArray(
            WhmcsBridge::parseNameservers(['something' => 'butNotNameserver'])
        );
    }

    public function testparseNameserversReturnsEmptyArrayWhenParamsAreMissing(): void
    {
        $this->assertEquals(
           [],
            WhmcsBridge::parseNameservers(['something' => 'butNotNameserver'])
        );
    }

    public function testparseNameserversReturnsArrayWithNameServerClass(): void
    {
        $result = WhmcsBridge::parseNameservers([
            'ns1' => 'ns.example.com'
        ]);

        $this->assertInstanceOf(Domainrobot\Model\NameServer::class, $result[0]);
    }

    public function testparseNameserversReturnsCorrectNameserver(): void
    {
        $result = WhmcsBridge::parseNameservers([
            'ns1' => 'ns.example.com'
        ]);


        $this->assertArrayHasKey('name', $result[0]);
        $this->assertEquals('ns.example.com', $result[0]['name']);
        $this->assertNull($result[0]['ttl']);
        $this->assertNull($result[0]['ipAddresses']);
    }

    public function testparseNameserversCanParseGlueRecordsIPV4Only(): void
    {
        $result = WhmcsBridge::parseNameservers([
            'ns1' => 'ns.example.com 127.0.0.1'
        ]);

        $this->assertEquals('ns.example.com', $result[0]['name']);
        $this->assertNull($result[0]['ttl']);
        $this->assertEquals(['127.0.0.1'],$result[0]['ipAddresses']);
    }

    public function testparseNameserversCanParseGlueRecordsBothipv4Andipv6(): void
    {
        $result = WhmcsBridge::parseNameservers([
            'ns1' => 'ns.example.com 127.0.0.1 2001:0db8:3c4d:0015:0000:0000:1a2f:1a2b'
        ]);

        $this->assertEquals('ns.example.com', $result[0]['name']);
        $this->assertNull($result[0]['ttl']);
        $this->assertEquals(['127.0.0.1', '2001:0db8:3c4d:0015:0000:0000:1a2f:1a2b'],$result[0]['ipAddresses']);
    }

    public function testparseNameserversCanParseGlueRecordsWithTabs(): void
    {
        $result = WhmcsBridge::parseNameservers([
            'ns1' => "ns.example.com\t127.0.0.1\t2001:0db8:3c4d:0015:0000:0000:1a2f:1a2b"
        ]);

        $this->assertEquals('ns.example.com', $result[0]['name']);
        $this->assertNull($result[0]['ttl']);
        $this->assertEquals(['127.0.0.1', '2001:0db8:3c4d:0015:0000:0000:1a2f:1a2b'],$result[0]['ipAddresses']);
    }

    // parseDomainZone($params){
    public function testparseDomainZoneWorks(): void
    {


        $result = WhmcsBridge::parseDomainZone([
            'domainIP' => '127.0.0.1',
            'domainMX' => 'mail',
            'domain_punycode' => 'example.com',
            'ns1' => 'ns1.example.com',
            'ns2' => 'ns2.example.com'
        ]);
        $this->assertInstanceOf(DomainRobot\Model\Zone::class, $result);
        $this->assertInstanceOf(DomainRobot\Model\Soa::class, $result['soa']);
        $this->assertInstanceOf(DomainRobot\Model\MainIp::class, $result['main']);
        $this->assertEquals('example.com', $result['origin']);
        $this->assertEquals('do-not-reply@example.com', $result['soa']['email']);
        $this->assertEquals('127.0.0.1', $result['main']['address']);
    }

    public function testparseDomainReturnsCorrectActionForOwnNameservers(): void
    {

        $result = WhmcsBridge::parseDomainZone([
            'domainIP' => '127.0.0.1',
            'domainMX' => 'mail',
            'domain_punycode' => 'example.com',
            'ns1' => 'a.ns14.net',
            'ns2' => 'b.ns14.net',

        ]);
        $this->assertEquals(NameserverActionConstants::COMPLETE, $result['action']);
    }

    public function testparseDomainReturnsCorrectActionForForeignNameServers(): void
    {

        $result = WhmcsBridge::parseDomainZone([
            'domainIP' => '127.0.0.1',
            'domainMX' => 'mail',
            'domain_punycode' => 'example.com',
            'ns1' => 'a.other.com',
            'ns2' => 'b.other.com',

        ]);
        $this->assertEquals(NameserverActionConstants::NONE, $result['action']);
    }


    /**
     * @var string
     */
    private static $contactParams = [
        'tld' => 'com',
        'additionalfields' => [],
        'adminfirstname' => 'Test2',
        'adminlastname' => 'Kunde',
        'admincompanyname' => '',
        'adminemail' => 'mail@example.com',
        'adminaddress1' => 'Street 1',
        'adminaddress2' => 'Address 2',
        'admincity' => 'City',
        'adminfullstate' => 'Full State',
        'adminstate' => 'State',
        'adminpostcode' => '48145',
        'admincountry' => 'DE',
        'adminphonenumber' => '15127027754',
        'adminphonecc' => '49',
        'adminfullphonenumber' => '+49.15127027754',
    ];

    // whmcs2domainrobotContact
    public function testwhmcs2domainrobotContactForPersonReturnsCorrectly(): void
    {
        $result = WhmcsBridge::whmcs2domainrobotContact(
            'admin',
            static::$contactParams
        );
        $this->assertEquals(ContactTypeConstants::PERSON, $result['type']);
    }

    public function testwhmcs2domainrobotContactForOrgReturnsCorrectly(): void
    {
        $params = static::$contactParams;
        $params['admincompanyname'] = 'unit LLC';
        $result = WhmcsBridge::whmcs2domainrobotContact(
            'admin',
            $params
        );
        $this->assertEquals(ContactTypeConstants::ORG, $result['type']);
    }

    public function testwhmcs2domainrobotContactHandlesDomainExtensionsWithItAsExample(): void
    {
        $params = static::$contactParams;

        $params['tld'] = 'it';
        $params['additionalfields'] = [
            'IT Entity Type' => 1,
            'ID Number' => 'persoid',
            'vatnumber' => 'vatid',
        ];

        $result = WhmcsBridge::whmcs2domainrobotContact(
            'admin',
            $params
        );
        $this->assertInstanceOf(Domainrobot\Model\ContactExtensions::class, $result['extensions']);
        $this->assertInstanceOf(Domainrobot\Model\ContactItExtensions::class, $result['extensions']['it']);
        $this->assertInstanceOf(Domainrobot\Model\ContactGeneralExtensions::class, $result['extensions']['general']);
        $this->assertInstanceOf(Domainrobot\Model\ContactIdentificationExtensions::class, $result['extensions']['general']['identification']);
        $this->assertEquals('vatid', $result['extensions']['general']['vatNumber']);
        $this->assertEquals('persoid', $result['extensions']['general']['identification']['number']);
        $this->assertEquals(1, $result['extensions']['it']['entityType']);
    }





}

