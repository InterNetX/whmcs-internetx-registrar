<script type="text/javascript">
var datepickerformat = "";
$(document).ready(function(){
    $("input[name^='register'], input[name^='transfer'], input[name^='renew']").each(function(i){
         if($(this).val() < 0)
         {
             $(this).hide();
         }
    });
    $(".datepick").datepicker({
        dateFormat: "dd/mm/yy",
        showOn: "button",
        buttonImage: "images/showcalendar.gif",
        buttonImageOnly: true,
        showButtonPanel: true,
        showOtherMonths: true,
        selectOtherMonths: true
    });

$(".pricingToggle").click(function() {
    var data = $(this).attr("data");

    if ($(this).is(":checked")) {
        $("input[name='register" + data + "']").val("0.00").show();
        $("input[name='transfer" + data + "']").val("0.00").show();
        $("input[name='renew" + data + "']").val("0.00").show();
    } else {
        $("input[name='register" + data + "']").val("0.00").hide();
        $("input[name='transfer" + data + "']").val("0.00").hide();
        $("input[name='renew" + data + "']").val("0.00").hide();
    }
});});
function backToPremiumClasses(){
    $("#modalAjaxPremiumLoader").show();
    $.ajax({
            type: "POST",
            data: {
                loadNewModal : "yes"},
            success: function(data) {
                $("#modalAjax .modal-content").html(data);
            }
        });
}
</script>
<div class="modal-header panel-heading" style="text-align: center">
    <h4 class="modal-title" id="premiumlevels">Configure Premium Domain Pricing</h4>
</div>
<div class="modal-body" id="modalAjaxBody">
  <div class="alert alert-warning text-center">
  Premium domain cost prices are fetched in real-time from database.
  </div>
  <form id="premiumPricingForm" method="post">
    {*<p align="center">Pricing Slab for <select name="selectedcugroupid" onchange="location.href='/client/admin/configdomains.php?action=editpricing&id=55&selectedcugroupid='+this.value;" class="form-control select-inline">
    <option value="0">Default Base Slab</option>
    </select> <button type="button" class="btn btn-info" data-toggle="tooltip" data-placement="bottom" title="Pricing slabs can be created to offer groups of clients alternative pricing. Slabs are tied to client groups, so you need to set pricing for the default base slab, and then optionally for any of the client groups you want to offer custom pricing to."><i class="fa fa-question"></i></button></p>*}

    <table class="datatable" style="margin-left: 8px;">
        <tr>
            <th class="domain-pricing-head"></th>
            <th class="domain-pricing-head">Currency</th>
            <th class="domain-pricing-head">Enable</th>
            <th class="domain-pricing-head">Register</th>
            <th class="domain-pricing-head">Transfer</th>
            <th class="domain-pricing-head">Renewal</th>
        </tr>
        {for $i=1 to 1}
            <tr class="domain-pricing-row"><td class="field-highlight text-nowrap text-center"><b>{$i} Year</b></td>
            <td class="text-center">{$currencies}**</td>
            {if $pricingData}
            <td class="text-center">
                <input type="checkbox" name="enable[1][{$i}]" class="pricingToggle" data="[1][{$i}]" class="form-control"{foreach from=$pricingData key=$key item=$pricing}{if $pricing.currency == 1 && $pricing.{$i|cat:'years'} > 0}checked="true"{/if}{/foreach}/>
            </td>
            <td class="text-center">
                <input type="text" name="register[1][{$i}]" {foreach from=$pricingData key=$key item=$pricing}{if $pricing.currency == 1 && $pricing.type == "domainregister"} value="{$pricing.{$i|cat:'years'}}" {/if}{/foreach} size="10" class="form-control" />
            </td>
            <td class="text-center">
                <input type="text" name="transfer[1][{$i}]" {foreach from=$pricingData key=$key item=$pricing}{if $pricing.currency == 1 && $pricing.type == "domaintransfer"} value="{$pricing.{$i|cat:'years'}}" {/if}{/foreach} size="10" class="form-control" />
            </td>
            <td class="text-center">
                <input type="text" name="renew[1][{$i}]" {foreach from=$pricingData key=$key item=$pricing}{if $pricing.currency == 1 && $pricing.type == "domainrenew"} value="{$pricing.{$i|cat:'years'}}" {/if}{/foreach} size="10" class="form-control" />
            </td>
            {else}
            <td class="text-center">
                <input type="checkbox" name="enable[1][{$i}]" class="pricingToggle" data="[1][{$i}]" class="form-control" />
            </td>
            <td class="text-center">
                <input type="text" name="register[1][{$i}]" value="-1.00" size="10" style="display:none;" class="form-control" />
            </td>
            <td class="text-center">
                <input type="text" name="transfer[1][{$i}]" value="-1.00" size="10" style="display:none;" class="form-control" />
            </td>
            <td class="text-center">
                <input type="text" name="renew[1][{$i}]" value="-1.00" size="10" style="display:none;" class="form-control" />
            </td>
            {/if}
        </tr>
        {/for}
    </table>
</form>
<div id="additionalInfo" style="text-align: left">
    <h6>** prices in other currencies will be automatically recalculated by WHMCS and this module</h6>
</div>
 </div>
<div class="modal-footer panel-footer">
    <div id="modalFooterLeft" style="text-align: left">
        {*<h6>** prices in other currencies will be automatically recalculated by WHMCS and this module</h6>*}
    </div>

  <div class="pull-left loader" id="modalAjaxPremiumLoader" style="display: none;">
      <i class="fa fa-circle-o-notch fa-spin"></i> Loading...
  </div>
  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
  <button type="button" class="btn btn-default" id="backButton" onclick="backToPremiumClasses();">Back</button>
  <button type="button" id="savePremumClasses" class="btn btn-primary btn-inverse" onclick="savePremiumPricing({$relid});">Save</button>
</div>