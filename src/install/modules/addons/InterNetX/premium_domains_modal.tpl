<div class="modal-header panel-heading">
    <h4 class="modal-title" id="premiumlevels">Configure Premium Domain Level</h4>
</div>
<div class="modal-body" id="modalAjaxBody">
  <div class="alert alert-warning text-center">
  Premium domain cost prices are fetched in real-time from database.
  </div>
  <form id="premiumClassForm">
      <div class="table-responsive">
          <table class="table">
              <tbody><tr>
                  <th>Premium Domain class</th><th></th><th></th>
              </tr>
    {if $premiumClasses}
    {foreach from=$premiumClasses key=$key item=$value}
    <tr>
      <td>
          <input class="form-control to-amount" name="to[{$value.id}]" value="{$value.class_name}" data-toggle="tooltip" data-placement="top" data-trigger="manual" title="The pricing class must be unique" type="text">
      </td>
      <td>
          <div class="input-group" style="width: 100%;">
              <input class="form-control" value="Configure" type="button" onclick="replaceModalBodyWithPricing({$value.id});">
          </div>
      </td>
      <td><a href="#" class="btn btn-sm" onclick="deleteClass({$value.id}); return false;"><i class="fa fa-minus-circle text-danger"></i></a></td>
    </tr>
    {/foreach}
    {/if}
    <tr>
      <td>
          <input class="form-control to-amount" name="to[0]" id="test" placeholder="e.g class 1" data-toggle="tooltip" data-placement="top" data-trigger="manual" title="The pricing level must be unique" type="text">
      </td>
      <td>
          <div class="input-group" style="width: 100%;">
              <input class="form-control" value="Configure" type="button" disabled="true">
          </div>
      </td>
      <td><a href="#" onclick="return false;" class="btn btn-sm premium-delete" data-pricing-id="0" disabled="true"><i class="fa fa-minus-circle text-danger"></i></a></td>
  </tr>
  <tr>
      <td>
          <input class="form-control to-amount" name="to[1]" placeholder="e.g class 2" data-toggle="tooltip" data-placement="top" data-trigger="manual" title="The pricing level must be unique" type="text">
      </td>
      <td>
          <div class="input-group" style="width: 100%;">
              <input class="form-control" value="Configure" type="button" disabled="true">
          </div>
      </td>
      <td><a href="#" onclick="return false;" class="btn btn-sm premium-delete" data-pricing-id="0" disabled="true"><i class="fa fa-minus-circle text-danger"></i></a></td>
  </tr>
  <tr>
      <td>
          <input class="form-control to-amount" name="to[2]" placeholder="e.g class 2" data-toggle="tooltip" data-placement="top" data-trigger="manual" title="The pricing level must be unique" type="text">
      </td>
      <td>
          <div class="input-group" style="width: 100%;">
              <input class="form-control" value="Configure" type="button" disabled="true">
          </div>
      </td>
      <td><a href="#" onclick="return false;" class="btn btn-sm premium-delete" data-pricing-id="0" disabled="true"><i class="fa fa-minus-circle text-danger"></i></a></td>
  </tr>
  <tr>
      <td>
          <input class="form-control to-amount" name="to[3]" placeholder="e.g class 2" data-toggle="tooltip" data-placement="top" data-trigger="manual" title="The pricing level must be unique" type="text">
      </td>
      <td>
          <div class="input-group" style="width: 100%;">
              <input class="form-control" value="Configure" type="button" disabled="true">
          </div>
      </td>
      <td><a href="#" onclick="return false;" class="btn btn-sm premium-delete" data-pricing-id="0" disabled="true"><i class="fa fa-minus-circle text-danger"></i></a></td>
  </tr>
          </tbody>
        </table>
      </div>
  </form>
 </div>
<div class="modal-footer panel-footer">
  <div id="modalFooterLeft"></div>
  <div class="pull-left loader" id="modalAjaxPremiumLoader" style="display: none;">
      <i class="fa fa-circle-o-notch fa-spin"></i> Loading...
  </div>
  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
  <button type="button" id="savePremumClasses" class="btn btn-primary btn-inverse" onclick="savePremium();">Save</button>
</div>