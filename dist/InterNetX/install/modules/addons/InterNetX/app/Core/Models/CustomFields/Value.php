<?php

namespace MGModule\InterNetX\Addon\Core\Models\CustomFields;

class Value extends \Illuminate\Database\Eloquent\Model
{
    public $timestamps = false;
    protected $table = 'tblcustomfieldsvalues';

    protected function customField()
    {
        return $this->hasOne('MGModule\InterNetX\Addon\Core\Models\CustomField', 'id', 'fieldid')
                ->where('type', 'product');
    }
}