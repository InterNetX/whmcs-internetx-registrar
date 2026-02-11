# Installation of the Registrar Module
Hi and welcome to our Registrar Module.
If you are upgrading from an older Version of our Module, please check UPGRADE.md for instructions, new features and (breaking) changes.

## What files to put where
the folder "install" includes the directories you need to copy to your WHMCS installation folder.
They have the same structure you will already find in your WHMCS folder

### The Registrar Module itself
`install/modules/registrars/InterNetX` needs to be copied to `{your-whmcs-folder}/modules/registrars/InterNetX`

### Additional Language Files
`install/lang/overrides` needs to be copied to `{your-whmcs-folder}/lang/overrides`. If these files already exist, you can copy the additional translations from our files to the existing files.

### Additional Domain Fields
Some TLDs (like .it) require additional Domain Fields (e.g. Legal agreements, VAT).
To provide them, WHMCS allows to customize these Fields by adding a file /resources/domains/additionalfields.php. (see https://docs.whmcs.com/Additional_Domain_Fields)
In Order to use the additional Fields specific to this Module, please do the following:

add a file /resources/domains/additionalfields.php with the following content. Or edit the existing file to include the lines below "<?php" to the bottom of the file

```
<?php

// Include Additional Fields from DomainRobot Registrar Module
$includePath = implode( DIRECTORY_SEPARATOR, array(ROOTDIR, "modules", "registrars", "InterNetX", "additionalfields.php") );
if (file_exists($includePath)) include $includePath;
```

### TLD Files
Some of the TLDs we allow you to register, ask you to accept Terms provided as PDF.
Please copy the folder `install/resources/domains/TLDfiles` to `{your-whmcs-folder}/resources/domains/TLDfiles`
Also please Copy the TLDFiles from this directory to /resources/domains/TLDFiles. These Are Documents that get linked. E.g. to "accept the following terms"

### Premium Domains Addon
To sell premium domains, you need to install our premium domains addon alongside the registrar module.

`install/modules/addons/InterNetX` needs to be copied to `{your-whmcs-folder}/modules/addons/InterNetX`
Activate it in the Addon Modules settings of WHMCS and enable premium domains on the Domainpricing page.

### Virtual Nameservers
If you are using virtual nameservers from InterNetX please update /modules/registrars/InterNetX/app/Helpers/Config.php and change the "ns14.net" entries to your own virtual nameserver domain.

### TLD Domain Sync
To allow WHMCS domain sync activate XML-Pricelist feature:
https://en.help.internetx.com/display/APIADDITIONALEN/XML+price+list+guide