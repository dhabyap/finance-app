<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . '/AiProviderInterface.php';

class GeminiProvider implements AiProviderInterface
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
            return ['ok' => false, 'text' => '', 'status' => 0, 'error' => 'Missing GEMINI_API_KEY', 'meta' => []];
        }

        $model = $options['model'] ?? 'gemini-1.5-flash';
        $timeout = (int)($options['timeout_seconds'] ?? 20);

        // Gemini expects "contents" with role + parts.
        $contents = [];
        foreach ($messages as $m) {
            $role = $m['role'] ?? 'user';
            // Gemini roles: user|model. We'll map system/assistant to model for context.
            $gemRole = ($role === 'user') ? 'user' : 'model';
            $contents[] = [
                'role' => $gemRole,
                'parts' => [['text' => (string)($m['content'] ?? '')]],
            ];
        }

        $body = [
            'contents' => $contents,
            'generationConfig' => [
                // Ask for JSON output when possible, but still treat output as untrusted.
                'responseMimeType' => 'application/json',
                'temperature' => 0.2,
            ],
        ];

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . rawurlencode($model) . ':generateContent?key=' . rawurlencode($this->apiKey);

        $resp = $this->post_json($url, $body, $timeout);
        if (!$resp['ok']) return $resp;

        $json = json_decode($resp['raw'], true);
        if (!is_array($json)) {
            return ['ok' => false, 'text' => '', 'status' => $resp['status'], 'error' => 'Invalid JSON from Gemini API', 'meta' => ['raw' => $resp['raw']]];
        }

        $text = '';
        // Typical: candidates[0].content.parts[0].text
        if (!empty($json['candidates'][0]['content']['parts'][0]['text'])) {
            $text = (string)$json['candidates'][0]['content']['parts'][0]['text'];
        }

        return [
            'ok' => true,
            'text' => $text,
            'status' => $resp['status'],
            'error' => null,
            'meta' => ['provider' => 'gemini', 'model' => $model],
        ];
    }

    private function post_json($url, $payload, $timeout)
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
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

