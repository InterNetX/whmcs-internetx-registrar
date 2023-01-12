<?php

namespace MGModule\InterNetX\Addon\Core\Models;

class Invoice extends \WHMCS\Billing\Invoice 
{
    public function calculateSubtotal()
    {
        $subtotal   =   0;
        foreach($this->items as $item)
        {
            if($item->amount > 0)
            {
                $subtotal += $item->amount;
            }
        }
        
        return $subtotal;
    }
    /**
     * Get related services
     * @return type
     */
    public function services()
    {
        return $this->hasMany('MGModule\InterNetX\Addon\Core\Models\Invoice\Item', 'invoiceid', 'id')
                ->where(function($query){
                    $query->where('type', 'Hosting')
                    ->orWhere('type', 'Setup');
                });
    }
    
    /**
     * 
     * @return int
     */
    public function getItemsTypes()
    {
        $types  =   [];
        
        foreach($this->items as $item)
        {
            if(!$item->isService())
            {
                continue;
            }
            
            $type = $item->service->getType();
            if(!isset($types[$type]))
            {
                $types[$type] = 0;
            }
            
            $types[$type]++;
        }
        
        return $types;
    }
    
    public function isUpgradeOrder()
    {
        return true;
    }
    
    public function items()
    {
        return $this->hasMany('MGModule\InterNetX\Addon\Core\Models\Invoice\Item', 'invoiceid', 'id')
                ->orderByRaw('ABS(notes) ASC')
                ->orderBy('id');
    }
    
    /**
     * Return true if invoice is trial
     * @return type
     * @todo DELETE ME!
     */
    public function isTrial()
    {
        return $invoice->paymentmethod == 'trial';
    }
    
    /**
     * 
     * @return boolean
     */
    public function isCustomInvoice()
    {
        foreach($this->items as $item)
        {
            if($item->type || $item->relid) 
            {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Get order
     * @return type
     */
    public function order()
    {
        return $this->hasOne('MGModule\InterNetX\Addon\Core\Models\Order', 'invoiceid', 'id');
    }
    
    public static function formatItems(\Illuminate\Database\Eloquent\Collection $collection)
    {
        $items      =   [];
        $last       =   null;
        
        foreach($collection->toArray() as $item)
        {
            if($item['type'] == 'Hosting')
            {
                $items['P'.$item['relid']]  =   $item;
            }
            elseif((strpos($item['type'], 'MGP') !== false || $item['type'] == 'HostingDiscount')  && $item['relid']) 
            {
                isset($items['P'.$item['relid']]['discountItems']) ?: $items['P'.$item['relid']]['discountItems'] = [];
                $items['P'.$item['relid']]['discountItems'][] = $item;
            }
            else
            {
                $items[\uniqid()]   =   $item;
            }
        }

        return $items;
    }
    
    public function formatItemsForEmail()
    {
        $items  =   self::formatItems($this->items);
        $html   =   '';
        getCurrency($this->userid);
        
        foreach($items as $item)
        {
            $html   .=  $item['description'].' '.customFormatCurrency($item['amount']).'<br />';
            
            if(!empty($item['discountItems']))
            {
                foreach($item['discountItems'] as $discountItem)
                {
                    $html   .=  $discountItem['description'].' '.customFormatCurrency($discountItem['amount']).'<br />';
                }
            }
        }
        
        return $html;
    }
    /**
     * Get invoice total discount
     * @return type
     */
    public function getTotalDiscount()
    {
        $totalDiscount  =   '';
        
        foreach($this->items as $item)
        {
            if(stripos($item->type, 'MGP_') !== false || $item->type == 'HostingDiscount')
            {
                $totalDiscount += $item->amount;
            }
        }
        
        return $totalDiscount;
    }
    
    /**
     * Delete service from specified invoice
     * @param type $serviceId
     */
    public function deleteService($serviceId)
    {
        \MGModule\InterNetX\Addon\Core\Models\Invoice\Item::where('invoiceid', $this->id)
            ->where('relid', $serviceId)
            ->whereIn('type', [
                'Hosting',
                'Setup'
            ])
            ->delete();
    }
    
    /**
     * Recalculate invoice total
     */
    public function recalculate()
    {
        updateInvoiceTotal($this->id);
    }
    
    public function getUrl()
    {
        global $CONFIG;
        
        $url = $CONFIG['SystemURL'].'/invoice/'.$this->id;
        
        return $url;
    }
    
    
    public function getLink()
    {
        $url = $this->getUrl();
        return '<a href="'.$url.'">'.$url.'</a>';

    }
}