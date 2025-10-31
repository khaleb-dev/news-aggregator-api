<?php

namespace App\Http\Services\Integrations;

/**
 * Base Implementation for API Integrations
 */
class BaseImpl
{
    public function __construct()
    {}

    public function exec(string $url, string $method = 'GET', mixed $data = []): mixed
    {
        $headers = [
            "Cache-Control: no-cache",
            "Content-Type: application/json",
            "Accept: application/json",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36",
            //"api-key: " . $apiKey,
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        if (strtoupper($method) === 'POST') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif (strtoupper($method) === 'PUT') {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return ['error' => $err];
        }

        return json_decode($response, true);
    }

    public function prepUrl(string $baseUrl, string $endpoint): string
    {
        $url = rtrim($baseUrl, '/') . '/' . ltrim($endpoint, '/');
        return $url;
    }
}
