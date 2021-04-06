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


class CreateOrderController extends Controller
{

    protected $validationRule = [
      'order_id' => 'order_id',
      'customer' => 'customer',
      'products' => 'products',
    ];



    /**
     * Author: Sara Hasan
     * Date: 10-November-2020
     */
    public function create($orderData)
    {
        try {
            $orderResponse = Order::createMerchantOrder($orderData);
            if($orderResponse['error'])
            {
                return ApiResponseHandler::failure($orderResponse['message'],$orderResponse['exception']);
            }else{
                $response = $orderResponse['body'];
                return ApiResponseHandler::success($response, trans('bSecure::messages.order.success'));
            }
        } catch (\Exception $e) {
            return ApiResponseHandler::failure(trans('bSecure::messages.order.failure'), $e->getTraceAsString());
        }
    }


    public function _setCharges($object)
    {
        $orderData['sub_total_amount'] = array_key_exists('sub_total_amount',$object ) ? $object['sub_total_amount'] : null;
        $orderData['discount_amount'] = array_key_exists('discount_amount',$object ) ? $object['discount_amount'] : null;
        $orderData['total_amount'] = array_key_exists('total_amount',$object ) ? $object['total_amount'] : null;
        return $orderData;
    }

    public function _setProductsDataStructure($products)
    {
        $orderData['products'] = $products;
        return $orderData;
    }

    public function _setCustomer($customerData)
    {
        $customer = [];
        if(!empty($customerData))
        {
            $auth_code = array_key_exists('auth_code',$customerData) ? $customerData['auth_code'] : '' ;

            if( !empty( $auth_code ) )
            {
                $customer = [
                  "auth_code" => $auth_code,
                ];;
            }
            else{
                $customer = [
                  "country_code" => array_key_exists('country_code',$customerData) ? $customerData['country_code'] : '',
                  "phone_number" => array_key_exists('phone_number',$customerData) ? $customerData['phone_number'] : '',
                  "name" => array_key_exists('name',$customerData) ? $customerData['name'] : '',
                  "email" => array_key_exists('email',$customerData) ? $customerData['email'] : '',
                ];
            }
        }

        return $customer;
    }

    public function _setShipmentDetails($shipmentData)
    {
        $shipmentDetail = [
          "charges" => '',
          "method_name" => '',
        ];
        if(!empty($shipmentData))
        {
            $shipmentDetail['charges'] = array_key_exists('charges',$shipmentData) ? $shipmentData['charges'] : '';
            $shipmentDetail['method_name'] = array_key_exists('method_name',$shipmentData) ? $shipmentData['method_name'] : '';
        }
        return  $shipmentDetail;
    }
}
