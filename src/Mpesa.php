<?php

namespace SilahKosgei\Mpesa;

use SilahKosgei\Mpesa\Helpers\CurlHelper;

class Mpesa
{
    protected $config;
    protected $timestamp;

    public function __construct()
    {
        $this->config = config('mpesa');
        $this->timestamp = now()->format('YmdHis');
    }

    public function initiateStkPush($phone, $amount, $reference, $description, $referenceId)
    {
        $type = $this->config['shortcode_type'] === 'BuyGoods'
            ? 'CustomerBuyGoodsOnline'
            : 'CustomerPayBillOnline';

        $partyB = $this->config['shortcode_type'] === 'BuyGoods'
            ? $this->config['buygoods_till']
            : $this->config['shortcode'];

        $payload = [
            'BusinessShortCode' => $this->config['shortcode'],
            'Password' => $this->generatePassword(),
            'Timestamp' => $this->timestamp,
            'TransactionType' => $type,
            'Amount' => $amount,
            'PartyA' => $phone,
            'PartyB' => $partyB,
            'PhoneNumber' => $phone,
            'CallBackURL' => url($this->config['callback_url'] . '/' . $referenceId),
            'AccountReference' => $reference,
            'TransactionDesc' => $description,
        ];

        $url = $this->baseUrl() . '/mpesa/stkpush/v1/processrequest';

        return CurlHelper::post($url, $payload, $this->accessToken());
    }

    public function lookupPayment($reference)
    {
        $payload = [
            'CheckoutRequestID' => $reference,
            'BusinessShortCode' => $this->config['shortcode'],
            'Password' => $this->generatePassword(),
            'Timestamp' => $this->timestamp,
        ];

        $url = $this->baseUrl() . '/mpesa/stkpushquery/v1/query';

        return CurlHelper::post($url, $payload, $this->accessToken());
    }

    protected function generatePassword()
    {
        return base64_encode($this->config['shortcode'] . $this->config['passkey'] . $this->timestamp);
    }

    protected function accessToken()
    {
        $credentials = base64_encode($this->config['consumer_key'] . ':' . $this->config['consumer_secret']);

        $url = $this->baseUrl() . '/oauth/v1/generate?grant_type=client_credentials';

        $response = CurlHelper::get($url, [
            'Authorization: Basic ' . $credentials
        ]);

        return $response->access_token ?? '';
    }

    protected function baseUrl()
    {
        return $this->config['env'] === 'sandbox'
            ? 'https://sandbox.safaricom.co.ke'
            : 'https://api.safaricom.co.ke';
    }
}
