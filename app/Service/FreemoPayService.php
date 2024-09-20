<?php

namespace App\Service;

class FreemoPayService
{
    private string $baseUrl;
    private string $userName;
    private string $passwordUser;

    public function __construct(string $baseUrl, string $userName, string $passwordUser)
    {
        $this->baseUrl = $baseUrl;
        $this->userName = $userName;
        $this->passwordUser = $passwordUser;
    }

    private function sendRequest(string $url, string $method, array $data = [], string $token = null)
    {
        $curl = curl_init();

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        ];

        if ($method === 'POST') {
            $options[CURLOPT_CUSTOMREQUEST] = 'POST';
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
        } elseif ($method === 'GET') {
            $options[CURLOPT_CUSTOMREQUEST] = 'GET';
        }

        $headers = ['Content-Type: application/json'];

        if ($token) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        $options[CURLOPT_HTTPHEADER] = $headers;

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response);
    }

    private function getAccessToken()
    {
        return $this->sendRequest($this->baseUrl . '/api/v1/app/token', 'POST', [
            'user' => $this->userName,
            'password' => $this->passwordUser
        ]);
    }

    private function generatePaymentReference()
    {
        $timestamp = time();
        $randomDigits = mt_rand(0, 9999);
        $reference = 'FM' . str_pad($timestamp, 10, '0') . str_pad($randomDigits, 4, '0');

        return $reference;
    }

    public function initPayment(string $payer, float $amount)
    {
        $tokenObj = $this->getAccessToken();

        if (!isset($tokenObj->token))
            return null;

        $token = $tokenObj->token;
        $externalId = $this->generatePaymentReference();

        return $this->sendRequest($this->baseUrl . '/api/v1/payment', 'POST', [
            "payer" => $payer,
            "amount" => $amount,
            "external_id" => $externalId
        ], $token);
    }

    public function getPaymentStatus($reference)
    {
        $tokenObj = $this->getAccessToken();

        if (!isset($tokenObj->token))
            return null;

        $token = $tokenObj->token;

        return $this->sendRequest($this->baseUrl . '/api/v1/payment/' . $reference, 'GET', [], $token);
    }
}
