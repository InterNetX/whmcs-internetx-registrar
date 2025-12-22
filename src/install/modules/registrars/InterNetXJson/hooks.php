<?php
// Bootstrap the Module, Composer Autoload etc.
require_once(__DIR__ . '/app/bootstrap.php');

use App\RegistrarModule;
use App\Helpers\Config;
use App\Helpers\WhmcsBridge;
use Illuminate\Database\Capsule\Manager as Capsule;
use \WHMCS\Service\Service as Service;
use \WHMCS\Config\Setting as Setting;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

add_hook('AdminAreaHeaderOutput', 1, 'internetXClientDomainsCustomButtons');
add_hook("ClientAreaHeadOutput", 1, "internetXFixDomainContacts");
add_hook("ClientAreaHeadOutput", 1, "internetXAutoRenewHandler");
add_hook('CancellationRequest', 1, "internetXCancellationRequest");
add_hook("DomainEdit", 1, "internetXOnDomainEdit");


function internetXOnDomainEdit($vars)
{
    $domainid = $vars['domainid'];
    $domain = (array) Capsule::table('tbldomains')->where('id', $domainid)->first();
    if ($domain['registrar'] != Config::moduleName()) return;

    try {
        $domainName   = $domain['domain'];

        if ($domain['donotrenew'] == 1) {
            RegistrarModule::domainCancelationCreate($domainName);
        }
        if ($domain['donotrenew'] == 0) {
            RegistrarModule::domainCancelationDelete($domainName);
        }

    } catch (Exception $ex) {
    }
}


function internetXAutoRenewHandler($vars){
    if ($vars['action'] != "domaindetails") return;
    if (!$_POST || !isset($_POST['autorenew'])) return;

    $domainid = $vars['domainid'];

    $domain = (array)Capsule::table('tbldomains')->where('id', $domainid)->first();
    if ($domain['registrar'] != Config::moduleName()) return;

    global $smarty;
    try {
        $smarty->assign('registrar', $domain['registrar']);
        $domainName   = $domain['domain'];
        if ($vars['donotrenew'] == 1 || $vars['autorenew'] == 0) {
            RegistrarModule::domainCancelationCreate($domainName);

        } else if ($vars['donotrenew'] == 0 || $vars['autorenew'] == 1) {
            RegistrarModule::domainCancelationDelete($domainName);
        }
    }
    catch (Exception $ex) {
        $smarty->assign("updatesuccess", false);
        $smarty->assign("registrarcustombuttonresult", false);
        $smarty->assign("error", $ex->getMessage());
    }
}

