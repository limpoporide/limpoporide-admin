<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('order_id');
            $table->integer('product_id');
            $table->string('unit_price');
            $table->string('product_price');
            $table->string('shipping_charg')->default(0);
            $table->string('coupon_discount')->default(0);
            $table->string('grand_total');
            $table->string('qty');
            $table->integer('address_id');
            $table->smallInteger('status')->default(1)->comment('1-Pending,2-Accecpted, 3-Cancel,4-Complete');
            $table->smallInteger('payment_status')->default(1)->comment('1-pending,2-processed,3-success,4-failed,5-cancel');
            $table->smallInteger('payment_type')->default(1)->comment('1-online,2-cash,3-voucher');
            $table->integer('coupon_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->timestamp('order_date')->useCurrent();
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
        Schema::dropIfExists('orders');
    }
}
