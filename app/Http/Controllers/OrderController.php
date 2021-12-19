<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Requests\OrderStatusRequest;
use App\Jobs\MailSendingJob;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderMaster;
use Illuminate\Http\Request;

class OrderController extends BaseController
{

    public $order;

    /**
     * it resolves the dependancy of Order master class
     * @return object
     */

    public function __construct(Order $orderObj)
    {

        $this->order = $orderObj;
    }

    /**
     * It will return the Order List of logged users
     *
     * @return Illuminate\Support\Facades\Response\Json     *
     */

    public function index()
    {

        return $this->order->getOrderList(new OrderMaster);
    }



    /**
     * It will store new order information
     * @param  \Illuminate\Http\Request  $request
     * @return Illuminate\Support\Facades\Response\Json     *
     */

    public function addOrders(OrderRequest $request)
    {
        $result = $this->order->addOrders($request->all());
        if (!$result) {
            return $this->sendError($this->order->errors, $this->order->errors['message'], $this->order->errors['code']);
        }
        $this->sendNotification();
        return $this->sendResponse([], 'Order Successfully placed', 200);
    }


    /**
     * It will store the modification of an  order in history table and update the order table
     * @param  \Illuminate\Http\Request  $request
     * @return Illuminate\Support\Facades\Response\Json
     *
     */

    public function modifyExistingOrder(Request $request, $referenceNo)
    {

        if (!$this->order->hasReferenceNumberExists($referenceNo, new OrderMaster)) {
            return $this->sendError([], 'Invalid Reference Number', 404);
        }

        $result = $this->order->updateOrder($request->all(), $referenceNo);
        if (!$result) {
            return $this->sendError($this->order->errors, $this->order->errors['message'], $this->order->errors['code']);
        }

        return $this->sendResponse([], 'Order Successfully updated!', 200);
    }

    public function sendNotification()
    {
        dispatch(new MailSendingJob(env('ADMIN_EMAIL')));
    }

    public function modifyExistingOrderStatus(OrderStatusRequest $request, $referenceNo)
    {

        if (!$this->order->hasReferenceNumberExists($referenceNo, new OrderMaster)) {
            return $this->sendError([], 'Invalid Reference Number', 404);
        }

        $result = $this->order->updateOrderStatus($referenceNo,$request->order_status,new OrderMaster);
        if (!$result) {
            return $this->sendError($this->order->errors, $this->order->errors['message'], $this->order->errors['code']);
        }

        return $this->sendResponse([], 'Order status Successfully updated!', 200);

    }
}
