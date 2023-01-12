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

namespace MGModule\InterNetX\Addon\Models\Migrations;

use WHMCS\Database\Capsule;

/**
 * Description of CreateInterNetXpollsTable
 *
 * @author Dariusz Bijoś <dariusz.bi@modulesgarden.com>
 */
class CreateInterNetXPremiumDomiansClassesTable
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public static function up()
    {
        if (!Capsule::schema()->hasTable('mod_InterNetXtblpremiumclasses')) {
            Capsule::schema()->create('mod_InterNetXtblpremiumclasses',
                function ($table) {
                    $table->increments('id');
                    $table->string('class_name');
                });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public static function down()
    {
        Capsule::schema()->dropIfExists('mod_InterNetXtblpremiumclasses');
    }
}
