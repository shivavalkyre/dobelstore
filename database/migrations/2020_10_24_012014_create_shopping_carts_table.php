<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShoppingCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shopping_carts', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('seller_id');
            $table->integer('product_id');
            $table->integer('qty');
            $table->integer('is_checkout')->default(0);
            $table->integer('is_paid')->default(0);
            $table->integer('from')->default(0);
            $table->integer('to')->default(0);
            $table->integer('weight')->default(0);
            $table->string('courier')->nullable();
            $table->string('courier_type')->nullable();
            $table->integer('amount')->default(0);
            $table->integer('courier_amount')->default(0);
            $table->integer('subtotal_amount')->default(0);
            $table->timestamps();
            $table->integer('is_deleted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shopping_carts');
    }
}
