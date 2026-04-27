<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| AI Chat configuration
|
| Keep keys out of git. Prefer injecting via server env vars and reading with getenv().
*/

// Prefer .env-loaded values from $_ENV/$_SERVER; fallback to getenv().
function ai_env($key, $default = null) {
    if (isset($_ENV[$key])) return $_ENV[$key];
    if (isset($_SERVER[$key])) return $_SERVER[$key];
    $v = getenv($key);
    return ($v === false) ? $default : $v;
}

// Robust boolean parsing: supports true/false/1/0/yes/no/on/off.
$aiEnabledRaw = ai_env('AI_ENABLED');
$aiEnabledParsed = filter_var($aiEnabledRaw, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
$config['ai_enabled'] = ($aiEnabledParsed === null) ? false : (bool)$aiEnabledParsed;

// Providers: gemini | groq
$config['ai_provider_primary'] = ai_env('AI_PROVIDER_PRIMARY', 'gemini') ?: 'gemini';
$config['ai_provider_fallback'] = ai_env('AI_PROVIDER_FALLBACK', 'groq') ?: 'groq';

// Models (defaults chosen for cost/latency; adjust in env)
$config['ai_model_primary'] = ai_env('AI_MODEL_PRIMARY', 'gemini-1.5-flash') ?: 'gemini-1.5-flash';
$config['ai_model_fallback'] = ai_env('AI_MODEL_FALLBACK', 'llama-3.1-8b-instant') ?: 'llama-3.1-8b-instant';

$config['ai_timeout_seconds'] = (int)(ai_env('AI_TIMEOUT_SECONDS', 20) ?: 20);
$config['ai_max_response_bytes'] = (int)(ai_env('AI_MAX_RESPONSE_BYTES', 50000) ?: 50000);

// Rate limiting (simple server-side protection)
$config['ai_rate_limit_per_minute'] = (int)(ai_env('AI_RATE_LIMIT_PER_MINUTE', 15) ?: 15);

// API keys read from env
$config['gemini_api_key'] = (string)(ai_env('GEMINI_API_KEY', '') ?: '');
$config['groq_api_key'] = (string)(ai_env('GROQ_API_KEY', '') ?: '');

