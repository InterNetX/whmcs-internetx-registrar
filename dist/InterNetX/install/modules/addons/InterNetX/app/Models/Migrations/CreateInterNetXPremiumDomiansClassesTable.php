<?php

namespace MGModule\InterNetX\Addon\Models\Migrations;

use WHMCS\Database\Capsule;


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
