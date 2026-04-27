<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . '/AiProviderInterface.php';

class GroqProvider implements AiProviderInterface
{
    private $apiKey;
    private $maxResponseBytes;

    public function __construct($apiKey, $maxResponseBytes = 50000)
    {
        $this->apiKey = (string)$apiKey;
        $this->maxResponseBytes = (int)$maxResponseBytes;
    }

    public function chat($messages, $options)
    {
        if ($this->apiKey === '') {
            return ['ok' => false, 'text' => '', 'status' => 0, 'error' => 'Missing GROQ_API_KEY', 'meta' => []];
        }

        $model = $options['model'] ?? 'llama-3.1-8b-instant';
        $timeout = (int)($options['timeout_seconds'] ?? 20);

        $body = [
            'model' => $model,
            'messages' => array_map(function ($m) {
                return [
                    'role' => (string)($m['role'] ?? 'user'),
                    'content' => (string)($m['content'] ?? ''),
                ];
            }, $messages),
            'temperature' => 0.2,
        ];

        $url = 'https://api.groq.com/openai/v1/chat/completions';

        $resp = $this->post_json($url, $body, $timeout);
        if (!$resp['ok']) return $resp;

        $json = json_decode($resp['raw'], true);
        if (!is_array($json)) {
            return ['ok' => false, 'text' => '', 'status' => $resp['status'], 'error' => 'Invalid JSON from Groq API', 'meta' => ['raw' => $resp['raw']]];
        }

        $text = '';
        if (!empty($json['choices'][0]['message']['content'])) {
            $text = (string)$json['choices'][0]['message']['content'];
        }

        return [
            'ok' => true,
            'text' => $text,
            'status' => $resp['status'],
            'error' => null,
            'meta' => ['provider' => 'groq', 'model' => $model],
        ];
    }

    private function post_json($url, $payload, $timeout)
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => $timeout,
        ]);

        $raw = curl_exec($ch);
        $err = curl_error($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($raw === false) {
            return ['ok' => false, 'text' => '', 'status' => 0, 'error' => $err ?: 'Curl error', 'meta' => []];
        }
        if ($this->maxResponseBytes > 0 && strlen($raw) > $this->maxResponseBytes) {
            return ['ok' => false, 'text' => '', 'status' => $status, 'error' => 'Response too large', 'meta' => []];
        }
        if ($status >= 400) {
            return ['ok' => false, 'text' => '', 'status' => $status, 'error' => 'HTTP ' . $status, 'meta' => ['raw' => $raw]];
        }

        return ['ok' => true, 'raw' => $raw, 'status' => $status];
    }
}

