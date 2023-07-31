<?php

/* * ********************************************************************
 * Customization Services by ModulesGarden.com
 * Copyright  ModulesGarden, INBS Group Brand, All Rights Reserved
 * (2014-03-05, 09:26:57)
 *
 *
 *  CREATED BY MODULESGARDEN       ->        http://modulesgarden.com
 *  CONTACT                        ->       contact@modulesgarden.com
 *
 *
 *
 *
 * This software is furnished under a license and may be used and copied
 * only  in  accordance  with  the  terms  of such  license and with the
 * inclusion of the above copyright notice.  This software  or any other
 * copies thereof may not be provided or otherwise made available to any
 * other person.  No title to and  ownership of the  software is  hereby
 * transferred.
 *
 *
 * ******************************************************************** */

/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
use Illuminate\Database\Capsule\Manager as Capsule;

// debug mode
//if (isset($_GET['_debug']) && $_GET['_debug'] == 'turnon') {
//    error_reporting(E_ALL);
//    ini_set('display_errors', 1);
//}

if (!defined('DS'))
    define('DS', DIRECTORY_SEPARATOR);

// include class connection
include_once(dirname(__FILE__) . DS . 'class.connection.php');

function InterNetX_SSL_ProductTypes()
{
    return array(
        // THAWTE
        'THAWTECODESIGNING' => 'Thawte - Code Signing Certificate',
        'SSLWEB' => 'Thawte - SSL Web Server',
        'SGC' => 'Thawte - SGC SuperCerts',
        'SSL123' => 'SSL123',
        'SSLWEBWILDCARD' => 'Thawte - SSL Web Server Wildcard',
        'SSLWEBSERVEREV' => 'Thawte - SSL Web Server with EV',
        // Symantec
        'SECURESITE' => 'VeriSign - Secure Site',
        'SECURESITEPRO' => 'VeriSign - Secure Site Pro',
        'SECURESITEEV' => 'VeriSign - Secure Site with EV',
        'SECURESITEPROEV' => 'VeriSign - Secure Site Pro with EV',
        //GeoTrust
        'BASIC_SSL' => 'GeoTrust - Basic SSL',
        'TRUEBIZID' => 'GeoTrust - True Business',
        'TRUEBIZIDEV' => 'GeoTrust - True Business with EV',
        'TRUEBIZIDWILDCARD' => 'GeoTrust - True Business Wildcard',
        //Comodo
        'COMODO_SSL_ESSENTIAL' => 'Comodo - SSL Essential',
        'COMODO_SSL_ESSENTIAL_WILDCARD' => 'Comodo - SSL Essential Wildcard',
        'COMODO_SSL' => 'Comodo - SSL',
        'COMODO_SSL_PREMIUM' => 'Comodo - SSL Premium',
        'COMODO_SSL_PREMIUM_WILDCARD' => 'Comodo - SSL Premium Wildcard',
        'COMODO_SSL_UCC_MD' => 'Comodo - SSL UCC incl. 3 Domains',
        'COMODO_SSL_EV' => 'Comodo - SSL Extended Validation',
        'COMODO_SSL_MD_EV' => 'Comodo - SSL Extended Validation Multi Domain',
        //RAPID SSL
        'RAPID_SSL' => 'RapidSSL',
        'RAPID_SSL_WILDCARD' => 'RapidSSL - Wildcard',
        //S/MIMES
        'GLOBALSIGN_PERSONALSIGN_1' => 'GloablSign PersonalSign 1',
        'GLOBALSIGN_PERSONALSIGN_2' => 'GloablSign PersonalSign 2',
        'GLOBALSIGN_PERSONALSIGN_2_PRO' => 'GloablSign PersonalSign 2 Pro',
        'GLOBALSIGN_PERSONALSIGN_2_DEPT' => 'GloablSign PersonalSign 2 Department'
    );
}

function InterNetX_SSL_Software()
{
    return array(
        'IIS5' => 'Windows Server',
        'APACHESSL' => 'APACHE',
        'PLESK' => 'PLESK',
        'TOMCAT' => 'TOMCAT',
        'JAVASOFT' => 'Codesigning - Sun Java',
        'MS_AUTHENTICODE' => 'Codesigning - Microsoft Authenticode',
        'VBA' => 'Codesigning - Microsoft Office VB',
        'ADOBE_AIR' => 'Codesigning - Adobe Air',
        'APPLE' => 'Codesigning - Mac'
    );
}

