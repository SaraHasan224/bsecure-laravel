<?php

namespace bSecure\UniversalCheckout\Models;

use bSecure\UniversalCheckout\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    /**
     * Author: Sara Hasan
     * Date: 10-November-2020
     */
    public static function getMerchantAccessToken()
    {
        $merchantClientId = config('bSecure.client_id');
        $merchantClientSecret = config('bSecure.client_secret');

        $merchantAppCredentials = ClientApp::verifyAppCredentials($merchantClientId, $merchantClientSecret);
        $merchantAppCredentials['store_id'] = config('bSecure.store_id');

        if (empty($merchantAppCredentials)) {
            return ['error' => true, 'message' => trans('bSecure::messages.client.invalid')];
        } else {
            if (!empty($merchantAppCredentials['client_id'])) {
                // Get Merchant Access Token
                $merchantToken = Helper::getAccessToken($merchantAppCredentials);

                if ($merchantToken['error']) {
                    return ['error' => true, 'message' => $merchantToken['message']];
                } else {
                    return ['error' => false, 'body' => $merchantToken['accessToken']];
                }

            } else if ($merchantAppCredentials['error']) {
                return $merchantAppCredentials;
            } else {
                return ['error' => true, 'message' => trans('bSecure::messages.general.failed')];
            }
        }
    }
}
