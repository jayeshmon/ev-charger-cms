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
        Schema::create('oems', function (Blueprint $table) {
            $table->id();
			$table->string('name');
			$table->string('website');
			$table->string('address');
			$table->string('supplier_name');
			$table->string('supplier_address');
			$table->string('phone_no');
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
        Schema::dropIfExists('oems');
    }
};
