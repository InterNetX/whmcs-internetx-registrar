<?php

/* * ********************************************************************
 * Customization Services by ModulesGarden.com
 * Copyright  ModulesGarden, INBS Group Brand, All Rights Reserved
 * (2014-03-05, 09:55:41)
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

use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * @author Maciej Husak <maciej@modulesgarden.com>
 */
function InterNetX_ChangeSelectValue($vars)
{
//    if($vars['filename'] == 'configuressl')
//    {
//        $r = mysql_fetch_assoc(mysql_query("SELECT p.servertype,p.configoption5 FROM tblproducts p JOIN tblhosting h ON (p.id = h.packageid) WHERE h.id='".$vars['serviceid']."' "));
//        if($r['servertype'] == 'InterNetX_SSL')
//        {
//            include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'InterNetX_SSL.php');
//            global $smarty;
//
//            if($r['configoption5'] == 'THAWTECODESIGNING')
//            {
//                $smarty->_tpl_vars['webservertypes'] = array(
//                    'JAVASOFT'          => 'Javasoft',
//                    'MS_AUTHENTICODE'   => 'MS Authenticode',
//                    'VBA'               => 'VBA',
//                    'ADOBE_AIR'         => 'Adobe Air',
//                    'APPLE'             => 'Apple'
//                );
//            } else
//                $smarty->_tpl_vars['webservertypes'] = InterNetX_SSL_Software();
//
//        }
//    }

    if ($vars['filename'] == 'configuressl') {
        $r = (array) Capsule::table('tblproducts')->join('tblhosting', 'tblhosting.packageid', '=', 'tblproducts.id')->where('tblhosting.id', '=', $vars['serviceid'])->first();

        if ($r['servertype'] == 'InterNetX_SSL') {

            include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'InterNetX_SSL.php');
            //for WHMCS < 7.2
            global $smarty;
            $smarty->tpl_vars['webservertypes']->value = InterNetX_SSL_Software();

            // for WHMCS >= 7.2
            global $smartyvalues;
            $smartyvalues['webservertypes'] = InterNetX_SSL_Software();


            $selectedProduct = Capsule::table('tblproducts')->select('configoption5')->where('name', '=', $vars['certtype'])->first();

            if((!isset($_REQUEST['step']) || $_REQUEST['step'] == 2) && ($selectedProduct->configoption5 == 'GLOBALSIGN_PERSONALSIGN_1' || $selectedProduct->configoption5 == 'GLOBALSIGN_PERSONALSIGN_2' || $selectedProduct->configoption5 == 'GLOBALSIGN_PERSONALSIGN_2_PRO' || $selectedProduct->configoption5 == 'GLOBALSIGN_PERSONALSIGN_2_DEPT'))
            {
                return <<<HTML
    <script type="text/javascript">
        $(document).ready(function(){
           $("#inputCsr").parent().hide();
           $("#inputServerType").parent().hide();
           $(document).find("p").andSelf().filter("p:first").first().hide();
           $(document).find("fieldset").first();
           $("input[name*='Tech']").parent().parent().parent().hide();
           $("p:contains('Technical Contact')").hide();
           $("input[name*='Tech']").val("---");
           $( $(document).find("fieldset").first() ).append( "<div class='form-group'><label class='col-md-4 control-label' for='inputAdditionalField'>Enduser Password</label><div class='col-md-8'><input name='fields[enduser_password]' size='30' value='' type='password'> </div></div>" );
           //$("input[type='checkbox']").prop('checked', true);

        });
    </script>
HTML;
            }
        }
    }
}


function InterNetX_SSL_save($params)
{
    if ($params['servertype'] == 'InterNetX_SSL') {
        if ($params['configoption3'] != htmlspecialchars_decode($params['configoption3'])) {
            Capsule::table('tblproducts')->where('id', '=', (int) $params['pid'])->update(array(
                'configoption3' => htmlspecialchars_decode($params['configoption3'])));
        }
    }
}

function InterNetX_SSL_customfield($params)
{
    if (isset($_SESSION['adminid']) && isset($_REQUEST['action']) && isset($_GET['id']) && isset($_POST['servertype']) && $_POST['servertype'] == 'InterNetX_SSL' && $_REQUEST['action'] == 'generateCustom') {
        ob_clean();
        $numRows = Capsule::table('tblcustomfields')->where('type', '=', 'product')->where('relid', '=', (int) $_GET['id'])->where('fieldname', 'LIKE', 'Domain|%')->count();
        if ($numRows == 0) {
            Capsule::table('tblcustomfields')->insert(array(
                'type' => 'product',
                'relid' => (int) $_GET['id'],
                'fieldname' => 'Domain|Domain',
                'fieldtype' => 'text',
                'required' => 'on',
                'showorder' => 'on'
            ));
            echo "Custom Field has been added";
        } else {
            echo "Custom Field already exists";
        }
        die();
    }
}

add_hook("ClientAreaHeadOutput", 1, "InterNetX_ChangeSelectValue");
add_hook("ProductEdit", 1, "InterNetX_SSL_save");
add_hook("AdminAreaHeaderOutput", 1, "InterNetX_SSL_customfield");