function InterNetX_SSL_ConfigOptions()
{
    $id = $_POST['id'];
    try {
        Capsule::schema()->create('InterNetX_SSL_ssl', function($table) {
            $table->integer('account_id')->unique();
            $table->text('data')->nullable();
        });
    } catch (\Exception $e) {

    }

    $count = Capsule::table('tblemailtemplates')->where('name', '=', 'InterNetX_SSL - SSL Certificate Configuration Required')->count();
    if ($count <= 0) {
        Capsule::table('tblemailtemplates')->insert(
                array('type' => 'product',
                    'name' => 'InterNetX_SSL - SSL Certificate Configuration Required',
                    'subject' => "SSL Certificate Configuration Required",
                    'message' => '<p>Dear {\$client_name},</p><p>Thank you for your order for an SSL Certificate. Before you can use your certificate, it requires configuration which can be done at the URL below.</p><p>{\$ssl_configuration_link}</p><p>Instructions are provided throughout the process but if you experience any problems or have any questions, please open a ticket for assistance.</p><p>{\$signature}</p>',
                    'fromname' => '',
                    'fromemail' => '',
                    'disabled' => '',
                    'custom' => '',
                    'language' => '',
                    'copyto' => '',
                    'plaintext' => '0'));
    }

    $script = '
               <script type="text/javascript">
                   $(document).ready(function()
                   {
                       $("#generateCustomField").click(function()
                       {
                           $.post(window.location.href,{"action":"generateCustom","servertype": $("select[name=servertype]").val()}, function(data)
                           {
                               alert(data);
                               window.location.href = "configproducts.php?action=edit&id=' . $id . '&tab=3";
                           });

                           return false;
                       });
                   });
               </script>
               <a href="" id="generateCustomField">Generate custom field</a>
               ';

    $configarray = array(
        "Host" => array(
            "Type" => "text",
            "Size" => "25",
            "FriendlyName" => "Host",
            "Description" => "",
            "Default" => "",
        ),
        "User" => array(
            "Type" => "text",
            "Size" => "25",
            "FriendlyName" => "Username",
            "Description" => "",
            "Default" => "",
        ),
        "Pass" => array(
            "Type" => "password",
            "Size" => "25",
            "FriendlyName" => "Password",
            "Description" => "",
            "Default" => "",
        ),
        "Context" => array(
            "Type" => "text",
            "Size" => "25",
            "FriendlyName" => "Context",
            "Description" => "",
            "Default" => "",
        ),
        "Product" => array(
            "Type" => "dropdown",
            "Size" => "25",
            "Options" => implode(",", array_keys(InterNetX_SSL_ProductTypes())),
            "FriendlyName" => "Product",
            "Description" => "",
            "Default" => "",
        ),
        "Lifetime" => array(
            "Type" => "dropdown",
            "Size" => "25",
            "Options" => "12,24,36,48",
            "FriendlyName" => "Lifetime",
            "Description" => "",
            "Default" => "",
        ),
        "Software" => array(
            "Type" => "dropdown",
            "Options" => implode(",", array_values(InterNetX_SSL_Software())),
            "FriendlyName" => "Software",
            "Description" => "",
            "Default" => "",
        ),
        "CustomField" => array(

            "FriendlyName" => "Create Custom Field",
            "Description" => $script,
        ),
    );

    return $configarray;
}

/**
 * CREATE NEW RECORD IN DATABASE AND SEND EMAIL WITH CONFIGURATION LINK TO CLIENT
 * @param type $params
 * @return type
 */
function InterNetX_SSL_CreateAccount($params)
{
    if (isset($params['customfields']['Domain']) && !empty($params['customfields']['Domain'])) {
        Capsule::table('tblhosting')->where('id', '=', $params['serviceid'])->update(array(
            'domain' => $params['customfields']['Domain']));
    }
    $row = Capsule::table('tblsslorders')->where('serviceid', '=', $params['serviceid'])->first();

    if ($row->remoteid) {
        return 'Certificate already exists';
    }

    GLOBAL $CONFIG;

    Capsule::table('tblhosting')->where('id', $params['serviceid'])->update(array(
        'username' => '', 'password' => ''));

    Capsule::table('tblsslorders')->where('serviceid', '=', $params['serviceid'])->where('userid', '=', $params['clientsdetails']['userid'])->delete();

    $id = Capsule::table('tblsslorders')->insertGetId(array(
        'id' => '',
        'userid' => $params['clientsdetails']['userid'],
        'serviceid' => $params['serviceid'],
        'remoteid' => '',
        'module' => 'InterNetX_SSL',
        'certtype' => $params['configoption4'],
        'configdata' => '',
        'completiondate' => '',
        'status' => 'Awaiting Configuration'
    ));

    $url = '<a href="' . $CONFIG["SystemURL"] . '/configuressl.php?cert=' . md5($id) . '">' . $CONFIG["SystemURL"] . '/configuressl.php?cert=' . md5($id) . '</a>';

    sendMessage("InterNetX_SSL - SSL Certificate Configuration Required", $params["serviceid"], array(
        "ssl_configuration_link" => $url
            )
    );

    if (isset($params['customfields']['Domain']) && !empty($params['customfields']['Domain'])) {
        Capsule::table('tblhosting')->where('id', '=', $params['serviceid'])->update(array(
            'domain' => $params['customfields']['Domain']));
    }



    return "success";
}

