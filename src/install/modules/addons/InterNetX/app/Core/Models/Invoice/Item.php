<?php

namespace MGModule\InterNetX\Addon\Core\Models\Invoice;

class Item extends \Illuminate\Database\Eloquent\Model
{
    const TYPE_HOSTING          =   '';
    const TYPE_HOSTING_DISCOUNT =   'HostingDiscount';
    
    public $timestamps          =   false;
    
    protected $table            =   'tblinvoiceitems';
    
    /**
     * Is hosting?
     * @return type
     */
    public function isService()
    {
        return in_array($this->type, [
            'Hosting',
            'Setup'
        ]);
    }
    
    /*
     * Is hosting discount
     */
    public function isHostingDiscount()
    {
        return $this->type == Item::TYPE_HOSTING_DISCOUNT;
    }
    
    /**
     * Return service object
     */
    public function service()
    {
        /*if(!$this->isService())
        {
            return 0;
        }*/
        
        return $this->hasOne('MGModule\InterNetX\Addon\Core\Models\Service', 'id', 'relid');
    }
    
    /**
     * 
     * @return type
     */
    public function invoice()
    {
        return $this->belongsTo('MGModule\InterNetX\Addon\Core\Models\Invoice', 'invoiceid', 'id');
    }
}