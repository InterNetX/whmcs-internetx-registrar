{**********************************************************************
 * Customization Services by ModulesGarden.com
 * Copyright (c) ModulesGarden, INBS Group Brand, All Rights Reserved
 * (2014-03-05)
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
 **********************************************************************}

{**
 * @author Paweł Kopeć <pawelk@modulesgarden.com>
 *}

 <div class="card">
    <div class="card-body">
        <h3 class="card-title">{$LANG.internetxjsonchangeowner}</h3>

            <p>{$LANG.internetxjsonchangeownerdesc}</p>
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
                  <div class="center80">
                        <table  class="table table-striped table-framed">
                              <thead>
                                    <tr>
                                          <th colspan="2" style="text-align: center !important;">{$LANG.internetxjsonchangeownercontacts}</th>
                                    </tr>
                              </thead>
                              <tbody>
                                    <tr>
                                          <td style="text-align: right;">OwnerC:</td>
                                          {*<td><input type="text" size="35" name="domainContacts[ownerc]" value="{$domainContacts.ownerc}"></td>*}
                                          <td><select class="form-control" name='domainContacts[ownerc]'>{$contacts2}</select></td>
                                    </tr>
                              </tbody>
                        </table>
                  </div>

            <p class="text-center">
                  <input type="submit" value="{$LANG.clientareasavechanges}" class="btn btn-primary" /> <input type="reset" value="{$LANG.clientareacancel}" class="btn btn-default" />
            </p>
            </form>
      </div>
</div>

