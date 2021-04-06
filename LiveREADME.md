<p align="center">
  <img src="https://bsecure-dev.s3-eu-west-1.amazonaws.com/dev/react_app/assets/secure_logo.png" width="400px" position="center">
</p>



[![Latest Version on Packagist](https://img.shields.io/packagist/v/bsecure/bsecure-laravel.svg?style=flat-square)](https://packagist.org/packages/bsecure/bsecure-laravel)
[![Latest Stable Version](https://poser.pugx.org/bsecure/bsecure-laravel/v)](//packagist.org/packages/bsecure/bsecure-laravel) 
[![Total Downloads](https://img.shields.io/packagist/dt/bsecure/bsecure-laravel.svg?style=flat-square)](https://packagist.org/packages/bsecure/bsecure-laravel)
[![License](https://poser.pugx.org/bsecure/bsecure-laravel/license)](//packagist.org/packages/bsecure/bsecure-laravel)
[![Build Status](https://scrutinizer-ci.com/g/bSecureCheckout/bsecure-laravel/badges/build.png?b=master)](https://scrutinizer-ci.com/g/bSecureCheckout/bsecure-laravel/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/bSecureCheckout/bsecure-laravel/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/bSecureCheckout/bsecure-laravel/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bSecureCheckout/bsecure-laravel/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/bSecureCheckout/bsecure-laravel/?branch=master)

bSecure Checkout 
=========================
Pakistan's first universal checkout solution that is easy and simple to integrate on your e-commerce store. 

### About bSecure Checkout ##

It gives you an option to enable *universal-login*, *two-click checkout* and accept multiple payment method for your customers, as well as run your e-commerce store hassle free.\
It is built for *desktop*, *tablet*, and *mobile devices* and is continuously tested and updated to offer a frictionless payment experience for your e-commerce store.


### Installation
You can install the package via **composer**

`` composer require bSecure/bsecure-laravel``

**Prerequisites** 

>PHP 7.2.5 and above

**Dependencies**

>"guzzlehttp/guzzle": "^7.2"

## Usage

### Configuration Setup

By following a few simple steps, you can set up your **bSecure Checkout** and **Single-Sign-On**. 

#### Getting Your Credentials

1. Go to [Builders Portal](https://builder.bsecure.pk/)
2. [App Integration](https://builder.bsecure.pk/integration-live) >> Enable keys for the environment  Live / Sandbox 
3. Save & Copy your credentials (Client ID & Client Secret).
4. Please make sure to keep credentials at safe place in your code
5. Select My Stores. And create a new store by clicking on the button.
6. Select Integration Type (Custom Integration)
7. Fill following fields:\
    a. *Store Name* its required in any case\
    b. *Store URL* its required in any case\
    c. *Product detail URL* Required for feature **Manual Orders**\
    d. *Login Redirect URL* Required for feature **Login with bSecure**\
    e. *Checkout Redirect URL* Required for feature **Pay with bSecure**\
    f. *Checkout Order Status webhook* Required for feature **Pay with bSecure**\
    g. *Minimum order amount to ask for CNIC* its optional in any case\
    h. *Minimum cart value to proceed to checkout* its optional in any case\



## bSecure Checkout

Add provider for bSecure checkout in app.php

`` bSecure\UniversalCheckout\CheckoutServiceProvider::class ``

Add alias

`` 'BsecureCheckout' => bSecure\UniversalCheckout\BsecureCheckout::class ``


#### Publish the language file.
  ``php artisan vendor:publish --provider="bSecure\UniversalCheckout\CheckoutServiceProvider"``

It will create a vendor/bSecure folder inside resources/lang folder. If you want to customize the error messages your can overwrite the file.

#### Publish the configuration file
  ``php artisan vendor:publish --provider="bSecure\UniversalCheckout\CheckoutServiceProvider" --tag="config"``

A file (bSecure.php) will be placed in config folder.

```php
return [
  'client_id' => env('BSECURE_CLIENT_ID', ''),
  'client_secret' => env('BSECURE_CLIENT_SECRET',''),

  'environment' => env('BSECURE_ENVIRONMENT'),
  'store_id' => env('BSECURE_STORE_ID'),
];
```

### Examples

#### Create Order
To create an order you should have an order_id, customer and products object parameters that are to be set before creating an order.
##### Create Order Request Params:

###### Product Object:

Products object should be in below mentioned format:


```
"products": [
  {
  "id": "product-id",
  "name": "product-name",
  "sku": "product-sku",
  "quantity": 0,
  "price": 0,
  "sale_price": 0,
  "image": "product-image",
  "description": "product-description",
  "short_description": "product-short-description"
  }
]
```

###### Shipment Object

Shipment object should be in below mentioned format:

>1- If the merchant want his pre-specified shipment method then he should pass shipment method detail in below mentioned format:  

```
"shipment": {
  "charges": "numeric",
  "method_name": "string"
}
```

###### Customer Object

Customer object should be in below mentioned format:

>1- If the customer has already signed-in via bSecure into your system and you have auth-code for the customer you can
just pass that code in the customer object no need for the rest of the fields.

>2- Since all the fields in Customer object are optional, if you don’t have any information about customer just pass the
empty object, or if you have customer details then your customer object should be in below mentioned format:

```
"customer": {
  "name": "string",
  "email": "string",
  "country_code": "string",
  "phone_number": "string",
}
```

#### Create Order
```php
use bSecure\UniversalCheckout\BsecureCheckout;
```

```php
$order = new BsecureCheckout();

$order->setOrderId($orderId);
$order->setCustomer($customer);
$order->setCartItems($products);
$order->setShipmentDetails($shipment);

$result =  $order->createOrder();
return $result;
```

In response createOrder(), will return order expiry, checkout_url, order_reference and merchant_order_id.
```
array (
  'expiry' => '2020-11-27 10:55:14',
  'checkout_url' => 'bSecure-checkout-url',
  'store_url' => 'store-url',
  'merchant_store_name' => 'your-store-name',
  'order_reference' => 'bsecure-reference',
  'merchant_order_id' => 'your-order-id'
) 
```
>If you are using a web-solution then simply redirect the user to checkout_url
```
if(!empty($result['checkout_url']))
return redirect($result['checkout_url']); 
```
>If you have Android or IOS SDK then initialize your sdk and provide order_reference to it
```
if(!empty($result['order_reference']))
return $result['order_reference']; 
```
When order is created successfully on bSecure, you will be redirected to bSecure SDK or bSecure checkout app where you will process your checkout.


#### Callback on Order Placement
Once the order is successfully placed, bSecure will redirect the customer to the url you mentioned in “Checkout
redirect url” in your [environment settings](https://builder.bsecure.pk/) in Partners Portal, with one additional param “order_ref” in the query
string.

#### Order Updates
By using order_ref you received in the "**[Callback on Order Placement](#callback-on-order-placement)**" you can call below method to get order details.

```php
use bSecure\UniversalCheckout\BsecureCheckout;
```

```php
$order_ref = $order->order_ref;

$orderStatusUpdate = new BsecureCheckout();
$result =  $orderStatusUpdate->orderStatusUpdates($order_ref);
return $result;
```

#### Order Status Change Webhook
Whenever there is any change in order status or payment status, bSecure will send you an update with complete
order details (contents will be the same as response of *[Order Updates](https://github.com/bSecureCheckout/bsecure-laravel/tree/master#order-updates)*) on the URL you mentioned in *Checkout Order Status webhook* in your environment settings in Partners Portal. (your webhook must be able to accept POST request).


In response of "**[Callback on Order Placement](#callback-on-order-placement)**" and "**[Order Updates](#order-updates)**" you will recieve complete details of your order in below mentioned format:

```
{
  "status": 200,
  "message": [
    "Request Successful"
  ],
  "body": {
    "merchant_order_id": "your-order-id",
    "order_ref": "bsecure-order-reference",
    "order_type": "App/Manual/Payment gateway",
    "placement_status": "6",
    "payment_status": null,
    "customer": {
      "name": "",
      "email": "",
      "country_code": "",
      "phone_number": "",
      "gender": "",
      "dob": ""
    },
    "payment_method": {
      "id": 5,
      "name": "Debit/Credit Card"
    },
    "card_details": {
      "card_type": null,
      "card_number": null,
      "card_expire": null,
      "card_name": null
    },
    "delivery_address": {
      "country": "",
      "province": "",
      "city": "",
      "area": "",
      "address": "",
      "lat": "",
      "long": ""
    },
    "shipment_method": {
      "id": 0,
      "name": "",
      "description": "",
      "cost": 0
    },
    "items": [
      {
        "product_id": "",
        "product_name": "",
        "product_sku": "",
        "product_qty": ""
      },
    ],
    "created_at": "",
    "time_zone": "",
    "summary": {
      "total_amount": "",
      "sub_total_amount": "",
      "discount_amount": "",
      "shipment_cost": "",
      "merchant_service_charges": ""
    }
  },
  "exception": null
}

```

### Managing Orders and Payments

#### Payment Status

| ID  | Value     | Description                                                                    |
| :-: | :-------- | :----------------------------------------------------------------------------- |
|  0  | Pending   | Order placed. But payment is awaiting for fulfillment by the customer.         |
|  1  | Completed | Order fulfilled, placed and payment has also been received.                    |
|  2  | Failed    | Payment failed or was declined or maximum attempt for payment request reached. |

#### Order Status

| ID  | Value                 | Description                                                                                                                        |
| :-: | :-------------------- | :--------------------------------------------------------------------------------------------------------------------------------  |
|  1  | Created               | Order created by merchant	                                                                                                       |
|  2  | Initiated             | Customer landed on bSecure checkout URL. Order is awaiting fulfillment.                                                            |
|  3  | Placed                | Customer successfully placed the order                                                                                             |
|  4  | Awaiting Confirmation | Customer successfully placed the order, but is awaiting for customer confirmation to authenticate the transaction.                 |
|  5  | Canceled              | Customer cancelled the order at the time of confirmation.                                                                          |
|  6  | Expired               | Order not processed within expected time frame. timeframe                                                                          |
|  7  | Failed                | Max payment attempt reached                                                                                                        |
|  8  | Awaiting Payment      | Customer successfully placed the order, but is payment is due or awaiting payment                                                  |


## bSecure Single Sign On (SSO)


Add provider for bSecure checkout and single-sign-on in app.php

`` bSecure\UniversalCheckout\SSOServiceProvider::class ``

Add alias

`` 'BsecureSSO' => bSecure\UniversalCheckout\BsecureSSO::class ``


### Publish the language file.
   ``php artisan vendor:publish --provider="bSecure\UniversalCheckout\SSOServiceProvider"``

It will create a vendor/bSecure folder inside resources/lang folder. If you want to customize the error messages your can overwrite the file.

### Publish the configuration file
  ``php artisan vendor:publish --provider="bSecure\UniversalCheckout\SSOServiceProvider" --tag="config"``

A file (bSecure.php) will be placed in config folder.

Before using bSecure SSO, you will also need to add credentials for the OAuth services your application utilizes. These credentials should be placed in your config/bSecure.php configuration file. For example:

```

return [
  'client_id' => env('BSECURE_CLIENT_ID', ''),
  'client_secret' => env('BSECURE_CLIENT_SECRET',''),

  'environment' => env('BSECURE_ENVIRONMENT'),
];
```

### Routing

Next, you are ready to authenticate users! You will need two routes: one for redirecting the user to the OAuth provider, and another for receiving the customer profile from the provider after authentication. We will access BsecureSSO using the BsecureSSO Facade:

### Authenticate Client
Client Authentication is of two type sdk and web client validation.

>If you are using a web-solution then use below method

```php
use bSecure\UniversalCheckout\BsecureSSO;

$state = $requestData['state'];

$client = new BsecureSSO();
return $client->authenticateWebClient($state);

```

In response, authenticateWebClient will return redirect_url, then simply redirect the user to redirect_url
```
array (
  "redirect_url": "your-authentication-url"

)
```

>If you are using a sdk-solution then use below method
```php
use bSecure\UniversalCheckout\BsecureSSO;

$state = $requestData['state'];

$client = new BsecureSSO();
return $client->authenticateSDKClient($state);

```

In response, authenticateSDKClient will return request_id, merchant_name and store_url which you have to pass it to your SDK.
```
array (
  "request_id": "your-request-identifier",
  "merchant_name": "builder-company-name",
  "store_url": "builder-store-url"
)
```

### Client Authorization
On Successful Authorization,\
bSecure will redirect to Login redirect url you provided when setting up environment in Partners portal, along
with two parameters in query string: **code** and **state**
```
eg: https://my-store.com/sso-callback?code=abc&state=xyz
```
code recieved in above callback is cutsomer's auth_code which will be further used to get customer profile.

#### Verify Callback
Verify the state you received in the callback by matching it to the value you stored in DB before sending the client authentication
request, you should only proceed if and only if both values match.

### Get Customer Profile
Auth_code recieved from **Client Authorization** should be passed to method below to get customer profile. 


```php
use bSecure\UniversalCheckout\BsecureSSO;

$auth_code = $requestData['auth_code'];

$client = new BsecureSSO();
return $client->customerProfile($auth_code);

```

In response, it will return customer name, email, phone_number, country_code, address book.
```
array (
    'name' => 'customer-name',
    'email' => 'customer-email',
    'phone_number' => 'customer-phone-number',
    'country_code' => customer-phone-code,
    'address' => 
        array (
          'country' => '',
          'state' => '',
          'city' => '',
          'area' => '',
          'address' => '',
          'postal_code' => '',
        ),
)
```
### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Contributions

**"bSecure – Your Universal Checkout"** is open source software.
