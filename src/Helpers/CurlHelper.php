<?php

namespace SilahKosgei\Mpesa\Helpers;

class CurlHelper
{
    public static function post($url, $data, $token)
    {
        return self::request($url, $data, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ]);
    }

    public static function get($url, array $headers = [])
    {
        return self::request($url, [], $headers, false);
    }

    protected static function request($url, $data = [], $headers = [], $post = true)
    {
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POST => $post,
            CURLOPT_POSTFIELDS => $post && !empty($data) ? json_encode($data) : null,
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response);
    }
}