function InterNetX_SSL_TerminateAccount($params)
{
    $r = (array) Capsule::table('tblsslorders')->where('serviceid', '=', $params['serviceid'])->first();
    $api = new InterNetX_SSL_API($params['configoption1'], $params['configoption2'], $params['configoption3'], $params['configoption4']);
    $params['remoteid'] = $r['remoteid'];
    if ($params['remoteid'] > 0) {
        $api->revokeCert($params);
        if ($api->hasError())
            return $api->getErrors();
        else {
            Capsule::table('tblsslorders')->where('serviceid', '=', $params['serviceid'])->where('userid', '=', $params['clientsdetails']['userid'])->delete();
        }
    } else {
        return "Certificate not found.";
    }

    return "success";
}

/**
 * Renew certificate. This function should be called automatically
 * @param type $params
 * @return type
 */
function InterNetX_SSL_Renew($params)
{
    $r = (array) Capsule::table('tblsslorders')->where('serviceid', '=', $params['serviceid'])->first();
    $params['configdata'] = unserialize($r['configdata']);
    $api = new InterNetX_SSL_API($params['configoption1'], $params['configoption2'], $params['configoption3'], $params['configoption4']);
    if ($r['remoteid'] > 0) {
        $details = $api->getDetails((int) $r['remoteid']);
        $params['original'] = (array) $details->result->data->certificate;
        if($params['configoption5'] == 'GLOBALSIGN_PERSONALSIGN_1' || $params['configoption5'] == 'GLOBALSIGN_PERSONALSIGN_2' || $params['configoption5'] == 'GLOBALSIGN_PERSONALSIGN_2_PRO' || $params['configoption5'] == 'GLOBALSIGN_PERSONALSIGN_2_DEPT')
        {
            $api->renewCertNew($params);
        }else{
            $api->renewCert($params);
        }

        if ($api->hasError())
            return $api->getErrors();
    } else {
        return "Certificate not found.";
    }

    return "success";
}

function InterNetX_SSL_ContactTypes()
{
    return array('Administrative Contact' => 'admin', 'Technical Contact' => 'tech');
}

/**
 * First step
 * @param type $params
 * @return boolean
 */
