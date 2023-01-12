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

namespace MGModule\InterNetX\Addon\Core\exceptions;

/**
 * ModelesGarden Base Exception
 * @author Michal Czech <michael@modulesgarden.com>
 * @edited Dariusz Bijoś <dariusz.bi@modulesgarden.com>
 */
class Base extends \Exception
{
    private $_token;

    /**
     * Base Contructor
     * @author Michal Czech <michael@modulesgarden.com>
     * @edited Dariusz Bijoś <dariusz.bi@modulesgarden.com>
     * @since 1.0.0
     */
    public function __construct($message, $code, $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->_token = md5(microtime());
    }

    /**
     * Exception Token
     * @author Michal Czech <michael@modulesgarden.com>
     * @edited Dariusz Bijoś <dariusz.bi@modulesgarden.com>
     * @since 1.0.0
     * @ return Base->_token;
     */
    public function getToken()
    {
        return $this->_token;
    }
}