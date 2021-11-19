## Additional Domain Fields
Some TLDs (like .it) require additional Domain Fields (e.g. Legal agreements, VAT).
To provide them WHMCS allows to customize these Fields by adding a file /resources/domains/additionalfields.php. (see https://docs.whmcs.com/Additional_Domain_Fields)
In Order to use the additional Fields specific to this Module, please do the following:

add a file /resources/domains/additionalfields.php with the following content

```
<?php

// Include Additional Fields from DomainRobot Registrar Module
$includePath = implode( DIRECTORY_SEPARATOR, array(ROOTDIR, "modules", "registrars", "InterNetXJson", "additionalfields.php") );
if (file_exists($includePath)) include $includePath;
```

Also please Copy the TLDFiles from this directory to /resources/domains/TLDFiles. These Are Documents that get linked. E.g. to "accept the following terms"
