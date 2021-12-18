<?php

namespace App\Console\Commands;

use App\Models\Delivery;
use App\Models\OrderMaster;
use Illuminate\Console\Command;

class OrderShipment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:delivered';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'move all the delivered products in the deliveries table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $deliveredOrderList = OrderMaster::deliveredOrder()->get();

        foreach ($deliveredOrderList as $delivery) {
            Delivery::create([
                'user_id' => $delivery->user_id,
                'reference_no' => $delivery->reference_no,
                'total_amount' => $delivery->total_amount,
                'total_amount' => $delivery->total_amount,
                'paid_amount' => $delivery->paid_amount,
                'payment_method' => $delivery->payment_method,
                'order_status' => $delivery->order_status,
            ]);
            $delivery->delete();
        }
        $this->info('Order shipped');
    }
}
