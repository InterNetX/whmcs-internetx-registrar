<?php

namespace MGModule\InterNetX\Addon\Core\Models;

class Order extends \Illuminate\Database\Eloquent\Model
{
    public $timestamps          =   false;
    
    protected $table            =   'tblorders';
    
    public function client()
    {
        return $this->hasOne('MGModule\InterNetX\Addon\Core\Models\Client', 'id', 'userid');
    }
    
    public function invoice()
    {
        return $this->hasOne('MGModule\InterNetX\Addon\Core\Models\Invoice', 'id', 'invoiceid');
    }
    
    /**
     * Set order as trial
     * @param type $trial
     */
    public function setTrial($trial = false)
    {
        $orderdata              =   unserialize($this->orderdata);
        $orderdata['isTrial']   =   $trial;
        
        $this->orderdata        =   serialize($orderdata);
    }
    
    /**
     * Return true if order is trial
     * @return type
     */
    public function isTrial()
    {
        $orderdata              =   unserialize($this->orderdata);
        
        return !empty($orderdata['isTrial']);
    }
    
    /**
     * 
     * @param type $disabled
     */
    public function setCancellationRequestDisabled($disabled = true)
    {
        $orderdata                                      =   unserialize($this->orderdata);
        $orderdata['isCancelaltionRequestDisabled']     =   $disabled;
        
        $this->orderdata                                =   serialize($orderdata);
    }
    
    /**
     * 
     * @return type
     */
    public function isCancellationRequestDisabled()
    {
        $orderdata              =   unserialize($this->orderdata);
        
        return isset($orderdata['isCancelaltionRequestDisabled']) ? $orderdata['isCancelaltionRequestDisabled'] : null;
    }
}