// Module hooks
function internetXFixDomainContacts($vars){

    if ($_GET['action'] == 'domaincontacts')
    {
        $script = '<script type="text/javascript">
                window.onload = function() {
                $(".radio").hide();
                console.log(document.getElementById("Registrant3"));
                if(document.getElementById("Registrant3") !== null)
                {
                    document.getElementById("Registrant3").parentElement.style.display = "none";
                    document.getElementsByName("contactdetails[Registrant][First Name]")[0].disabled = true;
                    document.getElementsByName("contactdetails[Registrant][Last Name]")[0].disabled = true;
                    // document.getElementsByName("contactdetails[Registrant][Handle ID]")[0].disabled = true;
                }
                console.log(document.getElementById("Tech3"));
                if(document.getElementById("Tech3") !== null)
                {
                    document.getElementById("Tech3").parentElement.style.display = "none";
                    document.getElementsByName("contactdetails[Tech][First Name]")[0].disabled = true;
                    document.getElementsByName("contactdetails[Tech][Last Name]")[0].disabled = true;
                    // document.getElementsByName("contactdetails[Tech][Handle ID]")[0].disabled = true;
                    // document.getElementsByName("contactdetails[Tech][Company Name]")[0].readOnly = true;
                    // document.getElementsByName("contactdetails[Tech][City]")[0].readOnly = true;
                    // document.getElementsByName("contactdetails[Tech][Address]")[0].readOnly = true;
                    // document.getElementsByName("contactdetails[Tech][State]")[0].readOnly = true;
                    // document.getElementsByName("contactdetails[Tech][Postcode]")[0].readOnly = true;
                    // document.getElementsByName("contactdetails[Tech][Country]")[0].readOnly = true;
                    // document.getElementsByName("contactdetails[Tech][Phone Number]")[0].readOnly = true;
                    // document.getElementsByName("contactdetails[Tech][Email]")[0].readOnly = true;
                }
                if(document.getElementById("Admin3") !== null)
                {
                    document.getElementById("Admin3").parentElement.style.display = "none";
                    document.getElementsByName("contactdetails[Admin][First Name]")[0].disabled = true;
                    document.getElementsByName("contactdetails[Admin][Last Name]")[0].disabled = true;
                    // document.getElementsByName("contactdetails[Admin][Handle ID]")[0].disabled = true;
                    // document.getElementsByName("contactdetails[Admin][Company Name]")[0].readOnly = true;
                    // document.getElementsByName("contactdetails[Admin][City]")[0].readOnly = true;
                    // document.getElementsByName("contactdetails[Admin][Address]")[0].readOnly = true;
                    // document.getElementsByName("contactdetails[Admin][State]")[0].readOnly = true;
                    // document.getElementsByName("contactdetails[Admin][Postcode]")[0].readOnly = true;
                    // document.getElementsByName("contactdetails[Admin][Country]")[0].readOnly = true;
                    // document.getElementsByName("contactdetails[Admin][Phone Number]")[0].readOnly = true;
                    // document.getElementsByName("contactdetails[Admin][Email]")[0].readOnly = true;
                }
                if(document.getElementById("Zone3") !== null)
                {
                    document.getElementById("Zone3").parentElement.style.display = "none";
                    document.getElementsByName("contactdetails[Zone][First Name]")[0].disabled = true;
                    document.getElementsByName("contactdetails[Zone][Last Name]")[0].disabled = true;
                    // document.getElementsByName("contactdetails[Zone][Handle ID]")[0].disabled = true;
                    // document.getElementsByName("contactdetails[Zone][Company Name]")[0].readOnly = true;
                    // document.getElementsByName("contactdetails[Zone][City]")[0].readOnly = true;
                    // document.getElementsByName("contactdetails[Zone][Address]")[0].readOnly = true;
                    // document.getElementsByName("contactdetails[Zone][State]")[0].readOnly = true;
                    // document.getElementsByName("contactdetails[Zone][Postcode]")[0].readOnly = true;
                    // document.getElementsByName("contactdetails[Zone][Country]")[0].readOnly = true;
                    // document.getElementsByName("contactdetails[Zone][Phone Number]")[0].readOnly = true;
                    // document.getElementsByName("contactdetails[Zone][Email]")[0].readOnly = true;
                }
                };
                </script>';
        return $script;
    }
}

