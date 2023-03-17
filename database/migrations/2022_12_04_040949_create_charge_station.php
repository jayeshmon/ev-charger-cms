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
        Schema::create('charge_stations', function (Blueprint $table) {
            $table->id();
			$table->string('station_name');
			$table->string('address');
			$table->string('latitude');
			$table->string('longitude');
			$table->string('owner_id');									//Model
			$table->string('service_provider_id');						//Model
			$table->string('city');	
			$table->string('state');
			$table->string('country');
			$table->string('pincode');
			$table->string('operating_hours_start');
			$table->string('operating_hours_end');
			$table->string('commissioned_on');
			$table->string('amenities');								//Model		
			$table->string('status');
			$table->string('users_id'); 								//created by user
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
        Schema::dropIfExists('charge_station');
    }
};
