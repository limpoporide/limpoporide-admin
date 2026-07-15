<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_histories', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->nullable();
            $table->string('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('user_email')->nullable();
            $table->string('user_mobile')->nullable();
            $table->string('user_address')->nullable();
            $table->string('product_name')->nullable();
            $table->string('unit_price')->nullable();
            $table->string('product_price')->nullable();
            $table->string('shipping_charg')->nullable();
            $table->string('coupon_discount')->nullable();
            $table->string('grand_total')->nullable();
            $table->string('status')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('order_date')->nullable();
            $table->timestamp('expact_delivery_date')->nullable();
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
        Schema::dropIfExists('order_histories');
    }
}
