<?php

namespace MGModule\InterNetX\Addon\Core\Models;

class Client extends \WHMCS\User\Client   
{
    /**
     * Key - value pair array
     * @return array
     */
    public function getClientCustomFields()
    {
        $pdo    =   \Illuminate\Database\Capsule\Manager::connection()->getPdo();
        $q      =   $pdo->prepare("
            SELECT
                f.fieldname,
                v.value
            FROM
                tblcustomfields f
            INNER JOIN
                tblcustomfieldsvalues v
            ON
                f.id = v.fieldId
            WHERE
                    f.type = 'client'
                AND
                    v.relid = ?
        ");
        
        $q->execute([
            $this->id
        ]);
        
        return $q->fetchAll(\PDO::FETCH_KEY_PAIR);
    }
    
    /**
     *
     * @return boolean
     */
    public function isAntriFraudDisabled()
    {
        $fields =   $this->getClientCustomFields();
        
        return isset($fields['Disable Antifraud']) && $fields['Disable Antifraud'] == 'on';
    }
    
    public function services()
    {
        return $this->hasMany('MGModule\InterNetX\Addon\Core\Models\Service', 'userid', 'id');
    }
    
    public function getActiveServiceTypes($olderBy = null)
    {
        $types      = [];
        $services   = $this->services()->where('domainstatus', 'Active');
        
        if($olderBy)
        {
            $services->where('regdate', '<', $olderBy);
        }
        
        foreach($services->get() as $service)
        {
            $type = $service->getType();
            if(!isset($types[$type]))
            {
                $types[$type] = 0;
            }
            
            $types[$type]++;
        }
        
        return $types;
    } 
}