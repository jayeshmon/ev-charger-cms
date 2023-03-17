<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.-transactions
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::create('charging_transactions', function (Blueprint $table) {
        $table->id();
		
		$table->string('transaction_id');
		$table->string('user_id');											//Model
		$table->string('charger_id');											//Model
		$table->string('invoice_id');											//Model
		
		$table->string('account_transaction_id');									
		$table->string('meter_start');
		$table->string('meter_end');
		$table->string('units_consumed');
		
		
		
		$table->string('transaction_mode_id');											//Fk
		$table->string('connector_type_id');											//Fk
		$table->string('session_status_id');											//Fk
		
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
