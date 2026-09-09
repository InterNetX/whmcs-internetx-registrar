<?php

namespace MGModule\InterNetX\Addon\Models;


class PremiumDomainsClasses extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'mod_InterNetXtblpremiumclasses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'class_name'];
}
