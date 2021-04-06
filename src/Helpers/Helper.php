<?php


namespace bSecure\UniversalCheckout\Helpers;

use bSecure\UniversalCheckout\Models\Merchant;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Helper
{
    public static function apiRequest($method, $url, $queryParams = [], $body = [], $headers = [], $contentType = 'json', $returnWithStatusCode = false)
    {
        $response = [];

        try {
            if (is_array($queryParams) && count($queryParams) > 0) {
                $url .= '?' . http_build_query($queryParams);
            }

            $payload = [
                $contentType => $body,
                'headers' => $headers,
                'http_errors' => false,
                'timeout' => 30,
                'connect_timeout' => 30
            ];

            $client = new Client();
            $curlResponse = $client->request($method, $url, $payload);

            if ($returnWithStatusCode) {
                $response['code'] = $curlResponse->getStatusCode();
                $response['content'] = json_decode($curlResponse->getBody()->getContents(), true);
            } else {
                $response = json_decode($curlResponse->getBody()->getContents(), true);
            }
        } catch (RequestException $e) {
//            AppException::log($e);
        } catch (Exception $e) {
//            AppException::log($e);
        } finally {
            return $response;
        }
    }

    /**
     * Author: Sara Hasan
     * Date: 10-November-2020
     */
    static function getAccessToken($data)
    {
        $accessToken = null;

        $http = new Client();
        $authUrl = Constant::AUTH_SERVER_URL . Constant::API_ENDPOINTS['oauth'];

        $storeId =  array_key_exists('store_id',$data) ? $data['store_id'] : null;
        $clientId = !empty($storeId) ? $data['client_id'].':'.$storeId : $data['client_id'];

        $response = $http->post($authUrl, [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_id' => $clientId,
                'client_secret' => $data['client_secret'],
                'scope' => "",
            ],
        ]);

        $result = json_decode((string)$response->getBody("access_token"), true);

        if (isset($result['status']) && $result['status'] == Constant::HTTP_RESPONSE_STATUSES['success']) {

            $merchantEnvironmentCheck = config('bSecure.environment') ?? 'sandbox';

            if ($merchantEnvironmentCheck == $result['body']['environment']) {
                $accessToken = isset($result['body']['access_token']) ? $result['body']['access_token'] : null;
                return ['client_id' => '', 'error' => false, 'accessToken' => $accessToken];
            } else {
                return ['client_id' => '', 'error' => true, 'message' => trans('bSecure::messages.client.environment.invalid')];
            }
        }
    }


    /**
     * Author: Sara Hasan
     * Date: 10-November-2020
     */
    static function createOrder($merchantAccessToken, $orderPayload)
    {
        $method = 'POST';

        $url = Constant::AUTH_SERVER_URL . Constant::API_ENDPOINTS['create_order'];

        $headers = ['Authorization' => 'Bearer ' . $merchantAccessToken];

        $result = Helper::apiRequest($method, $url, [], $orderPayload, $headers, 'form_params');

        if (isset($result['status']) && $result['status'] == Constant::HTTP_RESPONSE_STATUSES['success']) {
            $response = ['error' => false, 'body' => $result['body']];
        } else {
            $response = ['error' => true, 'body' => $result];
        }
        return $response;
    }


    /**
     * Author: Sara Hasan
     * Date: 10-November-2020
     */
    static function orderStatus($merchantAccessToken, $order_ref)
    {
        $method = 'POST';

        $url = Constant::AUTH_SERVER_URL . Constant::API_ENDPOINTS['order_status'];

        $headers = ['Authorization' => 'Bearer ' . $merchantAccessToken];

        $result = Helper::apiRequest($method, $url, [], $order_ref, $headers, 'form_params');

        if (isset($result['status']) && $result['status'] == Constant::HTTP_RESPONSE_STATUSES['success']) {
            $response = ['error' => false, 'body' => $result['body']];
        } else {
            $response = ['error' => true, 'body' => $result];
        }
        return $response;
    }


    /**
     * Author: Sara Hasan
     * Date: 10-November-2020
     */
    static function manualOrderStatusUpdate($merchantAccessToken, $payload)
    {
        $method = 'POST';

        $url = Constant::AUTH_SERVER_URL . Constant::API_ENDPOINTS['manual_order_status_update'];

        $headers = ['Authorization' => 'Bearer ' . $merchantAccessToken];

        $result = Helper::apiRequest($method, $url, [], $payload, $headers, 'form_params');

        if (isset($result['status']) && $result['status'] == Constant::HTTP_RESPONSE_STATUSES['success']) {
            $response = ['error' => false, 'body' => $result['body']];
        } else {
            $response = ['error' => true, 'body' => $result];
        }
        return $response;
    }

    /**
     * Author: Sara Hasan
     * Date: 26-November-2020
     */
    public static function verifyClient($ssoPayload)
    {
        try {
            $client_response = null;

            $http = new Client();
            $authUrl = Constant::AUTH_SERVER_URL . Constant::API_ENDPOINTS['verify_client'];

            $response = $http->post($authUrl, [
                'form_params' => $ssoPayload
            ]);

            $result = json_decode((string)$response->getBody("access_token"), true);

            if (isset($result['status']) && $result['status'] == Constant::HTTP_RESPONSE_STATUSES['success']) {
                $response = ['error' => false, 'body' => $result['body']];
            } else {
                $response = ['error' => true, 'body' => $result];
            }
            return $response;
        } catch (Exception $e) {
            return ['error' => true, 'message' => trans('bSecure::messages.sso_sco.failure'), 'exception' => $e->getTraceAsString()];
        }
    }


    /**
     * Author: Sara Hasan
     * Date: 26-November-2020
     */
    public static function customerProfile($ssoCustomerProfile)
    {
        $merchantToken = Merchant::getMerchantAccessToken();

        if ($merchantToken['error']) {
            return ['error' => true, 'message' => $merchantToken['message']];
        } else {
            $merchantAccessToken = $merchantToken['body'];
            // Call Create Order API
            $response = Helper::getCustomerProfile($merchantAccessToken, $ssoCustomerProfile);

            if ($response['error']) {
                return ['error' => true, 'message' => $response['body']['message']];
            } else {
                return $response;
            }
        }

    }


    /**
     * Author: Sara Hasan
     * Date: 26-November-2020
     */
    public static function getCustomerProfile($merchantAccessToken, $ssoCustomerProfile)
    {
        $method = 'POST';

        $url = Constant::AUTH_SERVER_URL . Constant::API_ENDPOINTS['customer_profile'];

        $headers = ['Authorization' => 'Bearer ' . $merchantAccessToken];

        $result = Helper::apiRequest($method, $url, [], $ssoCustomerProfile, $headers, 'form_params');

        if (isset($result['status']) && $result['status'] == Constant::HTTP_RESPONSE_STATUSES['success']) {
            $response = ['error' => false, 'body' => $result['body']];
        } else {
            $response = ['error' => true, 'body' => $result];
        }
        return $response;

    }

}

