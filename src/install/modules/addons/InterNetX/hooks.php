<?php
/*
 *  * ******************************************************************
 *
 *    CREATED BY MODULESGARDEN       ->        http://modulesgarden.com
 *    AUTHOR                         ->        Dariusz Bijoś <dariusz.bi@modulesgarden.com>
 *    CONTACT                        ->        contact@modulesgarden.com
 *
 *   This software is furnished under a license and may be used and copied
 *   only  in  accordance  with  the  terms  of such  license and with the
 *   inclusion of the above copyright notice.  This software  or any other
 *   copies thereof may not be provided or otherwise made available to any
 *   other person.  No title to and  ownership of the  software is  hereby
 *   transferred.
 *
 *  * ******************************************************************
 */

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

require_once __DIR__.DS.'Loader.php';
require_once 'API_Connect.php';
use Illuminate\Database\Capsule\Manager as Capsule;
use MGModule\InterNetX\Addon\Core\extensions\Smarty;

new MGModule\InterNetX\Loader();

add_hook('AdminAreaHeadOutput', 1, function($vars) {

    $lookupProvider = Capsule::table('tblconfiguration')->select('value')->where('setting', '=', 'domainLookupRegistrar')->first();

    if ($lookupProvider->value == 'InterNetX')
    {
        if (isset($_REQUEST['loadNewModal'])  && $_REQUEST['loadNewModal'] == 'yes'){
            //check if delete button was clicked
            if (isset($_REQUEST['idToDelete']))
            {
                $idToDelete = $_REQUEST['idToDelete'];
                unset($_REQUEST['idToDelete']);
                try {
                    Capsule::table('mod_InterNetXtblpremiumclasses')->where('id', '=', $idToDelete)->delete();
                } catch (Exception $ex) {
                    throw new Exception($ex->getMessage());
                }
            }
            //check if save button was clicked
            if (isset($_REQUEST['savePremiumClasses']))
            {
                $premiumClasses = $_REQUEST['premiumClassData'];

                foreach ($premiumClasses as $key => $premiumClass) {
                    try {
                        if ($premiumClass['value'] != '')
                        {
                            $classExist = Capsule::select('SELECT class_name FROM mod_InterNetXtblpremiumclasses WHERE class_name="'.$premiumClass['value'].'"');

                            if (empty($classExist))
                            {
                                Capsule::table('mod_InterNetXtblpremiumclasses')->insert(array('class_name' => $premiumClass['value']));
                                $classExist = [];
                            }
                        }
                    } catch (Exception $ex) {
                        throw new Exception($ex->getMessage());
                    }
                }
            }
            //geting data from database
            try {
                $premiumDomiansClassesData = Capsule::select("SELECT * FROM mod_InterNetXtblpremiumclasses");
                foreach ($premiumDomiansClassesData as $key => $value) {
                    $adds['premiumClasses'][] = (array) $value;
                }
                //załadowanie modala
                $customDir = '../modules/addons/InterNetX/';
                $smarty = new Smarty();
                $modalContent = $smarty->view('premium_domains_modal', $adds, $customDir);
                die($modalContent);
            } catch (Exception $ex) {
                throw new Exception($ex->getMessage());
            }
        }

        if (isset($_REQUEST['replaceModalWithPricingForm']) && $_REQUEST['replaceModalWithPricingForm'] == 'yes')
        {
            $relID = $_REQUEST['pricingID'];
            try {
                // check if pricing save button was clicked
                if (isset($_REQUEST['savePremiumDomiansPrices']) && $_REQUEST['savePremiumDomiansPrices'] == 'yes')
                {
                    if (isset($_REQUEST['relID']))
                    {
                        $relID = $_REQUEST['relID'];
                    }
                    $premiumPricingData = $_REQUEST['premiumPricingData'];

                    foreach ($premiumPricingData as $key => $pricingValue) {
                        $insertDetails = explode(',', str_replace(['[',']'], ',', $pricingValue['name']));
                        if ($insertDetails[0] == 'register' || $insertDetails[0] == 'renew' || $insertDetails[0] == 'transfer'){
                            try {
                                //check if row already exists and update it
                                $relIDExists = Capsule::select('SELECT * FROM mod_InterNetXtblpremiumpricing WHERE relid='.$relID.' AND type="'.'domain'.$insertDetails[0].'" AND currency='.$insertDetails[1]);
                                if (!empty($relIDExists))
                                {
                                    Capsule::table('mod_InterNetXtblpremiumpricing')->where('type', 'domain'.$insertDetails[0])->where('currency',$insertDetails[1])->where('relid',$relID)->update(array($insertDetails[3].'years' => $pricingValue['value']));
                                }else{
                                    Capsule::table('mod_InterNetXtblpremiumpricing')->insert(array('relid' => $relID, 'type' => 'domain'.$insertDetails[0], 'currency' => $insertDetails[1], $insertDetails[3].'years' => $pricingValue['value']));
                                }
                            } catch (Exception $ex) {
                                throw new Exception($ex->getMessage());
                            }
                        }
                    }
                }
                $currencyInfo = Capsule::table('tblcurrencies')->select('code')->where('id', '=', '1')->first();
                $adds['currencies'] = $currencyInfo->code;
                $adds['relid'] = $relID;
                $premuimPricingData = Capsule::select("SELECT * FROM mod_InterNetXtblpremiumpricing WHERE relid=".$relID);
                foreach ($premuimPricingData as $key => $value) {
                    $premuimPricingData[$key] = (array) $value;
                }
                $adds['pricingData'] = $premuimPricingData;

                //load modal
                $customDir = '../modules/addons/InterNetX/';
                $smarty = new Smarty();
                $modalContent = $smarty->view('premium_pricing_modal', $adds, $customDir);
                die($modalContent);
            } catch (Exception $ex) {
                throw new Exception($ex->getMessage());
            }
        }

        $configureButton = "<a id='configureButton' class='btn btn-default btn-sm btn-block' data-toggle='modal' data-target='#modalAjax' data-modal-title='Configure Premium Domain Levels' data-btn-submit-id='btnSavePremium' data-btn-submit-label='Save'>Configure</a>";
        $configureButtonDisabled = "<a id='configureButton' class='btn btn-default btn-sm btn-block disabled' data-toggle='modal' data-target='#modalAjax' data-modal-title='Configure Premium Domain Levels' data-btn-submit-id='btnSavePremium' data-btn-submit-label='Save'>Configure</a>";
        if ($vars['filename'] == 'configdomains') {
            return '<script type="text/javascript">
            function replaceModalBody(){
                $("#modalAjax .modal-content").load("../modules/addons/InterNetX/premium_domains_modal.tpl");
            }
            function savePremiumPricing(relid){
                $("#modalAjaxPremiumLoader").show();
                $.ajax({
                        type: "POST",
                        data: {
                            replaceModalWithPricingForm : "yes",
                            savePremiumDomiansPrices : "yes",
                            relID: relid,
                            premiumPricingData : $("#premiumPricingForm").serializeArray()},
                        success: function(data) {
                            $("#modalAjax .modal-content").html(data);
                        }
                    });
            }
            function replaceModalBodyWithPricing(id, classname){
                $("#modalAjaxPremiumLoader").show();
                $.ajax({
                        type: "POST",
                        data: {
                            replaceModalWithPricingForm : "yes",
                            pricingID: id,
                            className: classname},
                        success: function(data) {
                            $("#modalAjax .modal-content").html(data);
                        }
                    });
            }
            function deleteClass(id){
            $("#modalAjaxPremiumLoader").show();
                $.ajax({
                        type: "POST",
                        data: {
                            loadNewModal : "yes",
                            idToDelete: id},
                        success: function(data) {
                            $("#modalAjax .modal-content").html(data);
                        }
                    });
            }
            function savePremium(){
            $("#modalAjaxPremiumLoader").show();
                $.ajax({
                        type: "POST",
                        data: {
                            savePremiumClasses : "yes",
                            loadNewModal : "yes",
                            premiumClassData : $("#premiumClassForm").serializeArray()},
                        success: function(data) {
                            $("#modalAjax .modal-content").html(data);
                        }
                });
            }
            window.onload = function() {
                var isSwitchOn = $(".bootstrap-switch-on").attr("class");
                MutationObserver = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver;

                var img = document.querySelector(".selected-provider")
                var observer = new MutationObserver(function onSrcChange(){
                  window.location.reload();
                })
                observer.observe(img,{attributes:true, childList: true});

                // $("#modalDuplicateTld").remove();
                // $("#modalMyNotes").remove();
                if(typeof(isSwitchOn) != "undefined")
                {
                    $("#linkConfigurePremiumMarkup").replaceWith("'.$configureButton.'");
                }else{
                    $("#linkConfigurePremiumMarkup").replaceWith("'.$configureButtonDisabled.'");
                }
                $("#configureButton").click(function(){
                    $.ajax({
                        type: "POST",
                        data: {loadNewModal : "yes"},
                        success: function(data) {
                            $("#modalAjax .modal-content").html(data);
                        }
                    });
                });
            };
            </script>';
        }
    }else{
        return'<script type="text/javascript">
		function reloadPage(){
                    $( window ).load(function() {
                        MutationObserver = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver;
                        var img = document.querySelector(".selected-provider")
                        var observer = new MutationObserver(function onSrcChange(){
                          window.location.reload();
                        })
                          observer.observe(img,{attributes:true, childList: true});
                        });
                    }
                reloadPage();
            </script>';
    }
});

