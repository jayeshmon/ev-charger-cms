<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chargers', function (Blueprint $table) {
            $table->id();
			$table->string('chargingstation_id');                       //fk                  
			$table->string('oem_id');                       //fk
			$table->string('charger_model_id');                       //fk
			                      //fk
			$table->string('display_name');                          
			$table->string('commissioned_date');  
			$table->string('charger_latitude');
$table->string('charger_longitude');  	
$table->string('charger_reservation_enabled'); 
 $table->string('tariff_type_id'); 
$table->string('tariff_id');  
$table->string('tax_id');  
$table->string('configuration_url'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chargers');
    }
};
