<?php

return [
    'client_id' => env('BSECURE_CLIENT_ID', ''),
    'client_secret' => env('BSECURE_CLIENT_SECRET', ''), //use 'production' for live orders and 'sandbox' for testing orders. When left empty or `null` the sandbox environment will be used
    'environment' => env('BSECURE_ENVIRONMENT'),
    'store_id' => env('BSECURE_STORE_ID'),   //If store id is not mentioned your orders will be marked against your default store
];