add_hook('ClientAreaPage', 1, function($vars) {

    if (isset($vars['gid']) && $vars['gid'] == 'renewals')
    {
        $registrarInfo = Capsule::table('tblregistrars')->select('setting','value')->where('registrar', '=', 'InterNetX')->get();
        foreach ($registrarInfo as $key => $value) {
            $drcryptedData[$value->setting] = decrypt($value->value);
        }
        global $smartyvalues;

        $renewals = $smartyvalues['renewals'];
        $API = new API_Connect($drcryptedData['serverHost'], $drcryptedData['serverUsername'], $drcryptedData['serverPassword'], $drcryptedData['serverContext'], $drcryptedData['sandboxServerUsername'], $drcryptedData['sandboxServerPassword'], $drcryptedData['sandboxServerContext'], $drcryptedData['debug'], $drcryptedData['sandboxMode']);
        $API->setReplyTo($drcryptedData['replyTo']);
        foreach ($renewals as $key => $value) {
            $result = $API->checkPremiumAvability($value['domain'], "checkAvability");

            $resultType = (string)$result->result->status->type;
            if ($resultType != 'error')
            {
                //this is premium domain
                $className = (string) $result->result->data->domain_premium->price_class;
                $premiumclassID = Capsule::table('mod_InterNetXtblpremiumclasses')->select('id')->where('class_name', '=', $className)->first();
                $domianPricingData = Capsule::table('mod_InterNetXtblpremiumpricing')->select('*')->where('relid', '=', $premiumclassID->id)->where('currency', '=', '1')->where('type', '=', 'domainrenew')->get();
                $renewPrice = $domianPricingData[0]->{'1years'};
                if ($renewPrice == 0.00 || $renewPrice < 0)
                {
                    unset($smartyvalues['renewals'][$key]);
                    continue;
                }
                if ($vars['currency']['id'] != '1')
                {
                    $renewPrice = $domianPricingData[0]->{'1years'} * $vars['currency']['rate'];
                }
                $premiumRenewalDomains[] = ['domain' => $value['domain'], 'pricingClass' => $className, 'price' => $renewPrice];
                \WHMCS\Session::set("premiumRenewalDomains" ,$premiumRenewalDomains);
                $price = new WHMCS\View\Formatter\Price($renewPrice, $vars['currency']);
                $priceoptions[] = ['period' => '1', 'price' => $price];
                $smartyvalues['renewals'][$key]['renewaloptions'] = $priceoptions;
                unset($priceoptions);
            }
        }
    }
    if ($vars['filename'] == 'cart' && $vars['action'] == 'view')
    {
        global $smartyvalues;

        $renewals = $vars['renewals'];
        $premiumDomainsTable = \WHMCS\Session::get("premiumRenewalDomains");

        if($premiumDomainsTable != '')
        {
            foreach ($premiumDomainsTable as $key => $premiumDomainData) {
                foreach ($renewals as $key => $domainToRenew) {
                    if ($domainToRenew['domain'] == $premiumDomainData['domain'])
                    {
                        $price = new WHMCS\View\Formatter\Price($premiumDomainData['price'], $vars['currency']);
                        $modifiedSubtotalValue = $smartyvalues['subtotal']->toNumeric() - $domainToRenew['price']->toNumeric() + $premiumDomainData['price'];
                        $newSubtotal = new WHMCS\View\Formatter\Price($modifiedSubtotalValue, $vars['currency']);

                        $smartyvalues['renewals'][$key]['price'] = $price;
                        $smartyvalues['subtotal'] = $newSubtotal;

                    }
                }
            }
        }

    }
});

add_hook('CartTotalAdjustment', 1, function($vars) {
    $premiumDomainsTable = \WHMCS\Session::get("premiumRenewalDomains");
    if($premiumDomainsTable != '')
    {
        $cart_adjustments = array();

        foreach ($vars['renewals'] as $key => $value) {
            $renewalDomains[] = Capsule::table('tbldomains')->select('id', 'domain', 'recurringamount')->where('id', '=', $key)->first();
        }
        $amount = 0;
        foreach ($premiumDomainsTable as $key => $premiumDomain) {
            foreach ($renewalDomains as $key => $domainToRenew) {
                if ($premiumDomain['domain'] == $domainToRenew->domain)
                {
                    $amount += $premiumDomain['price'] - $domainToRenew->recurringamount;
                }
            }
        }

        if ($amount > 0) {
            $cart_adjustments = [
                "description" => "Amount added for renew premium domain/s",
                "amount" => $amount,
                "taxed" => true,
            ];
        }
        return $cart_adjustments;
    }

});