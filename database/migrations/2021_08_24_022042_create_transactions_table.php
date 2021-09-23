<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->text('address')->nullable();
            $table->float('total_price')->default(0);
            $table->float('shipping_price')->default(0);
            $table->enum('status', ['PENDING', 'DELIVERED'])->default('PENDING');
            $table->enum('payment', ['MANUAL', 'TRANSFER'])->default('MANUAL');
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
        Schema::dropIfExists('transactions');
    }
}
