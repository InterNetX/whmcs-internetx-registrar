<?php

// Bootstrap the Module, Composer Autoload etc.
require_once(__DIR__ . '/app/bootstrap.php');

use App\RegistrarModule;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

// Pass all Module Calls through to the RegistrarModule Implementation
// to keep this file just a basic interface for whmcs.

function SchlundTech_getConfigArray(){
    return RegistrarModule::call('getConfigArray');
}

function SchlundTech_RegisterDomain($params){
    return RegistrarModule::call('RegisterDomain', $params);
    // return RegistrarModule::RegisterDomain($params);
}

function SchlundTech_TransferDomain($params){
    return RegistrarModule::call('TransferDomain', $params);
}

function SchlundTech_RenewDomain($params){
    return RegistrarModule::call('RenewDomain', $params);
}

function SchlundTech_GetNameservers($params){
    return RegistrarModule::call('GetNameservers', $params);
}

function SchlundTech_SaveNameservers($params){
    return RegistrarModule::call('SaveNameservers', $params);
}

function SchlundTech_GetDNS($params){
    return RegistrarModule::call('GetDNS', $params);
}

function SchlundTech_SaveDNS($params){
    return RegistrarModule::call('SaveDNS', $params);
}

function SchlundTech_GetContactDetails($params){
    return RegistrarModule::call('GetContactDetails', $params);
}

function SchlundTech_SaveContactDetails($params){
    return RegistrarModule::call('SaveContactDetails', $params);
}

function SchlundTech_GetEPPCode($params){
    return RegistrarModule::call('GetEPPCode', $params);
}

function SchlundTech_Sync($params){
    return RegistrarModule::call('Sync', $params);
}

function SchlundTech_TransferSync($params){
    return RegistrarModule::call('TransferSync', $params);
}

function SchlundTech_IDProtectToggle($params){
    return RegistrarModule::call('IDProtectToggle', $params);
}

function SchlundTech_GetEmailForwarding($params){
    return RegistrarModule::call('GetEmailForwarding', $params);
}

function SchlundTech_SaveEmailForwarding($params){
    return RegistrarModule::call('SaveEmailForwarding', $params);
}

function SchlundTech_CheckAvailability($params){
    return RegistrarModule::call('CheckAvailability', $params);
}

function SchlundTech_GetDomainSuggestions($params){
    return RegistrarModule::call('GetDomainSuggestions', $params);
}

function SchlundTech_AdminCustomButtonArray()
{
    return RegistrarModule::call('AdminCustomButtonArray');
}

function SchlundTech_TransferOutNack($params){
    return RegistrarModule::call('TransferOutNack', $params);
}

function SchlundTech_TransferOutAck($params)
{
    return RegistrarModule::call('TransferOutAck', $params);
}

function SchlundTech_ClientAreaCustomButtonArray($params)
{
    return RegistrarModule::call('ClientAreaCustomButtonArray');
}

function SchlundTech_DomainForwarding($params)
{
    return RegistrarModule::call('DomainForwarding', $params);
}

function SchlundTech_ChangeOwner($params)
{
    return RegistrarModule::call('ChangeOwner', $params);
}

