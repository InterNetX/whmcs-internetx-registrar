{* * ********************************************************************
 * InterNetX registrar module. Product developed. (2014-02-21)
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
                                                      <option value="frame"{if $redirect.mode eq "frame"} selected="selected"{/if}>frame</option>
                                                      <option value="http"{if $redirect.mode eq "http"} selected="selected"{/if}>http</option>
                                                      <option value="https"{if $redirect.mode eq "https" || $redirect.mode eq ""} selected="selected"{/if}>https</option>
                                                </select>
                                          </td>
                                          <td class="textcenter">
                                                <input type="text" name="domainForwarding[{$key}][target]" value="{$redirect.target}" class="form-control" >
                                                <input type="hidden" name="domainForwarding[{$key}][id]" value="{$redirect.id}" /></td>
                                          <td class="textcenter"><div id="domainForwarding_tilte_{$key}" {if $redirect.mode != "frame"}style="display:none;" {/if}> {$LANG.internetxdomainforwardingtitle}  <input type="text" name="domainForwarding[{$key}][title]" value="{$redirect.title}" class="form-control" ></div></td>

                                    </tr>
                              {/foreach}
                              <tr>
                                    <td class="textcenter"><input type="text" name="newDomainForwarding[source]" value="{$domain}" class="form-control" ></td>
                                    <td class="textcenter">
                                          <select name="newDomainForwarding[mode]" id="newDomainForwarding_mode" class="form-control" >
                                                <option value="frame">frame</option>
                                                <option value="http">http</option>
                                                <option value="https">https</option>
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

        $('#newDomainForwarding_mode').change(function(){
              var selected;
              selected =$("#newDomainForwarding_mode option:selected").val();
              if(selected=="frame")
                    $("#newDomainForwarding_tilte").show();
              else
                    $("#newDomainForwarding_tilte").hide();
        });
        $('.domainForwarding_mode').change(function(){
              var id = $(this).attr('rel');
              var selected;
              selected =$("option:selected", this).val();
              if(selected=="frame")
                    $("#domainForwarding_tilte_"+id).show();
              else
                    $("#domainForwarding_tilte_"+id).hide();
        });


    });
</script>
{/literal}