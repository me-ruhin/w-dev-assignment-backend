<?php

use App\Models\OrderMaster;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('reference_no');
            $table->string('total_amount');
            $table->string('paid_amount')->default(0);
            $table->string('payment_method')->nullable();
            $table->enum('order_status',[OrderMaster::PENDING,OrderMaster::REJECTED,OrderMaster::APPROVED,OrderMaster::PROCESSING,OrderMaster::SHIPPED,OrderMaster::DELIVERED])->default(OrderMaster::PENDING);
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
        Schema::dropIfExists('deliveries');
    }
}
