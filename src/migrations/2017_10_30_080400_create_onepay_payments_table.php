<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOnepayPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('onepay_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->string('item_type')->nullable();
            $table->unsignedInteger('item_id')->nullable();
            $table->tinyInteger('status')->default(\NuocGanSoi\LaravelOnepay\Models\OnepayPayment::STATUS_PENDING);
            $table->string('access_code', 8);
            $table->string('currency', 3);
            $table->string('command', 16);
            $table->string('locale', 2);
            $table->string('merchant', 12);
            $table->string('return_url', 64);
            $table->string('version', 2);
            $table->string('amount', 21);
            $table->string('merch_txn_ref', 40)->index();
            $table->string('order_info', 40);
            $table->string('ticket_no', 16);
            $table->string('secure_hash', 64);
            $table->text('url')->nullable();
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
        Schema::dropIfExists('onepay_payments');
    }
}
