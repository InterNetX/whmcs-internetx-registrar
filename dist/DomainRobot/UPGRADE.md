# Upgrade / Migrate from older Plugin Versions
The new module makes use of our JSON Api to support multiple new features, while beeing mostly backwards compatible.

## Setup Process
Please remove the existing directory "/modules/registrars/InterNetX" completely or rename it.
After that upload the new modules files from the zip.

## Domain Registrar Configuration
Please update the API server in your WHMCS registrar configuration to show the new API url:
https://api.autodns.com/v1

## New Features
### Zone Template
You can now define Templates for the Zones created for new domains, by editing the file "app/templates/zone.json"
The Format is json and our documentation can be found here: https://help.internetx.com/display/APIXMLDE/Zone+Create
You can use the following placeholder in the zone.json template:

{{config_ip}}  => The IP set in the Registrar Module config.
{{config_mx}}  => The MX set in the Registrar Module config.
{{domain}}     => The domain beeing created (register, transfer)
{{nameservers}} => The Nameservers provided during registration.

Also see "app/templates/zone.example.json" for more options, or contact us if you have special requirements.

### Transfer Sync
The Transfer Sync is something that WHMCS runs periodically for all pending Domain Transfers. It is invoked by the default cron, and can be configured using the automation settings of WHMCS.
The old Module did not trigger the "TransferCompleted" Webhook of WHMCS. The "TransferCompleted" will send an E-Mail about the successfull domain Transfer. The new module now triggers this hook, and your clients will be informed about the successfull transfer. You can disable this within WHMCS E-Mail settings if you want to keep things quiet. But at least you now have the option.

## Breaking Changes
### MX Configuration
In Registrar Module Configuration (/admin/configregistrars.php), we've historically provided the field "MX Record". While the description said "MX record (mailserver). Enter the complete domain host name of the mailserver." it actually supported both of these variants:
- A) single subdomains like "mail"
    resulting in a "mail.example.com A entry" AND a "mail.example.com. MX Entry"
- B) full hostnames ending with a dot like "mail.example.com."
    resulting in just a MX Entry like "mail.example.com."

So we encourage you to check what Variant you are currently using.

Option A) is supported by the new module out of the box. As you could check in "app/templates/zone.json", by providing a subdomain string like "mail" we will create the matching A record and set mail.{domain}. as MX Entry for the registered Domain.

If you want to use Option B, e.g. you have an external Mail Server you want to use as MX Entry for all created Domains: Please change the resourceRecords definition in "app/templates/zone.json" to the following:
```
{
"action": "{{nameserver_action}}",
"main": {
    "address": "{{config_ip}}",
    "ttl": 86400
},
"wwwInclude": true,
"resourceRecords": [
    {
        "name": "",
        "ttl": 86400,
        "type": "MX",
        "value": "{{config_mx}}"
    }
]
```
And make sure the MX Record in the module Config is a full hostname + ending with a dot.

More examples can be found in the zone.example.json file.

### Additional Domain Fields
The additional domain fields are now defined directly within the module.
#### Cleanup
If it exists edit the file resources/domains/additionalfields.php and remove the following lines. They where used to include "InterNetX_additionaldomainfields.php" and are no longer necessary nor working.
```
/**
 * InternetX REGISTRAR ADDITIONAL PARAMETERS
 * This file will load proper additional parameters for norid registrar domain module
 */
$filename = ROOTDIR . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'domains' . DIRECTORY_SEPARATOR .'InterNetX_additionaldomainfields.php';
if (file_exists($filename)) {
    include($filename);
} else {
    die('Can not load InterNetX_additionaldomainfields.php File error: resources/domains/additionaldomainfields.php');
}
```
and replace them with the new include:
```
// Include Additional Fields from DomainRobot Registrar Module
$includePath = implode( DIRECTORY_SEPARATOR, array(ROOTDIR, "modules", "registrars", "DomainRobot", "additionalfields.php") );
if (file_exists($includePath)) include $includePath;
```

Delete "resources/domains/InterNetX_additionaldomainfields.php" as it is no longer needed.

### Hooks
Hooks are now defined directly within the module.
#### Cleanup
Please remove the old file "includes/hooks/InterNetX_hooks.php" from your whmcs folder.

Please remove the old file "includes/hooks/delete_InterNetX_hooks.php" from your whmcs folder.
