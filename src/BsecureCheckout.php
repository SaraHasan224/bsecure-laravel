<?php

namespace bSecure\UniversalCheckout;

use bSecure\UniversalCheckout\Controllers\Orders\CreateOrderController;
use bSecure\UniversalCheckout\Controllers\Orders\IOPNController;
use bSecure\UniversalCheckout\Controllers\Orders\OrderStatusUpdateController;

use Illuminate\Support\Facades\Facade;

class BsecureCheckout extends Facade
{

    private $orderPayload = [
      'order_id' => null,
      'customer' => null,
      'products' => null,
      'shipment_charges' => null,
      'shipment_method_name' => null,
      'sub_total_amount' => null,
      'discount_amount' => null,
      'total_amount' => null
    ];


    /*
     *  CREATE ORDER: Set Order Id
    */
    public function setOrderId($orderId)
    {
        try {
            $this->orderPayload['order_id'] = $orderId;
            return $this->orderPayload;
            //code...
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    /*
     *  CREATE ORDER: Set Customer Payload
    */
    public function setCustomer($customerData)
    {
        try {
            $order = new CreateOrderController();
            $customer = $order->_setCustomer($customerData);
            $this->orderPayload['customer'] = $customer;
            return $this->orderPayload;
            //code...
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /*
     *  CREATE ORDER: Set Shipment Details
    */
    public function setShipmentDetails($shipmentData)
    {
        try {
            $order = new CreateOrderController();
            $shipment = $order->_setShipmentDetails($shipmentData);
            $this->orderPayload['shipment_charges'] = $shipment['charges'];
            $this->orderPayload['shipment_method_name'] = $shipment['method_name'];
            return $this->orderPayload;
            //code...
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /*
     *  CREATE ORDER: Set Products Data
    */

    public function setCartItems($productsData)
    {
        try {
            $order = new CreateOrderController();
            $orderItems = $order->_setProductsDataStructure($productsData);

            $this->orderPayload['products'] = $orderItems['products'];
            $this->orderPayload['sub_total_amount'] = $orderItems['sub_total_amount'];
            $this->orderPayload['discount_amount'] = $orderItems['discount_amount'];
            $this->orderPayload['total_amount'] = $orderItems['total_amount'];

            return $this->orderPayload;
            //code...
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /*
     *  CREATE ORDER: Set Order Charges
    */

    public function setOrderCharges($object)
    {
        try {
            $order = new CreateOrderController();
            $orderCharges = $order->_setCharges($object);

            $this->orderPayload['sub_total_amount'] = $orderCharges['sub_total_amount'];
            $this->orderPayload['discount_amount'] = $orderCharges['discount_amount'];
            $this->orderPayload['total_amount'] = $orderCharges['total_amount'];

            return $this->orderPayload;
            //code...
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /*
     *  CREATE ORDER: Create Order using Merchant Access Token from Merchant backend server
    */
    public function createOrder()
    {
        try {
            $order = new CreateOrderController();
            $result = $order->create($this->orderPayload);
            return json_decode($result->getContent(),true);
//            return $result->getContent();
            //code...
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /*
     *  INSTANT ORDER PROCESSING NOTIFICATIONS : Get order status for merchant
    */

    public function orderStatusUpdates($order_ref = null)
    {
        try {
            $customer = new IOPNController();
            $result = $customer->orderStatus($order_ref);
            return json_decode($result->getContent(),true);
            //code...
        } catch (\Throwable $th) {
            throw $th;
        }
    }

}
