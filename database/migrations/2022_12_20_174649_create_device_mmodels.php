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
        Schema::create('device_models', function (Blueprint $table) {
            $table->id();
			$table->string("model_no");
			$table->string("oem_id");
			$table->string("connector_type_id");
			$table->string("max_kwh");
			$table->string("no_of_slots");
			$table->string("slots");
			$table->string("device_type_id");
			$table->string("device_class");
			
			$table->string("warrenty");
			$table->string("description");
			$table->string("manufacturer_details");
			
			
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
        Schema::dropIfExists('device_mmodels');
    }
};
