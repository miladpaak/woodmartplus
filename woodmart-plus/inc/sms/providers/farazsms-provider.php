<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once dirname(__DIR__) . '/request/farazsms-request.php';

class farazsmsSender
{
    private array $args;

    public function __construct(...$args)
    {
        $this->args = $args;
    }

    public function send(): array
    {
        $defaults = [
            'mobile' => '',
            'from' => '',
            'pattern_code' => '',
            'api_key' => '',
            'input_data' => [],
        ];

        $payload = wp_parse_args($this->args[0] ?? [], $defaults);

        if (empty($payload['api_key']) || empty($payload['mobile']) || empty($payload['pattern_code'])) {
            return [
                'success' => false,
                'message' => 'Missing required FarazSMS fields: api_key, mobile, pattern_code.',
            ];
        }

        $request = new FarazSMSRequest($payload['api_key']);

        return $request->sendPattern(
            (string) $payload['mobile'],
            (string) $payload['from'],
            (string) $payload['pattern_code'],
            is_array($payload['input_data']) ? $payload['input_data'] : []
        );
    }
}
