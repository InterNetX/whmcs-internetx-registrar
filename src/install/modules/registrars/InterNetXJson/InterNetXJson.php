<?php

use App\RegistrarModule;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

// Bootstrap the Module, Composer Autoload etc.
require_once(__DIR__ . '/app/bootstrap.php');

// Pass all Module Calls through to the RegistrarModule Implementation
// to keep this file just a basic interface for whmcs.

function InterNetXJson_getConfigArray(){
    return RegistrarModule::getConfigArray();
}

function InterNetXJson_RegisterDomain($params){
    return RegistrarModule::RegisterDomain($params);
}

function InterNetXJson_TransferDomain($params){
    return RegistrarModule::TransferDomain($params);
}

function InterNetXJson_RenewDomain($params){
    return RegistrarModule::RenewDomain($params);
}

function InterNetXJson_GetNameservers($params){
    return RegistrarModule::GetNameservers($params);
}

function InterNetXJson_SaveNameservers($params){
    return RegistrarModule::SaveNameservers($params);
}

function InterNetXJson_GetDNS($params){
    return RegistrarModule::GetDNS($params);
}

function InterNetXJson_SaveDNS($params){
    return RegistrarModule::SaveDNS($params);
}

function InterNetXJson_GetContactDetails($params){
    return RegistrarModule::GetContactDetails($params);
}

function InterNetXJson_SaveContactDetails($params){
    return RegistrarModule::SaveContactDetails($params);
}

function InterNetXJson_GetEPPCode($params){
    return RegistrarModule::GetEPPCode($params);
}

function InterNetXJson_Sync($params){
    return RegistrarModule::Sync($params);
}

function InterNetXJson_TransferSync($params){
    return RegistrarModule::TransferSync($params);
}

function InterNetXJson_IDProtectToggle($params){
    return RegistrarModule::IDProtectToggle($params);
}

function InterNetXJson_GetEmailForwarding($params){
    return RegistrarModule::GetEmailForwarding($params);
}

function InterNetXJson_SaveEmailForwarding($params){
    return RegistrarModule::SaveEmailForwarding($params);
}

function InterNetXJson_CheckAvailability($params){
    return RegistrarModule::CheckAvailability($params);
}

function InterNetXJson_GetDomainSuggestions($params){
    return RegistrarModule::GetDomainSuggestions($params);
}