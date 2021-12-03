<div class="card">
    <div class="card-body">
        <h3 class="card-title">{lang key='domaindnsmanagement'}</h3>

        {include file="$template/includes/alert.tpl" type="info" msg="{lang key='domaindnsmanagementdesc'}"}

        {if $error}
            {include file="$template/includes/alert.tpl" type="error" msg=$error}
        {/if}

        {if $external}
            <div class="text-center px-4">
                {$code}
            </div>
        {else}

            {if $registrar eq "InterNetX" or $registrar eq "SchlundTech" or $registrar eq "DomainRobot"}
			<div style="margin-bottom: 10px; margin-left: 20px;">
			<b>{$LANG.internetxmainip} </b><input type="text" name="zone[main][value]" size="40" value="{$zone->main->value}" />
			<br /><br /><input type="checkbox" name="zone[www_include]" value="1" {if $zone->www_include eq 1} checked {/if}><b> {$LANG.internetxwwwinclude} </b></span>
			</div>
			{/if}

            <form method="post" action="{$smarty.server.PHP_SELF}?action=domaindns">
                <input type="hidden" name="sub" value="save" />
                <input type="hidden" name="domainid" value="{$domainid}" />

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{lang key='domaindnshostname'}</th>
                            <th>{lang key='domaindnsrecordtype'}</th>
                            <th>{lang key='domaindnsaddress'}</th>
                            <th>{lang key='domaindnspriority'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $dnsrecords as $dnsrecord}
                            <tr>
                                <td><input type="hidden" name="dnsrecid[]" value="{$dnsrecord.recid}" /><input type="text" name="dnsrecordhost[]" value="{$dnsrecord.hostname}" size="10" class="form-control" /></td>
                                <td>
                                    <select name="dnsrecordtype[]" class="form-control">
                                        <option value="A"{if $dnsrecord.type eq "A"} selected="selected"{/if}>{lang key="domainDns.a"}</option>
                                        <option value="AAAA"{if $dnsrecord.type eq "AAAA"} selected="selected"{/if}>{lang key="domainDns.aaaa"}</option>
                                        <option value="MX"{if $dnsrecord.type eq "MX"} selected="selected"{/if}>{lang key="domainDns.mx"}</option>
                                        <option value="CNAME"{if $dnsrecord.type eq "CNAME"} selected="selected"{/if}>{lang key="domainDns.cname"}</option>
                                        <option value="TXT"{if $dnsrecord.type eq "TXT"} selected="selected"{/if}>{lang key="domainDns.txt"}</option>
                                        <option value="NS"{if $dnsrecord.type eq "NS"} selected="selected"{/if}>{lang key="domainDnsIX.ns"}</option>
                                        <option value="SRV"{if $dnsrecord.type eq "SRV"} selected="selected"{/if}>{lang key="domainDnsIX.srv"}</option>
                                        <option value="CAA"{if $dnsrecord.type eq "CAA"} selected="selected"{/if}>{lang key="domainDnsIX.caa"}</option>
                                    </select>
                                </td>
                                <td><input type="text" name="dnsrecordaddress[]" value="{$dnsrecord.address}" size="40" class="form-control" /></td>
                                <td>
                                    {if $dnsrecord.type eq "MX"}<input type="text" name="dnsrecordpriority[]" value="{$dnsrecord.priority}" size="2" class="form-control" />{else}<input type="hidden" name="dnsrecordpriority[]" value="N/A" />{lang key='domainregnotavailable'}{/if}
                                </td>
                            </tr>
                        {/foreach}
                        <tr>
                            <td><input type="text" name="dnsrecordhost[]" size="10" class="form-control" /></td>
                            <td>
                                <select name="dnsrecordtype[]" class="form-control">
                                    <option value="A">{lang key="domainDns.a"}</option>
                                    <option value="AAAA">{lang key="domainDns.aaaa"}</option>
                                    <option value="MX">{lang key="domainDns.mx"}</option>
                                    <option value="CNAME">{lang key="domainDns.cname"}</option>
                                    <option value="TXT">{lang key="domainDns.txt"}</option>
                                    <option value="NS">{lang key="domainDnsIX.ns"}</option>
                                    <option value="SRV">{lang key="domainDnsIX.srv"}</option>
                                    <option value="CAA">{lang key="domainDnsIX.caa"}</option>
                                </select>
                            </td>
                            <td><input type="text" name="dnsrecordaddress[]" size="40" class="form-control" /></td>
                            <td><input type="text" name="dnsrecordpriority[]" size="2" class="form-control" /></td>
                        </tr>
                    </tbody>
                </table>

                <p class="text-right text-muted">
                    <small>* {lang key='domaindnsmxonly'}</small>
                </p>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">
                        {lang key='clientareasavechanges'}
                    </button>
                    <button type="reset" class="btn btn-default">
                        {lang key='clientareacancel'}
                    </button>
                </div>

            </form>

        {/if}

    </div>
</div>
