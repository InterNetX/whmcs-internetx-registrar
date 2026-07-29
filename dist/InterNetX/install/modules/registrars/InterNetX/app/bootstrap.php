<?php

// Load without prepend so WHMCS core's PSR interface versions win the autoloader race.
// Composer registers with prepend=true by default; that causes a Fatal Error when WHMCS's
// bundled zbateson/stream-decorators (which implements psr/http-message 1.x) is loaded in
// the same request after our vendor has already registered the 2.x interface with its
// incompatible ?int return type on getSize().
$loader = require(__DIR__ . '/../vendor/autoload.php');
if ($loader instanceof \Composer\Autoload\ClassLoader) {
    $loader->unregister();
    spl_autoload_register([$loader, 'loadClass'], true, false);
}
