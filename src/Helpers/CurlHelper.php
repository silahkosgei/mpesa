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
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        logger()->info("GET URL: $url");
        logger()->info("Headers: ", $headers);
        logger()->info("HTTP Code: $httpCode");
        logger()->info("Curl Error: $error");
        logger()->info("Response: $response");

        return json_decode($response);
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
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        logger()->info("HTTP CODE: $httpCode");
        logger()->info("Curl Error: $error");
        logger()->info("Raw response: $response");

        return json_decode($response);
    }
}
