{* * ********************************************************************
 * internetx registrar module. Product developed. (2014-02-21)
 * *
 *
 *  CREATED BY MODULESGARDEN       ->       http://modulesgarden.com
 *  CONTACT                        ->       contact@modulesgarden.com
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
 * ******************************************************************** *}

{**
* @author Paweł Kopeć <pawelk@modulesgarden.com>
*}
<div class="card">
    <div class="card-body">
        <h3 class="card-title">{$LANG.internetxdomainforwarding}</h3>
            <p> {$LANG.internetxdomainforwardingdesc}</p>
            <br/>
            <div class="alert alert-block alert-info">
                  <p>Domain Name: <strong>{$domain}</strong></p>
            </div>
            {if $info}
                  <div class="alert alert-success">
                        <p class="bold textcenter">{$info}</p>
                  </div>
            {/if}
            {if $errors}
                  <div class="alert alert-error">
                        <p class="bold textcenter">
                        {foreach from=$errors item=err}
                              {$err}<br/>
                        {/foreach}
                        </p>
                  </div>
            {/if}
            <form method="post" style="margin-top:30px;">

                  <table  style="width:100%;" class="table table-striped table-framed">
                        <thead>
                              <tr>
                                    <th class="textcenter">Domain/Subdomain</th>
                                    <th class="textcenter">{$LANG.internetxdomainforwardingtype}</th>
                                    <th class="textcenter">{$LANG.internetxdomainforwardingto}</th>
                                    <th></th>
                              </tr>
                        </thead>
                        <tbody>
                              {foreach from=$redirects key=key item=redirect}
                                    <tr>
                                          <td class="textcenter"><input class="form-control" type="text" name="domainForwarding[{$key}][source]" value="{$redirect.source}"></td>
                                          <td class="textcenter">
                                                <select name="domainForwarding[{$key}][mode]" class="domainForwarding_mode form-control" rel="{$key}">
                                                      <option value="HTTP"{if $redirect.mode eq "HTTP"} selected="selected"{/if}>HTTP</option>
                                                      <option value="HTTPS"{if $redirect.mode eq "HTTPS" || $redirect.mode eq ""} selected="selected"{/if}>HTTPS</option>
                                                      <option value="FRAME"{if $redirect.mode eq "FRAME"} selected="selected"{/if}>FRAME</option>
                                                </select>
                                          </td>
                                          <td class="textcenter">
                                                <input type="text" name="domainForwarding[{$key}][target]" value="{$redirect.target}" class="form-control" >
                                                <input type="hidden" name="domainForwarding[{$key}][id]" value="{$redirect.source}" /></td>
                                          <td class="textcenter"><div id="domainForwarding_tilte_{$key}" {if $redirect.mode != "FRAME"}style="display:none;" {/if}> {$LANG.internetxdomainforwardingtitle}  <input type="text" name="domainForwarding[{$key}][title]" value="{$redirect.title}" class="form-control" ></div></td>

                                    </tr>
                              {/foreach}
                              <tr>
                                    <th class="textcenter" colspan="4">Add New Redirect:</th>
                              </tr>
                              <tr>
                                    <td class="textcenter"><input type="text" name="newDomainForwarding[source]" value="{$domain}" class="form-control" ></td>
                                    <td class="textcenter">
                                          <select name="newDomainForwarding[mode]" id="newDomainForwarding_mode" class="form-control" >
                                                <option value="HTTPS">HTTPS</option>
                                                <option value="HTTP">HTTP</option>
                                                <option value="FRAME">FRAME</option>
                                          </select>
                                    </td>
                                    <td class="textcenter"><input type="text" name="newDomainForwarding[target]" class="form-control" ></td>
                                    <td class="textcenter"><div id="newDomainForwarding_tilte">  <input type="text" class="form-control"  name="newDomainForwarding[title]" placeholder="{$LANG.internetxdomainforwardingtitle}" value=""></div></td>
                              </tr>
                        </tbody>
                  </table>

            <p class="text-center">
                  <input type="submit" value="{$LANG.clientareasavechanges}" class="btn btn-primary" /> <input type="reset" value="{$LANG.clientareacancel}" class="btn btn-default" />
            </p>


            </form>
      </div>
</div>
{literal}
<script type="text/javascript">
   $(document).ready(function() {
        $("#newDomainForwarding_tilte").hide();
        $('#newDomainForwarding_mode').change(function(){
              var selected;
              selected =$("#newDomainForwarding_mode option:selected").val();
              if(selected=="FRAME")
                    $("#newDomainForwarding_tilte").show();
              else
                    $("#newDomainForwarding_tilte").hide();
        });
        $('.domainForwarding_mode').change(function(){
              var id = $(this).attr('rel');
              var selected;
              selected =$("option:selected", this).val();
              if(selected=="FRAME")
                    $("#domainForwarding_tilte_"+id).show();
              else
                    $("#domainForwarding_tilte_"+id).hide();
        });


    });
</script>
{/literal}