function InterNetX_SSL_SSLStepOne($params)
{
    //print_r($params);die();
    $_LANG = InterNetX_SSL_loadLanguage();
    $fields = array();

    $previous_type = null;
    $contact_types = InterNetX_SSL_ContactTypes();
    $support_md = array('TRUEBIZID', 'TRUEBIZIDEV', 'COMODO_SSL_MD_EV', 'COMODO_SSL_UCC_MD');

    if (in_array($params['configoption5'], $support_md)) {
        $fields['additionalfields']['Additional Domains']['Hosts'] = array(
            'FriendlyName' => 'Names',
            'Type' => 'textarea',
            'Size' => '30',
            'Description' => '',
            'Required' => false
        );
    }

    $fields['additionalfields']['Approver Email']['approver_email'] = array(
        'FriendlyName' => 'Approver Email',
        'Type' => 'text',
        'Size' => '30',
        'Description' => '',
        'Required' => false
    );




    foreach ($contact_types as $t) {
        $field = ucfirst($t);
        $tr = InterNetX_SSL_translate($t);

        if (in_array($previous_type, array('admin', 'billing', 'tech', 'organization'))) {
            $fields['additionalfields'][$tr]['CopyDataFrom' . $previous_type] = array
                (
                'FriendlyName' => InterNetX_SSL_translate('use_' . $previous_type . '_contact_info'),
                'Type' => 'yesno',
                'Description' => '<script type="text/javascript">$(function(){$("input[type=checkbox][name=\'fields[CopyDataFrom' . $previous_type . ']\']").click(function(){
                            if($(this).is(":checked"))
                            {
                                /* INPUT TYPE TEXT */
                                $("input[type=text][name^=\'fields[' . ucfirst($previous_type) . '\']").each(function(){
                                    val         = $(this).val();
                                    name        = $(this).attr("name");
                                    new_name    = name.replace("' . ucFirst($previous_type) . '", "' . $field . '");
                                    $("input[type=text][name=\'"+new_name+"\']").val(val);
                                    $("input[type=text][name=\'"+new_name+"\']").attr("readonly", "readonly");
                                });

                                $("input[type=text][name^=\'fields[' . ucfirst($previous_type) . '\']").change(function(){
                                    val         = $(this).val();
                                    name        = $(this).attr("name");
                                    new_name    = name.replace("' . ucFirst($previous_type) . '", "' . $field . '");
                                    $("input[type=text][name=\'"+new_name+"\']").val(val);
                                    $("input[type=text][name=\'"+new_name+"\']").change();
                                });

                                /* SELECT */
                                $("select[name^=\'fields[' . ucfirst($previous_type) . '\']").each(function(){
                                    val         = $(this).val();
                                    name        = $(this).attr("name");
                                    new_name    = name.replace("' . ucFirst($previous_type) . '", "' . $field . '");
                                    $("select[name=\'"+new_name+"\']").val(val);
                                    $("select[name=\'"+new_name+"\']").attr("readonly", "readonly");
                                });

                                $("select[name^=\'fields[' . ucfirst($previous_type) . '\']").change(function(){
                                    val         = $(this).val();
                                    name        = $(this).attr("name");
                                    new_name    = name.replace("' . ucFirst($previous_type) . '", "' . $field . '");
                                    $("select[name=\'"+new_name+"\']").val(val);
                                    $("select[name=\'"+new_name+"\']").change();
                                });
                            }
                            else
                            {
                                /* INPUT TYPE TEXT */
                                $("input[type=text][name^=\'fields[' . ucfirst($previous_type) . '\']").unbind("change");
                                $("input[type=text][name^=\'fields[' . $field . '\']").removeAttr("readonly");

                                /* SELECT  */
                                $("select[name^=\'fields[' . ucfirst($previous_type) . '\']").unbind("change");
                                $("select[name^=\'fields[' . $field . '\']").removeAttr("readonly");
                            }


                        })
                            $(".contentpadded select").css("width","220");
                        })</script>',
            );
        }

        $fields['additionalfields'][$tr][$field . 'FirstName'] = array
            (
            'FriendlyName' => InterNetX_SSL_translate('first_name'),
            'Type' => 'text',
            'Size' => '30',
            'Description' => '',
            'Required' => $t != 'organization' ? true : false,
        );
        $fields['additionalfields'][$tr][$field . 'LastName'] = array
            (
            'FriendlyName' => InterNetX_SSL_translate('last_name'),
            'Type' => 'text',
            'Size' => '30',
            'Description' => '',
            'Required' => $t != 'organization' ? true : false,
        );
        $fields['additionalfields'][$tr][$field . 'Title'] = array
            (
            'FriendlyName' => InterNetX_SSL_translate('title'),
            'Type' => 'text',
            'Size' => '30',
            'Description' => '',
            'Required' => true,
        );
        $fields['additionalfields'][$tr][$field . 'Name'] = array
            (
            'FriendlyName' => InterNetX_SSL_translate('organization'),
            'Type' => 'text',
            'Size' => '30',
            'Description' => '',
            'Required' => false,
        );
        $fields['additionalfields'][$tr][$field . 'Address1'] = array
            (
            'FriendlyName' => InterNetX_SSL_translate('address1'),
            'Type' => 'text',
            'Size' => '30',
            'Description' => '',
            'Required' => true,
        );
        $fields['additionalfields'][$tr][$field . 'Address2'] = array
            (
            'FriendlyName' => InterNetX_SSL_translate('address2'),
            'Type' => 'text',
            'Size' => '30',
            'Description' => '',
            'Required' => false,
        );
        /* $fields['additionalfields'][$tr][$field.'Address3'] = array
          (
          'FriendlyName'  =>  InterNetX_SSL_translate('address3'),
          'Type'          =>  'text',
          'Size'          =>  '30',
          'Description'   =>  '',
          'Required'      =>  false,
          ); */
        $fields['additionalfields'][$tr][$field . 'City'] = array
            (
            'FriendlyName' => InterNetX_SSL_translate('city'),
            'Type' => 'text',
            'Size' => '30',
            'Description' => '',
            'Required' => true,
        );
        $fields['additionalfields'][$tr][$field . 'State'] = array
            (
            'FriendlyName' => InterNetX_SSL_translate('state'),
            'Type' => 'text',
            'Size' => '30',
            'Description' => '',
            'Required' => true,
        );
        $fields['additionalfields'][$tr][$field . 'PostalCode'] = array
            (
            'FriendlyName' => InterNetX_SSL_translate('postal_code'),
            'Type' => 'text',
            'Size' => '30',
            'Description' => '',
            'Required' => true,
        );
        $fields['additionalfields'][$tr][$field . 'Country'] = array
            (
            'FriendlyName' => InterNetX_SSL_translate('country'),
            'Type' => 'country',
            'Size' => '30',
            'Description' => '',
            'Required' => true,
        );
        $fields['additionalfields'][$tr][$field . 'Phone'] = array
            (
            'FriendlyName' => InterNetX_SSL_translate('phone'),
            'Type' => 'text',
            'Size' => '30',
            'Description' => InterNetX_SSL_translate('phone_description'),
            'Required' => true,
        );
        $fields['additionalfields'][$tr][$field . 'Fax'] = array
            (
            'FriendlyName' => InterNetX_SSL_translate('fax'),
            'Type' => 'text',
            'Size' => '30',
            'Description' => InterNetX_SSL_translate('phone_description'),
            'Required' => true,
        );
        $fields['additionalfields'][$tr][$field . 'Email'] = array
            (
            'FriendlyName' => InterNetX_SSL_translate('email'),
            'Type' => 'text',
            'Size' => '30',
            'Description' => '',
            'Required' => true,
        );
        $previous_type = $t;
        $fields['additionalfields']['<br />'] = array(
        );
    }

    return $fields;
}

