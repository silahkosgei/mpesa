<?php

return [
    'env' => env('MPESA_ENV', 'sandbox'),
    'shortcode' => env('MPESA_SHORTCODE'),
    'buygoods_till' => env('MPESA_BUY_GOODS_TILL'),
    'shortcode_type' => env('MPESA_SHORTCODE_TYPE', 'PayBill'),
    'passkey' => env('MPESA_PASSKEY'),
    'consumer_key' => env('MPESA_CONSUMER_KEY'),
    'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
    'callback_url' => env('MPESA_CALLBACK_URL', '/api/complete-payment'),
];
