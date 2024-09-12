<?php

namespace App\Application\Service\Http;

class CurlClient
{
    public function postRequest(string $url, string $data): string|false
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer OGE4Mjk0MTc0YjdlY2IyODAxNGI5Njk5MjIwMDE1Y2N8c3k2S0pzVDg='));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $responseData = curl_exec($ch);

        if (curl_errno($ch)) {
            $errorMessage = curl_error($ch);
            curl_close($ch);
            throw new \Exception('cURL error: ' . $errorMessage);
        }

        curl_close($ch);
        return $responseData;
    }
}