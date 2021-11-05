<?php

namespace App\Contracts;

interface RegistrarModuleInterface{
    public static function getConfigArray();
    public static function RegisterDomain($params);
    public static function TransferDomain($params);
    public static function RenewDomain($params);
    public static function GetNameservers($params);
    public static function SaveNameservers($params);
    public static function GetDNS($params);
    public static function SaveDNS($params);
    public static function GetContactDetails($params);
    public static function SaveContactDetails($params);
    public static function GetEPPCode($params);
    public static function Sync($params);
    public static function TransferSync($params);
    public static function IDProtectToggle($params);
    public static function GetEmailForwarding($params);
    public static function SaveEmailForwarding($params);
    public static function CheckAvailability($params);
    public static function GetDomainSuggestions($params);
}



/*
Optional
public static function AdminCustomButtonArray()
	function transferOutAck($params)
	function transferOutNack($params)

function ClientAreaCustomButtonArray($params)
	function domainForwarding($params)
	function changeOwner($params)

*/
