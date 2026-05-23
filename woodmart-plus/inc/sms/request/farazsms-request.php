<?php

if (!defined('ABSPATH')) {
    exit;
}

class FarazSMSRequest
{
    private string $apiKey;
    private string $baseUrl = 'https://api2.farazsms.com';

    public function __construct(string $apiKey)
    {
        $this->apiKey = trim($apiKey);
    }

    public function sendPattern(string $to, string $from, string $patternCode, array $inputData): array
    {
        $url = trailingslashit($this->baseUrl) . 'ws/v1/otp/send';

        $payload = [
            'code'   => $patternCode,
            'mobile' => $to,
            'sender' => $from,
            'input_data' => $inputData,
        ];

        $response = wp_remote_post($url, [
            'headers' => [
                'accept' => 'application/json',
                'Content-Type' => 'application/json',
                'apikey' => $this->apiKey,
            ],
            'body' => wp_json_encode($payload),
            'timeout' => 20,
        ]);

        if (is_wp_error($response)) {
            return [
                'success' => false,
                'message' => $response->get_error_message(),
                'raw' => null,
            ];
        }

        $statusCode = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        return [
            'success' => $statusCode >= 200 && $statusCode < 300,
            'message' => $data['message'] ?? '',
            'raw' => $data,
            'status_code' => $statusCode,
        ];
    }
}
