<?php

namespace MGModule\InterNetX\Addon\Core\Models\Service;


class Server extends \Illuminate\Database\Eloquent\Model
{
    protected $table   = 'tblservers';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'ipaddress', 'hostname', 'username', 'password', 'secure'];

}