function internetXClientDomainsCustomButtons($vars)
{

    if ($vars['filename'] == 'clientsdomains') {
        if ($_POST['ajax'] == 1 && isset($_POST['action']) && $_POST['registrar'] == Config::moduleName() && $_POST['domain'] > 0) {
                switch ($_POST['action']) {
                    case 'deleteDomain':
                        $domain = (array) Capsule::table('tbldomains')->select('domain')->where('id', '=', (int) $_POST['domain'])->first();
                        RegistrarModule::deleteDomain($domain['domain'], $_POST['execdate']);
                        die('success');
                        break;
                }
        }
        return '<script type="text/javascript">
        jQuery(document).ready(function(){

            var reg = jQuery("select[name=registrar]").val();
            if(reg == "'.Config::moduleName().'"){
                var domain    = jQuery("select[name=id]").val();

                jQuery(".form").find("input[name^=oldns]").attr("value","");
                // jQuery(".form").find("input[type=button][data-target=\'#modalGetEPP\']").after(\' <input type="button" class="button btn btn-default" value="Delete Domain" id="DeleteDomain" /><div id="delete_dialog" style="display:none;"><br/>Execute: <select id="internetxjson_excute"><option value="NOW">Now</option><option value="EXPIRE">Expire</option><option value="WHEN">Date</option></select><span id="internetxjson_date" style="display:none;"> <input type="text" id="internetxjson_date_input" style="width:60px;"/> </span> </div>\');
                jQuery(".form").find("input[type=button][data-target=\'#modalGetEPP\']").after(\'<button type="button" class="button btn btn-default" id="DeleteDomain" data-toggle="modal" data-target="#internetxjsonmodalDelete"> Delete Domain </button> <div class="modal fade" id="internetxjsonmodalDelete" role="dialog" aria-labelledby="DeleteLabel" aria-hidden="true"> <div class="modal-dialog"> <div class="modal-content panel panel-primary"> <div id="internetxjsonmodalDeleteHeading" class="modal-header panel-heading"> <button type="button" class="close" data-dismiss="modal"> <span aria-hidden="true">×</span> <span class="sr-only">Close</span> </button> <h4 class="modal-title" id="DeleteLabel">Delete Domain</h4> </div> <div id="internetxjsonmodalDeleteBody" class="modal-body panel-body"> Execute: <select id="internetxjson_excute"> <option value="NOW">Now</option> <option value="EXPIRE">Expire</option> <option value="WHEN">Date</option> </select> <span id="internetxjson_date" style="display:none;"> <input type="text" id="internetxjson_date_input"/> </span> </div> <div id="internetxjsonmodalDeleteFooter" class="modal-footer panel-footer"> <button type="button" id="internetxjson-Delete-Yes" class="btn btn-primary"> Send Deletion Request </button><button type="button" id="internetxjson-Delete-No" class="btn btn-default" data-dismiss="modal"> Cancel </button> </div> </div> </div> </div>\');
                jQuery(".form").find("input[type=button][value=\'Transfer Out NACK\']").after(\'<div id="modcmdworking" style="display:none;text-align:center;margin-top:10px;"><img src="images/loader.gif"> &nbsp; Working...</div>\');

                $("#internetxjson-Delete-Yes").on("click", function(event) {
                        var execdate =$("#internetxjson_excute option:selected").val();
                        if(execdate=="WHEN")
                            execdate = $("#internetxjson_date_input").val();

                        jQuery.post("clientsdomains.php?userid=' . $_GET['userid'] . '&id=' . $_GET['id'] . '",{"action": "deleteDomain","ajax":1,"registrar":reg,"domain":domain,"execdate":execdate},function(data){
                             if(data=="success")
                                 jQuery("#profileContent > div").first().after(\'<div class="infobox ajax-info"><strong><span class="title">Success!</span></strong></div>\');
                             else
                                 jQuery("#profileContent > div").first().after(\'<div class="errorbox ajax-info"><strong><span class="title">Error</spam></strong><br />\'+data+\'</div>\');

                         });
                         $("#internetxjsonmodalDelete").modal( "hide" );

                });

                $("#internetxjson_excute").change(function(){
                        var selected =$("#internetxjson_excute option:selected").val();
                        if( selected == "WHEN")
                                $("#internetxjson_date").show();
                        else
                                $("#internetxjson_date").hide();
                });

                jQuery( document ).ajaxStart(function() {
                    jQuery(".ajax-info").remove();
                    jQuery("#modcmdworking").show();
                }).ajaxStop(function() {jQuery("#modcmdworking").hide();});
            }
        });
           $(function() {
                $( "#internetxjson_date_input" ).datepicker({ format: \'yyyy-mm-dd\' });
          });
        </script>';
    }
}

function internetXCancellationRequest($vars)
{
    // The Service ID to be cancelled will/should be passed (by WHMCS) as $vars['relid']
    if (!isset($vars['relid'])) {
        return;
    }

    // Only Proceed if user has choosen to cancel domain by ticking the checkbox
    if (!isset($_REQUEST['canceldomain']) || $_REQUEST['canceldomain'] != 'on') {
        return;
    }

    // Only Proceed if automation settings allow for automatic procession of cancellation requests
    if (Setting::getValue('AutoCancellationRequests') != "on") {
        return;
    }

    // Get the Service Info. Will be null if id does not exists
    $service = Service::find($vars['relid']);

    // Fail silently if Service does not exist or does not have an associated domain
    if (!$service || !$service->domain) {
        return;
    }

    // Get Domain associated with the Service
    $domainString = $service->domain;

    // Get The Domain Info
    $domain = (array) Capsule::table('tbldomains')->where('domain', '=', $domainString)->first();

    if ($domain['registrar'] != Config::moduleName()) return;

    RegistrarModule::domainCancelationCreate($domain['domain']);
}


