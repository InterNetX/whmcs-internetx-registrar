<?php

// Bootstrap the Module, Composer Autoload etc.
require_once(__DIR__ . '/app/bootstrap.php');

use App\RegistrarModule;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

// Pass all Module Calls through to the RegistrarModule Implementation
// to keep this file just a basic interface for whmcs.

function DomainRobot_getConfigArray(){
    return RegistrarModule::call('getConfigArray');
}

function DomainRobot_RegisterDomain($params){
    return RegistrarModule::call('RegisterDomain', $params);
    // return RegistrarModule::RegisterDomain($params);
}

function DomainRobot_TransferDomain($params){
    return RegistrarModule::call('TransferDomain', $params);
}

function DomainRobot_RenewDomain($params){
    return RegistrarModule::call('RenewDomain', $params);
}

function DomainRobot_GetNameservers($params){
    return RegistrarModule::call('GetNameservers', $params);
}

function DomainRobot_SaveNameservers($params){
    return RegistrarModule::call('SaveNameservers', $params);
}

function DomainRobot_GetDNS($params){
    return RegistrarModule::call('GetDNS', $params);
}

function DomainRobot_SaveDNS($params){
    return RegistrarModule::call('SaveDNS', $params);
}

function DomainRobot_GetContactDetails($params){
    return RegistrarModule::call('GetContactDetails', $params);
}

function DomainRobot_SaveContactDetails($params){
    return RegistrarModule::call('SaveContactDetails', $params);
}

function DomainRobot_GetEPPCode($params){
    return RegistrarModule::call('GetEPPCode', $params);
}

function DomainRobot_Sync($params){
    return RegistrarModule::call('Sync', $params);
}

function DomainRobot_TransferSync($params){
    return RegistrarModule::call('TransferSync', $params);
}

function DomainRobot_IDProtectToggle($params){
    return RegistrarModule::call('IDProtectToggle', $params);
}

function DomainRobot_GetEmailForwarding($params){
    return RegistrarModule::call('GetEmailForwarding', $params);
}

function DomainRobot_SaveEmailForwarding($params){
    return RegistrarModule::call('SaveEmailForwarding', $params);
}

function DomainRobot_CheckAvailability($params){
    return RegistrarModule::call('CheckAvailability', $params);
}

function DomainRobot_GetDomainSuggestions($params){
    return RegistrarModule::call('GetDomainSuggestions', $params);
}

function DomainRobot_AdminCustomButtonArray()
{
    return RegistrarModule::call('AdminCustomButtonArray');
}

function DomainRobot_TransferOutNack($params){
    return RegistrarModule::call('TransferOutNack', $params);
}

function DomainRobot_TransferOutAck($params)
{
    return RegistrarModule::call('TransferOutAck', $params);
}

function DomainRobot_ClientAreaCustomButtonArray($params)
{
    return RegistrarModule::call('ClientAreaCustomButtonArray');
}

function DomainRobot_DomainForwarding($params)
{
    return RegistrarModule::call('DomainForwarding', $params);
}

function DomainRobot_ChangeOwner($params)
{
    return RegistrarModule::call('ChangeOwner', $params);
}

function DomainRobot_GetTldPricing(array $params)
{
    return RegistrarModule::call('GetTldPricing', $params);
}

