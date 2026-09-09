<?php

namespace MGModule\InterNetX\Addon\Models\Migrations;

use WHMCS\Database\Capsule;


class CreateInterNetXPremiumDomiansPricingTable
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public static function up()
    {
        if (!Capsule::schema()->hasTable('mod_InterNetXtblpremiumpricing')) {
            Capsule::schema()->create('mod_InterNetXtblpremiumpricing',
                function ($table) {
                    $table->increments('id');
                    $table->integer('relid');
                    $table->string('type');
                    $table->integer('currency');
                    $table->float('1years');
                    $table->float('2years');
                    $table->float('3years');
                    $table->float('4years');
                    $table->float('5years');
                    $table->float('6years');
                    $table->float('7years');
                    $table->float('8years');
                    $table->float('9years');
                    $table->float('10years');
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
        Capsule::schema()->dropIfExists('mod_InterNetXtblpremiumpricing');
    }
}
