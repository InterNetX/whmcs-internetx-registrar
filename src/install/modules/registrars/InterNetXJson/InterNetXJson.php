<?php

// Bootstrap the Module, Composer Autoload etc.
require_once(__DIR__ . '/app/bootstrap.php');

use App\RegistrarModule;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

// Pass all Module Calls through to the RegistrarModule Implementation
// to keep this file just a basic interface for whmcs.

function InterNetXJson_getConfigArray(){
    return RegistrarModule::call('getConfigArray');
}

function InterNetXJson_RegisterDomain($params){
    return RegistrarModule::call('RegisterDomain', $params);
    // return RegistrarModule::RegisterDomain($params);
}

function InterNetXJson_TransferDomain($params){
    return RegistrarModule::call('TransferDomain', $params);
}

function InterNetXJson_RenewDomain($params){
    return RegistrarModule::call('RenewDomain', $params);
}

function InterNetXJson_GetNameservers($params){
    return RegistrarModule::call('GetNameservers', $params);
}

function InterNetXJson_SaveNameservers($params){
    return RegistrarModule::call('SaveNameservers', $params);
}

function InterNetXJson_GetDNS($params){
    return RegistrarModule::call('GetDNS', $params);
}

function InterNetXJson_SaveDNS($params){
    return RegistrarModule::call('SaveDNS', $params);
}

function InterNetXJson_GetContactDetails($params){
    return RegistrarModule::call('GetContactDetails', $params);
}

function InterNetXJson_SaveContactDetails($params){
    return RegistrarModule::call('SaveContactDetails', $params);
}

function InterNetXJson_GetEPPCode($params){
    return RegistrarModule::call('GetEPPCode', $params);
}

function InterNetXJson_Sync($params){
    return RegistrarModule::call('Sync', $params);
}

function InterNetXJson_TransferSync($params){
    return RegistrarModule::call('TransferSync', $params);
}

function InterNetXJson_IDProtectToggle($params){
    return RegistrarModule::call('IDProtectToggle', $params);
}

function InterNetXJson_GetEmailForwarding($params){
    return RegistrarModule::call('GetEmailForwarding', $params);
}

function InterNetXJson_SaveEmailForwarding($params){
    return RegistrarModule::call('SaveEmailForwarding', $params);
}

function InterNetXJson_CheckAvailability($params){
    return RegistrarModule::call('CheckAvailability', $params);
}

function InterNetXJson_GetDomainSuggestions($params){
    return RegistrarModule::call('GetDomainSuggestions', $params);
}

function InterNetXJson_AdminCustomButtonArray()
{
    return RegistrarModule::call('AdminCustomButtonArray');
}

function InterNetXJson_TransferOutNack($params){
    return RegistrarModule::call('TransferOutNack', $params);
}

function InterNetXJson_TransferOutAck($params)
{
    return RegistrarModule::call('TransferOutAck', $params);
}

function InterNetXJson_ClientAreaCustomButtonArray($params)
{
    return RegistrarModule::call('ClientAreaCustomButtonArray');
}

function InterNetXJson_DomainForwarding($params)
{
    return RegistrarModule::call('DomainForwarding', $params);
}

function InterNetXJson_ChangeOwner($params)
{
    return RegistrarModule::call('ChangeOwner', $params);
}

