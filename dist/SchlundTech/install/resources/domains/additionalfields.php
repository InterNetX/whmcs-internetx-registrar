<?php

$includePath = implode( DIRECTORY_SEPARATOR, array(ROOTDIR, "modules", "registrars", "SchlundTech", "additionalfields.php") );
if (file_exists($includePath)) include $includePath;