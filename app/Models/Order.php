<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['reference_no', 'product_id', 'price', 'qty'];
    public $errors = [];
    public $orderNumber;


    /**
     * It will store the order data in storage
     * @param  $datas array
     * @return Illuminate\Support\Facades\Response\Json
     *
     */


    public function addOrders($datas)
    {
        DB::beginTransaction();
        try {
            $total = 0;
            for ($key = 0; $key < count($datas['product_id']); $key++) {
                $attributes['product_id'] = $datas['product_id'][$key];
                $attributes['price'] = $this->findTheProductPrice($datas['product_id'][$key])->price;
                $attributes['qty'] = $datas['qty'][$key];
                $total += $this->findTheProductPrice($datas['product_id'][$key])->price;
                $attributes['reference_no'] = $this->generateReferenceNumber();
                $this->storeOrder($attributes);
            }
            $this->addRecordToOrderMaster(['user_id' => auth()->user()->id, 'reference_no' => $this->generateReferenceNumber(), 'total_amount' => $total], new OrderMaster);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->errors['message'] = $e->getMessage();
            $this->errors['code'] = $e->getCode();
            return false;
        }
    }

    public function storeOrder($data)
    {
        Order::create($data);
    }

    /**
     * It will store the Total history data in OrderMaster
     * @param  $data array instanec of OrderMaster class
     * @return Illuminate\Support\Facades\Response\Json
     *
     */

    public function addRecordToOrderMaster($data, OrderMaster $orderMasterObj)
    {
        return $orderMasterObj->addMasterDetails($data);
    }


    public function findTheProductPrice($productId)
    {

        return Product::select('price')->find($productId);
    }

    public function generateReferenceNumber()
    {
        if (!empty($orderNumber)) {
            return $orderNumber;
        }
        return $this->orderNumber = numberGenerator('order');
    }


    public function updateOrder($datas, $referenceNo)
    {
        if (!$this->currentOrderStatus($referenceNo, new OrderMaster)) {
            $this->errors['message'] = 'You cannot modify your order';
            $this->errors['code'] = 400;
            return false;
        }

        DB::beginTransaction();
        try {
            $this->addOrderInformationToOrderHistory($datas, $referenceNo, new OrderHistory);
            $this->updateOrderInformation($datas, $referenceNo);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->errors['message'] = $e->getMessage();
            $this->errors['code'] = $e->getCode();
            return false;
        }
    }



    public function addOrderInformationToOrderHistory($datas, $referenceNo, OrderHistory $orderHistoryObj)
    {


        foreach ($datas['id'] as $key => $data) {
            $data = $this->hasAnyModificationInOrder($datas['id'][$key], $datas['qty'][$key]);
            if ($data) {
                $orderHistoryObj->storeOrderHistory(['order_id' => $datas['id'][$key], 'reference_no' => $referenceNo, 'product_id' => $data->product_id, 'qty' => $data->qty]);
            }
        }
    }

    public function hasAnyModificationInOrder($orderId, $qty)
    {

        $result = Order::find($orderId);

        if ($result && ($result->qty == $qty)) {
            return false;
        }
        return $result;
    }

    public function updateOrderInformation($datas, $referenceNo)
    {
        $total = 0;
        foreach ($datas['id'] as $key => $data) {
            $result = $this->hasOrderIdExists($datas['id'][$key]);
            Log::info(json_encode($result));
            if ($result) {
                $result->qty = $datas['qty'][$key];
                $total += (float)$result->price * $datas['qty'][$key];
                $result->save();
            }
        }
        $this->updateTheOrderMasterInformation($referenceNo, $total, new OrderMaster());
    }

    public function hasOrderIdExists($orderNo)
    {

        return Order::where('id', $orderNo)->first();
    }


    public function hasReferenceNumberExists($referenceNo, OrderMaster $orderMasterObj)
    {
        return $orderMasterObj->hasReferenceNumberExists($referenceNo);
    }

    public function updateTheOrderMasterInformation($referenceNo, $totalAmount, OrderMaster $orderMasterObj)
    {
        return $orderMasterObj->updateOrderMasterInformation($referenceNo, $totalAmount);
    }

    public function currentOrderStatus($referenceNo, OrderMaster $orderMasterObj)
    {

        return $orderMasterObj->currentOrderStatus($referenceNo);
    }
}
