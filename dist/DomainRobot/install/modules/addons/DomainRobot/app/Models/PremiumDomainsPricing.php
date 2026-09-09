<?php

namespace MGModule\InterNetX\Addon\Models;


class PremiumDomainsPricing extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'mod_InterNetXtblpremiumpricing';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'relid', 'type', 'currency', '1years', '2years', '3years', '4years', '5years', '6years', '7years', '8years', '9years', '10years'];
}