/**
 * Second step
 * @param type $params
 * @return array
 */
function InterNetX_SSL_SSLStepTwo($params)
{
    //LOAD LANG
    $_LANG = InterNetX_SSL_loadLanguage();

    //CHECK TYPES AND PHONES
    $types = InterNetX_SSL_ContactTypes();

    foreach ($types as $type) {
        $n = $type . 'Phone';
        if (isset($params['configdata']['fields'][$n])) {
            $phone = $params['configdata']['fields'][$n];
            $preg = preg_match('/(\+{1})([0-9]{2,3})(\.{1})([0-9]+)/', $phone);
            if (!$preg) {
                return array('error' => InterNetX_SSL_translate('invalid_phone_number'));
            }
        }
    }

    //For trustwave certs
    $row = (array) Capsule::table('tblsslorders')->select('certtype')->where(Capsule::raw('MD5(id)'), '=', $_REQUEST['cert'])->first();

    if (in_array($row['certtype'], array_values(InterNetX_SSL_ProductTypes()))) {
        return array
            (
            'approveremails' => array
                (
                $params['clientsdetails']['email'],
            )
        );
    }

    if (isset($params['configdata']['csr'])) {
        $data = openssl_csr_get_subject($params['configdata']['csr']);
        $domain = $data['CN'];
    }

    if (isset($params['csr'])) {
        $data = openssl_csr_get_subject($params['configdata']['csr']);
        $domain = $data['CN'];
    }
    if (empty($domain)) {
        $data = openssl_csr_get_subject($_POST['csr']);
        $domain = $data['CN'];
    }

    if (empty($domain)) {
        $domain = $params['domain'];
    }

    $emails = array
        (
        'approveremails' => array(
            1 => 'admin@' . $domain,
            2 => 'administrator@' . $domain,
            3 => 'postmaster@' . $domain,
            4 => 'hostmaster@' . $domain,
            5 => 'webmaster@' . $domain
        )
    );

    if (!empty($params['fields']['approver_email'])) {
        $emails['approveremails'][0] = $params['fields']['approver_email'];
    }

    return $emails;
}

/**
 * Third step
 * @param type $params
 * @return boolean
 */
