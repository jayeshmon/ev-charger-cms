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
		Schema::create('alarms', function (Blueprint $table) {
            $table->id();
			
			$table->string('charger_id');
			$table->string('title');
		$table->string('summary');
		$table->string('ticket_id');
		$table->string('severity');
		$table->string('error_code');
		$table->string('status');
		$table->string('source_component');
		
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
