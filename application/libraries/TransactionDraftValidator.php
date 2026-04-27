<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TransactionDraftValidator
{
    public function validate($payload)
    {
        $errors = [];
        if (!is_array($payload)) {
            return ['ok' => false, 'errors' => ['Invalid payload'], 'payload' => null];
        }

        $intent = $payload['intent'] ?? null;
        if (!in_array($intent, ['create_transaction', 'clarify', 'unknown'], true)) {
            $errors[] = 'Invalid intent';
        }

        if ($intent === 'create_transaction') {
            $draft = $payload['draft'] ?? null;
            if (!is_array($draft)) {
                $errors[] = 'Missing draft';
            } else {
                $type = $draft['type'] ?? null;
                if (!in_array($type, ['income', 'expense'], true)) $errors[] = 'Invalid type';

                $amount = $draft['amount'] ?? null;
                if (!is_numeric($amount)) $errors[] = 'Invalid amount';

                $date = $draft['transaction_date'] ?? null;
                if (!is_string($date) || !preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $date)) $errors[] = 'Invalid transaction_date';

                $title = $draft['title'] ?? '';
                if (!is_string($title) || trim($title) === '') $errors[] = 'Missing title';

                $category = $draft['category'] ?? '';
                if (!is_string($category) || trim($category) === '') $errors[] = 'Missing category';

                if (isset($draft['payee']) && !is_string($draft['payee'])) $errors[] = 'Invalid payee';
            }
        }

        $missing = $payload['missing'] ?? [];
        if ($intent === 'clarify') {
            if (!is_array($missing) || empty($missing)) $errors[] = 'Missing required missing[]';
            $questions = $payload['questions'] ?? [];
            if (!is_array($questions) || count($questions) < 1) $errors[] = 'Missing questions[]';
        }

        return ['ok' => empty($errors), 'errors' => $errors, 'payload' => $payload];
    }

    public function parse_json_from_text($text)
    {
        $text = trim((string)$text);
        if ($text === '') return null;

        $json = json_decode($text, true);
        if (is_array($json)) return $json;

        // Sometimes models wrap JSON in fences; try extracting first {...} block.
        if (preg_match('/\\{[\\s\\S]*\\}/', $text, $m)) {
            $json = json_decode($m[0], true);
            if (is_array($json)) return $json;
        }

        return null;
    }
}

