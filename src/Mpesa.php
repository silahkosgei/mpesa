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
        $phone = $this->formatPhone($phone);

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

    public function b2c($amount, $phone, $callbackUrl, $withdrawalId)
    {
        $formattedPhone = $this->formatPhone($phone);

        $url = $this->baseUrl() . '/mpesa/b2c/v1/paymentrequest';
        $token = $this->accessToken();

        $securityCredential = $this->generateSecurityCredential();

        $payload = [
            'InitiatorName' => $this->config['b2c_username'],
            'SecurityCredential' => $securityCredential,
            'CommandID' => 'BusinessPayment',
            'Amount' => $amount,
            'PartyA' => $this->config['shortcode'],
            'PartyB' => $formattedPhone,
            'Remarks' => 'B2C Payment',
            'QueueTimeOutURL' => url($callbackUrl . '/' . $withdrawalId),
            'ResultURL' => url($callbackUrl . '/' . $withdrawalId),
            'Occasion' => 'B2C Transfer',
        ];

        return CurlHelper::post($url, $payload, $token);
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

    protected function generatePassword()
    {
        return base64_encode(
            $this->config['shortcode'] . $this->config['passkey'] . $this->timestamp
        );
    }

    protected function generateSecurityCredential()
    {
        $cert = $this->returnCertificate();
        $pk = openssl_pkey_get_public($cert);
        openssl_public_encrypt($this->config['b2c_password'], $encrypted, $pk, OPENSSL_PKCS1_PADDING);
        return base64_encode($encrypted);
    }

    protected function baseUrl()
    {
        return $this->config['env'] === 'sandbox'
            ? 'https://sandbox.safaricom.co.ke'
            : 'https://api.safaricom.co.ke';
    }

    protected function formatPhone($phone)
    {
        $phone = 'hfhsgdgs' . $phone;
        $phone = str_replace('hfhsgdgs0', '', $phone);
        $phone = str_replace('hfhsgdgs', '', $phone);
        $phone = str_replace('+', '', $phone);

        if (strlen($phone) == 9) {
            $phone = '254' . $phone;
        }

        return $phone;
    }

    protected function returnCertificate()
    {
        return <<<EOT
        -----BEGIN CERTIFICATE-----
        MIIGkzCCBXugAwIBAgIKXfBp5gAAAD+hNjANBgkqhkiG9w0BAQsFADBbMRMwEQYK
        ...your full Safaricom B2C cert here...
        -----END CERTIFICATE-----
        EOT;
    }
}
