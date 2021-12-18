<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderMaster extends Model
{
    use HasFactory;

    const PENDING = 0;
    const REJECTED = 1;
    const APPROVED = 2;
    const PROCESSING = 3;
    const SHIPPED = 4;
    const DELIVERED = 5;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'reference_no', 'total_amount', 'paid_amount', 'payment_method', 'order_status'];

    public function addMasterDetails($data)
    {
        OrderMaster::create($data);
    }

    public function scopeDeliveredOrder($query){
        return $query->where('order_status' , '5');
    }

    public function hasReferenceNumberExists($referenceNo)
    {
        return OrderMaster::where('reference_no', $referenceNo)->first();
    }

    public function updateOrderMasterInformation($referenceNo, $totalAmount)
    {
        $result =  OrderMaster::where('reference_no', $referenceNo)->first();

        if ($result) {
            $result->total_amount = $totalAmount;
            $result->save();
        }
    }

    public function currentOrderStatus($referenceNo)
    {
        $result = OrderMaster::where('reference_no', $referenceNo)->first();
        if ($result->order_status == 0) {
            return true;
        }
        return false;
    }
}
