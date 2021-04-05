<?php

namespace bSecure\UniversalCheckout\Controllers\Orders;

use App\Http\Controllers\Controller;


//Models
use bSecure\UniversalCheckout\Models\Order;

//Helper
use bSecure\UniversalCheckout\Helpers\AppException;
use bSecure\UniversalCheckout\Helpers\ApiResponseHandler;

//Facade
use Validator;

//Instant Order Processing Notification
class IOPNController extends Controller
{

    /**
     * Author: Sara Hasan
     * Date: 26-November-2020
     */
    public function orderStatus($order_ref)
    {
        try {
            $orderResponse = Order::getOrderStatus($order_ref);

            if($orderResponse['error'])
            {
                return ApiResponseHandler::failure($orderResponse['message'],$orderResponse['exception']);
            }else{
                $response = $orderResponse['body'];

                return ApiResponseHandler::success($response, trans('bSecure::messages.order.status.success'));
            }
        } catch (\Exception $e) {
            return ApiResponseHandler::failure(trans('bSecure::messages.order.status.failure'), $e->getTraceAsString());
        }
    }
}
