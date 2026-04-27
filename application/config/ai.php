<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| AI Chat configuration
|
| Keep keys out of git. Prefer injecting via server env vars and reading with getenv().
*/

// Robust boolean parsing: supports true/false/1/0/yes/no/on/off.
$aiEnabledRaw = getenv('AI_ENABLED');
$aiEnabledParsed = filter_var($aiEnabledRaw, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
$config['ai_enabled'] = ($aiEnabledParsed === null) ? false : (bool)$aiEnabledParsed;

// Providers: gemini | groq
$config['ai_provider_primary'] = getenv('AI_PROVIDER_PRIMARY') ?: 'gemini';
$config['ai_provider_fallback'] = getenv('AI_PROVIDER_FALLBACK') ?: 'groq';

// Models (defaults chosen for cost/latency; adjust in env)
$config['ai_model_primary'] = getenv('AI_MODEL_PRIMARY') ?: 'gemini-1.5-flash';
$config['ai_model_fallback'] = getenv('AI_MODEL_FALLBACK') ?: 'llama-3.1-8b-instant';

$config['ai_timeout_seconds'] = (int)(getenv('AI_TIMEOUT_SECONDS') ?: 20);
$config['ai_max_response_bytes'] = (int)(getenv('AI_MAX_RESPONSE_BYTES') ?: 50000);

// Rate limiting (simple server-side protection)
$config['ai_rate_limit_per_minute'] = (int)(getenv('AI_RATE_LIMIT_PER_MINUTE') ?: 15);

// API keys read from env
$config['gemini_api_key'] = getenv('GEMINI_API_KEY') ?: '';
$config['groq_api_key'] = getenv('GROQ_API_KEY') ?: '';

