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
        //
		Schema::create('charging_reservations', function (Blueprint $table) {
		$table->id();
			
		$table->string('user_id');											//Model
		$table->string('charger_id');											//Model
		$table->string('invoice_id');											//Model
		
		$table->string('reservation_duration');									
		$table->string('reservation_date');
		$table->string('reservation_expiry_date');
		
		
		$table->string('account_transaction_id');											//Fk
		$table->string('reservation_status_id');											//Fk
		
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
        //
    }
};
