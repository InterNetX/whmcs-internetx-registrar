<?php

$includePath = implode( DIRECTORY_SEPARATOR, array(ROOTDIR, "modules", "registrars", "InterNetX", "additionalfields.php") );
if (file_exists($includePath)) include $includePath;