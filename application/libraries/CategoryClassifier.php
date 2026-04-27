<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Category classifier for AI Chat transactions.
 *
 * AI-first (when enabled) with strict guardrails:
 * - AI can only choose from allowed categories.
 * - Otherwise fallback to deterministic keyword rules and then default category.
 */
class CategoryClassifier
{
    public function classify($text, $type, $allowedCategories, $aiCfg = null)
    {
        $text = trim((string)$text);
        $type = (string)$type;
        $allowed = $this->normalize_allowed($allowedCategories, $type);

        if (empty($allowed)) {
            return ['category' => 'Others', 'confidence' => 0.0, 'source' => 'fallback'];
        }

        $defaultCat = $this->default_category($allowed);

        // AI-first path
        if (is_array($aiCfg) && !empty($aiCfg['ai_enabled'])) {
            $ai = $this->classify_with_ai($text, $type, $allowed, $defaultCat, $aiCfg);
            if ($ai) {
                return $ai;
            }
        }

        // Deterministic rules
        $rule = $this->classify_with_rules(mb_strtolower($text, 'UTF-8'), $type, $allowed, $defaultCat);
        if ($rule) {
            return $rule;
        }

        return ['category' => $defaultCat, 'confidence' => 0.2, 'source' => 'fallback'];
    }

    private function normalize_allowed($allowedCategories, $type)
    {
        $out = [];
        if (!is_array($allowedCategories)) return $out;
        foreach ($allowedCategories as $c) {
            if (!is_array($c)) continue;
            if (($c['type'] ?? null) !== $type) continue;
            $name = trim((string)($c['name'] ?? ''));
            if ($name === '') continue;
            $out[] = $name;
        }
        return array_values(array_unique($out));
    }

    private function default_category($allowedNames)
    {
        // Prefer "Others" if present, else first allowed category.
        foreach ($allowedNames as $n) {
            if (strcasecmp($n, 'Others') === 0) return $n;
        }
        return $allowedNames[0] ?? 'Others';
    }

    private function classify_with_ai($text, $type, $allowedNames, $defaultCat, $aiCfg)
    {
        $allowedList = implode(', ', array_slice($allowedNames, 0, 60));

        $system = "Kamu adalah classifier kategori transaksi.\n"
            . "Balas JSON saja, tanpa markdown.\n"
            . "Pilih tepat 1 kategori dari daftar ALLOWED.\n"
            . "Jika tidak yakin, pilih DEFAULT.\n"
            . "Output format: {\"category\":\"<ALLOWED>\",\"confidence\":0.0}\n"
            . "TYPE: {$type}\n"
            . "DEFAULT: {$defaultCat}\n"
            . "ALLOWED: {$allowedList}";

        $messages = [
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => $text],
        ];

        require_once APPPATH . 'libraries/ai/AiRouter.php';
        $router = new AiRouter($aiCfg);
        $res = $router->chat($messages);
        if (empty($res['ok'])) {
            return null;
        }

        $payload = $this->parse_json((string)($res['text'] ?? ''));
        if (!is_array($payload)) return null;

        $cat = trim((string)($payload['category'] ?? ''));
        $conf = $payload['confidence'] ?? null;
        if ($cat === '' || !in_array($cat, $allowedNames, true)) {
            // Strict: only allow exact allowed name.
            return null;
        }
        $confF = is_numeric($conf) ? (float)$conf : 0.5;
        if ($confF < 0) $confF = 0;
        if ($confF > 1) $confF = 1;

        return ['category' => $cat, 'confidence' => $confF, 'source' => 'ai'];
    }

    private function parse_json($text)
    {
        $text = trim((string)$text);
        if ($text === '') return null;
        $json = json_decode($text, true);
        if (is_array($json)) return $json;
        if (preg_match('/\\{[\\s\\S]*\\}/', $text, $m)) {
            $json = json_decode($m[0], true);
            if (is_array($json)) return $json;
        }
        return null;
    }

    private function classify_with_rules($lowerText, $type, $allowedNames, $defaultCat)
    {
        // Map logical buckets to actual allowed category names.
        $map = [
            'food' => ['Food'],
            'transport' => ['Transport'],
            'utilities' => ['Utilities'],
            'health' => ['Health'],
            'shopping' => ['Shopping'],
            'entertainment' => ['Entertainment'],
            'salary' => ['Salary'],
        ];

        $keywords = [
            'food' => ['makan', 'nasi', 'ayam', 'kopi', 'minum', 'resto', 'restoran', 'cafe', 'kafe', 'jajan', 'snack'],
            'transport' => ['bensin', 'bbm', 'gojek', 'grab', 'ojek', 'angkot', 'bus', 'parkir', 'tol', 'taksi', 'kereta'],
            'utilities' => ['listrik', 'wifi', 'internet', 'pulsa', 'air', 'token', 'pln', 'pd', 'pdam'],
            'health' => ['obat', 'dokter', 'apotek', 'rumah sakit', 'rs', 'vitamin'],
            'shopping' => ['belanja', 'tokopedia', 'shopee', 'lazada', 'blibli', 'checkout'],
            'entertainment' => ['film', 'netflix', 'game', 'spotify', 'hiburan', 'bioskop'],
            'salary' => ['gaji', 'salary', 'payroll'],
        ];

        $bucket = null;
        foreach ($keywords as $b => $words) {
            foreach ($words as $w) {
                if (strpos($lowerText, $w) !== false) {
                    $bucket = $b;
                    break 2;
                }
            }
        }

        if (!$bucket) return null;

        // Salary only makes sense for income.
        if ($bucket === 'salary' && $type !== 'income') {
            return null;
        }

        $candidates = $map[$bucket] ?? [];
        foreach ($candidates as $want) {
            foreach ($allowedNames as $n) {
                if (strcasecmp($n, $want) === 0) {
                    return ['category' => $n, 'confidence' => 0.6, 'source' => 'rules'];
                }
            }
        }

        return ['category' => $defaultCat, 'confidence' => 0.3, 'source' => 'fallback'];
    }
}

