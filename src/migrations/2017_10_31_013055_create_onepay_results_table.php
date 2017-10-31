<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOnepayResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('onepay_results', function (Blueprint $table) {
            $table->increments('id');
            $table->string('addition_data')->nullable();
            $table->string('amount', 21);
            $table->string('command', 16);
            $table->string('currency_code', 3);
            $table->string('locale', 2);
            $table->string('merch_txn_ref', 40);
            $table->string('merchant', 12);
            $table->string('order_info', 40);
            $table->string('transaction_no', 12);
            $table->string('txn_response_code', 64);
            $table->string('version', 2)->nullable();
            $table->string('message', 200)->nullable();
            $table->string('secure_hash', 64);
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
        Schema::dropIfExists('onepay_results');
    }
}