function InterNetX_SSL_sslstepthree($params)
{
//MONKEY DEBUG by Paul
//@file_put_contents(__DIR__.'/log.txt', print_r($params, TRUE), FILE_APPEND);

    //Prepare CSR
    $csr = trim($params['configdata']['csr']);
    $csr = trim(preg_replace('/(.*)CERTIFICATE(.*)/', '', $csr));
    $csr = "-----BEGIN CERTIFICATE REQUEST-----\n" . $csr . "\n-----END CERTIFICATE REQUEST-----";
    $csr = trim($csr);

    $software = InterNetX_SSL_Software();
    $params['configdata']['software'] = $software[$params['configdata']['servertype']];

    $api = new InterNetX_SSL_API($params['configoption1'], $params['configoption2'], $params['configoption3'], $params['configoption4']);

    if($params['configoption5'] == 'GLOBALSIGN_PERSONALSIGN_1' || $params['configoption5'] == 'GLOBALSIGN_PERSONALSIGN_2' || $params['configoption5'] == 'GLOBALSIGN_PERSONALSIGN_2_PRO' || $params['configoption5'] == 'GLOBALSIGN_PERSONALSIGN_2_DEPT')
    {
    //create admin contact
    $admin = $api->contactCreate($params['configdata']['fields'], 'Admin');
    if ($api->hasError())
        return array('error' => $api->getErrors());
    }else{
        //create admin contact
        $admin = $api->contactCreate($params['configdata']['fields'], 'Admin');
        if ($api->hasError())
            return array('error' => $api->getErrors());

        //create tech contact
        $tech = $api->contactCreate($params['configdata']['fields'], 'Tech');
        if ($api->hasError())
            return array('error' => $api->getErrors());
    }

    $params['configdata']['admin_id'] = (int) $admin->result->status->object->value;
    $params['configdata']['tech_id'] = (int) $tech->result->status->object->value;

    //ORDER

    $cert = (array) Capsule::table('tblsslorders')->where('serviceid', '=', $params['serviceid'])->first();
    $cert['configdata'] = unserialize($cert['configdata']);
    $details = $cert['configdata']['fields'];
    if ($params['approveremail'])
        $params['configdata']['approveremail'] = $params['approveremail'];

    $data = openssl_csr_get_subject($csr);
    $domain = $data['CN'];

    $params['domain'] = $domain;
	// FIX: Replace \r\n in csr with \n
	// Because \r\n beeing replaced with &#13; by $xml->createElement, leading to malformated Request
	$params['csr'] = str_replace("\r\n", "\n", $params['csr']);

        //    create cert
    if($params['configoption5'] == 'GLOBALSIGN_PERSONALSIGN_1' || $params['configoption5'] == 'GLOBALSIGN_PERSONALSIGN_2' || $params['configoption5'] == 'GLOBALSIGN_PERSONALSIGN_2_PRO' || $params['configoption5'] == 'GLOBALSIGN_PERSONALSIGN_2_DEPT')
    {
        $res = $api->createCertNew($params);
    }else{
        $res = $api->createCert($params);
    }

    if ($api->hasError())
        return array('error' => $api->getErrors());

    $cert_id = (int) $res->result->data->certificate_job->job->id;
    $id = $api->searchSingleJob($cert_id,$id);
    if ($id > 0) {

    } else {
        $id = $api->searchSingleJob2($cert_id);
    }

    $params['configdata']['remoteid'] = $id;
    $params['configdata']['job_id'] = $cert_id;
    $config = serialize($params['configdata']);

    Capsule::table('tblsslorders')->where('serviceid', '=', $params['serviceid'])->where('configdata', '=', $config)->update(array(
        'remoteid' => $id));
    Capsule::table('tblhosting')->where('id', '=', $params['serviceid'])->update(array(
        'domain' => $params['domain']));

    $pdo = Capsule::connection()->getPdo();
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("REPLACE INTO InterNetX_SSL_ssl VALUES(:first,:second)");
        $stmt->execute(array(':first' => $params['serviceid'], ':second' => $config));
        $pdo->commit();
    } catch (Exception $ex) {
        $pdo->rollBack();
    }

    return array(
        'remoteid' => $id,
        'domain' => $params['domain'],
    );
}

function InterNetX_SSL_clientareacustombuttonarray()
{
    return array
        (
        InterNetX_SSL_translate('get_certificate') => 'getcert',
        InterNetX_SSL_translate('contact_management') => 'editcontact',
        InterNetX_SSL_translate('Reissue') => 'reissue'
    );
}

/**
 * Display details about cert in client area
 * @param type $params
 * @return string
 */
