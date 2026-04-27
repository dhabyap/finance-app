<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . '/GeminiProvider.php';
require_once __DIR__ . '/GroqProvider.php';

class AiRouter
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function chat($messages)
    {
        $primary = $this->config['ai_provider_primary'] ?? 'gemini';
        $fallback = $this->config['ai_provider_fallback'] ?? 'groq';

        $attempts = [
            ['provider' => $primary, 'model' => $this->config['ai_model_primary'] ?? null],
        ];
        if ($fallback && $fallback !== $primary) {
            $attempts[] = ['provider' => $fallback, 'model' => $this->config['ai_model_fallback'] ?? null];
        }

        $timeout = (int)($this->config['ai_timeout_seconds'] ?? 20);
        $maxBytes = (int)($this->config['ai_max_response_bytes'] ?? 50000);

        $last = null;
        $attemptMeta = [];
        foreach ($attempts as $a) {
            $providerName = $a['provider'];
            $model = $a['model'];

            $prov = $this->make_provider($providerName, $maxBytes);
            $t0 = microtime(true);
            $res = $prov->chat($messages, ['model' => $model, 'timeout_seconds' => $timeout]);
            $latencyMs = (int)round((microtime(true) - $t0) * 1000);
            $res['meta']['latency_ms'] = $latencyMs;

            if ($res['ok']) {
                $res['meta']['failover_used'] = ($providerName !== $primary);
                $res['meta']['attempts'] = $attemptMeta;
                return $res;
            }

            $attemptMeta[] = [
                'provider' => $providerName,
                'model' => $model,
                'status' => (int)($res['status'] ?? 0),
                'error' => (string)($res['error'] ?? ''),
                'latency_ms' => $latencyMs,
            ];
            $last = $res;

            // Failover only on transient errors.
            $status = (int)($res['status'] ?? 0);
            if (!in_array($status, [0, 408, 429, 500, 502, 503, 504], true)) {
                // Misconfig or hard failure: stop.
                break;
            }
        }

        if (!$last) {
            return ['ok' => false, 'text' => '', 'status' => 0, 'error' => 'AI router failure', 'meta' => ['attempts' => $attemptMeta]];
        }
        $last['meta']['attempts'] = $attemptMeta;
        return $last;
    }

    private function make_provider($name, $maxBytes)
    {
        $name = strtolower((string)$name);
        if ($name === 'groq') {
            return new GroqProvider($this->config['groq_api_key'] ?? '', $maxBytes);
        }
        // default: gemini
        return new GeminiProvider($this->config['gemini_api_key'] ?? '', $maxBytes);
    }
}

