<h3>{$lang.your_certificate}</h3>
<textarea style="width: 100%; height: 300px" readonly>{if $smarty.get.type == ctr}{$ctr}{else}{$csr}{/if}</textarea>
<br >

<p style="margin-top: 10px">
    <a href="clientarea.php?action=productdetails&id={$serviceid}" class="btn btn-mini"><i class="icon-arrow-left"></i> {$lang.back}</a>
    {if $smarty.get.type == ctr}
        <a href="clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=getcert" class="btn btn-mini btn-info"> {$lang.turn_to_csr}</a>
    {else}
        <a href="clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=getcert&type=ctr" class="btn btn-mini btn-info"> {$lang.turn_to_ctr}</a>
    {/if}
</p>