function InterNetX_SSL_ClientArea($params)
{

    $_LANG = InterNetX_SSL_loadLanguage();
    $r = (array) Capsule::table('tblsslorders')->where('serviceid', '=', $params['serviceid'])->first();
    $cert_id = $r['id'];

    $code = '<h3>' . InterNetX_SSL_translate('SSL Certificate Details') . '</h3>';
    $code .= '<table style="width: 100%" class="table">';
    if ($r['status'] == 'Completed') {
        $api = new InterNetX_SSL_API($params['configoption1'], $params['configoption2'], $params['configoption3'], $params['configoption4']);
        $details = $api->getDetails((int) $r['remoteid']);
        $r = $details->result->data->certificate;
        if ($api->hasError()) {
            $cert = (array) Capsule::table('InterNetX_SSL_ssl')->select('data')->where('account_id', '=', (int) $params['serviceid'])->first();
            $cert = unserialize($cert['data']);
            if ($cert['job_id'] > 0) {
                $api->cleanErrors();
                $info = $api->searchSingleJob($cert['job_id'], true);
                if (!empty($info)) {

                } else {
                    $info = $api->searchSingleJob2($cert['job_id'], true);
                }

                if ((string) $info->result->data->certificate_job->job->sub_status != "")
                    $status = (string) $info->result->data->certificate_job->job->sub_status;
            }

            $code .= '<tr><td style="border-top:0;">' . InterNetX_SSL_translate('configuration_status') . ':</td><td style="border-top:0;">' . ($status != "" ? $status : InterNetX_SSL_translate('WF_DOMAIN_APPROVAL')) . ' <button class="btn btn-mini btn-success" style="display:none;">' . InterNetX_SSL_translate('Reissue') . '</button></td></tr></table>';
            return $code;
        }

        $code .= '<tr><td style="border-top:0;">' . InterNetX_SSL_translate('certificate_details') . '</td><td style="border-top:0;"><button class="btn btn-mini btn-info" onclick="document.location=\'clientarea.php?action=productdetails&id=' . $params['serviceid'] . '&modop=custom&a=getcert\'">' . InterNetX_SSL_translate('get_certificate') . '</button></td></tr>';
        $code .= '<tr><td>' . InterNetX_SSL_translate('Certificate') . '</td><td><button class="btn btn-mini btn-info" onclick="document.location=\'clientarea.php?action=productdetails&id=' . $params['serviceid'] . '&modop=custom&a=getcert&type=ctr\'">' . InterNetX_SSL_translate('show_ctr') . '</button></td></tr>';
        $code .= '<tr><td>' . InterNetX_SSL_translate('Contact') . '</td><td><button class="btn btn-mini btn-warning" onclick="document.location=\'clientarea.php?action=productdetails&id=' . $params['serviceid'] . '&modop=custom&a=editcontact\'">' . InterNetX_SSL_translate('management') . '</button></td></tr>';
        $code .= '<tr><td>' . InterNetX_SSL_translate('domain') . '</td><td>' . (string) $r->name . '</td></tr>';
        //$code .= '<tr><td>'.InterNetX_SSL_translate('Product').'</td><td>'.(string)$r->product.'</td></tr>';
        $code .= '<tr><td>' . InterNetX_SSL_translate('Software') . '</td><td>' . (string) $r->software . '</td></tr>';
        $code .= '<tr><td>' . InterNetX_SSL_translate('period') . '</td><td>' . (int) $r->lifetime . ' ' . InterNetX_SSL_translate('Months') . '</td></tr>';
        if ((string) $r->approver_email) {
            $code .= '<tr><td>' . InterNetX_SSL_translate('approver_email') . '</td><td>' . (string) $r->approver_email . '</td></tr>';
        }
        $code .= '<tr><td>' . InterNetX_SSL_translate('configuration_status') . '</td><td>' . InterNetX_SSL_translate('completed') . '</td></tr>';
    } else {
        $code .= '<tr><td style="border-top:0;">' . InterNetX_SSL_translate('configuration_status') . '</td>';
        $code .= '<td style="border-top:0;"><a href="configuressl.php?cert=' . md5($cert_id) . '">' . InterNetX_SSL_translate('configure_now') . '</a></td></tr>';
    }

    $code .= '</table>';
    return $code;
}

/**
 * Display cert details in admin area
 * @param type $params
 * @return string
 */
function InterNetX_SSL_AdminServicesTabFields($params)
{
    $r = (array) Capsule::table('tblsslorders')->where('serviceid', '=', $params['serviceid'])->first();
    $remoteid = $r['remoteid'];
    if (!$r['id']) {
        $remoteid = "-";
        $r['status'] = "Not Yet Provisioned";
    }
    $fieldsarray = array(
        // "Order ID"                      => $remoteid,
        "SSL Configuration Status" => $r['status']
    );
    return $fieldsarray;
}

function InterNetX_SSL_AdminCustomButtonArray()
{
    $buttonarray = array(
        "Resend Configuration Email" => "resend",
        "Resend Approver Email" => "resendapprover",
        "Renew" => "Renew"
    );
    return $buttonarray;
}

function InterNetX_SSL_reissue($params)
{
    $_LANG = InterNetX_SSL_loadLanguage();
    $r = (array) Capsule::table('tblsslorders')->where('serviceid', '=', $params['serviceid'])->first();
    $api = new InterNetX_SSL_API($params['configoption1'], $params['configoption2'], $params['configoption3'], $params['configoption4']);
    $details = unserialize($r['configdata']);

    $vars = array(
        'remoteid' => (int) $r['remoteid'],
        'csr' => $details['csr'],
        'domain' => $params['domain'],
        'approver_email' => $details['approveremail'],
        'servertype' => $details['servertype'],
        'ProductName' => $params['configoption5']
    );

    $api->reissue($vars);
    if ($api->hasError()) {
        return $api->getLastError();
    } else {
        return 'success';
    }
}

/**
 * Display certificate in client area
 * @param type $params
 * @return type
 */
