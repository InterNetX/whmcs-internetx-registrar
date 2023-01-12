<?php

$includePath = implode( DIRECTORY_SEPARATOR, array(ROOTDIR, "modules", "registrars", "DomainRobot", "additionalfields.php") );
if (file_exists($includePath)) include $includePath;