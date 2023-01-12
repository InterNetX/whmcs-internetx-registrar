<?php
/*
 *  * ******************************************************************
 * 
 *    CREATED BY MODULESGARDEN       ->        http://modulesgarden.com
 *    AUTHOR                         ->        Dariusz Bijoś <dariusz.bi@modulesgarden.com>
 *    CONTACT                        ->        contact@modulesgarden.com
 * 
 *   This software is furnished under a license and may be used and copied
 *   only  in  accordance  with  the  terms  of such  license and with the
 *   inclusion of the above copyright notice.  This software  or any other
 *   copies thereof may not be provided or otherwise made available to any
 *   other person.  No title to and  ownership of the  software is  hereby
 *   transferred.
 * 
 *  * ******************************************************************
 */

namespace MGModule\InterNetX\Addon\Models;

/**
 * Description of pollsTable
 *
 * @author Dariusz Bijoś <dariusz.bi@modulesgarden.com>
 */
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