function InterNetX_SSL_getcert($params)
{
    $_LANG = InterNetX_SSL_loadLanguage();
    $r = (array) Capsule::table('tblsslorders')->where('serviceid', '=', $params['serviceid'])->first();
    $api = new InterNetX_SSL_API($params['configoption1'], $params['configoption2'], $params['configoption3'], $params['configoption4']);
    $details = $api->getDetails($r['remoteid']);
    $r = $details->result->data->certificate;
    if (!$api->hasError()) {
        return array
            (
            'templatefile' => 'getcert',
            'breadcrumb' => '> <a href="#">Get cert</a>',
            'vars' => array
                (
                'csr' => (string) $r->csr,
                'ctr' => (string) $r->server,
                'lang' => $_LANG,
                'serviceid' => $params['serviceid']
            )
        );
    }

    return $api->getErrors();
}

/**
 * Display certificate in client area
 * @param type $params
 * @return type
 */
function InterNetX_SSL_editcontact($params)
{
    $_LANG = InterNetX_SSL_loadLanguage();
    $r = (array) Capsule::table('tblsslorders')->where('serviceid', '=', $params['serviceid'])->first();
    $api = new InterNetX_SSL_API($params['configoption1'], $params['configoption2'], $params['configoption3'], $params['configoption4']);
    $errors = null;
    if (isset($_POST['_action']) && $_POST['_action'] == 'updateContact') {
        foreach (InterNetX_SSL_ContactTypes() as $val) {
            $api->contactUpdate($_POST['fields'], $val);
            if ($api->hasError())
                $errors .= $api->getErrors();
        }
    }


    $details = $api->getDetails($r['remoteid']);
    $r = $details->result->data->certificate;

    $contacts['Administrative Contact'] = (array) $r->admin;
    $contacts['Technical Contact'] = (array) $r->technical;


    if (!$api->hasError()) {
        return array
            (
            'templatefile' => 'editcontact',
            'breadcrumb' => '> <a href="#">Contact Management</a>',
            'vars' => array
                (
                'contacts' => $contacts,
                'lang' => $_LANG,
                'serviceid' => $params['serviceid'],
                'errors' => $errors
            )
        );
    }

    return $api->getErrors();
}

/**
 * Resend cert email to client
 * @param type $params
 * @return type
 */
function InterNetX_SSL_resend($params)
{
    $r = (array) Capsule::table('tblsslorders')->where('serviceid', '=', $params['serviceid'])->first();
    if (!$r['id']) {
        return "No SSL Order exists for this product";
    }
    global $CONFIG;
    $sslconfigurationlink = $CONFIG['SystemURL'] . "/configuressl.php?cert=" . md5($r['id']);
    $sslconfigurationlink = "<a href=\"{$sslconfigurationlink}\">{$sslconfigurationlink}</a>";
    $res = sendMessage("InterNetX_SSL - SSL Certificate Configuration Required", $params['serviceid'], array(
        "ssl_configuration_link" => $sslconfigurationlink
    ));

    return "success";
}

function InterNetX_SSL_resendapprover($params)
{
    /* $params['certid'] = InterNetX_SSL_getCertID($params);
      $api              = new InterNetX_SSL_API($params['configoption1'], $params['configoption2'], $params['configoption3'], $params['configoption4']);
      $res              = $api->resendEmail($params);
      $cert             = $api->searchCert($params)
      $details          = $api->getDetails((int)$cert->id);
      print_r($details);die();
     *
     */
    return "success";
}

function InterNetX_SSL_translate($key)
{
    $_LANG = InterNetX_SSL_loadLanguage();
    if (isset($_LANG[$key]))
        return $_LANG[$key];

    return $key;
}

//LOAD LANGUAGE
function InterNetX_SSL_loadLanguage()
{
    $language = null;
    if (isset($_SESSION['Language'])) { // GET LANG FROM SESSION
        $language = strtolower($_SESSION['Language']);
    } else {
        $row = (array) Capsule::table('tblclients')->select('language')->where('id', '=', $_SESSION['uid'])->first();
        if ($row['language'])
            $language = $row['language'];
    }

    if (!$language) { //Ouuuh?
        $row = (array) Capsule::table('tblconfiguration')->where('setting', '=', 'Language')->select('value')->first();
        $language = $row['value'];
    }
    $langfilename = dirname(__FILE__) . DS . 'lang' . DS . $language . '.php';
    $deflangfilename = dirname(__FILE__) . DS . 'lang' . DS . 'english.php';
    if (file_exists($langfilename))
        include($langfilename);
    else
        include($deflangfilename);

    return $_LANG;
}

function InterNetX_SSL_getCertID($params)
{
    $r = (array) Capsule::table('tblsslorders')->where('serviceid', '=', $params['serviceid'])->select('remoteid')->first();
    return $r['remoteid'];
}
