<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EcomStatusRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jpayment_ecom_order_status_request', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->index()->nullable(false);
            $table->text('request')->nullable(false);
            $table->text('response')->nullable(true);
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
}
