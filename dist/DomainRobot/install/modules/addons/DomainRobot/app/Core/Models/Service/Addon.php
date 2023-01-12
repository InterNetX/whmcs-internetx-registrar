<?php

namespace MGModule\InterNetX\Addon\Core\Models\Service;

class Addon extends \WHMCS\Service\Addon
{
    public function addon()
    {
        return $this->hasOne('MGModule\InterNetX\Addon\Core\Models\Addons', 'id', 'addonid');
    }
}