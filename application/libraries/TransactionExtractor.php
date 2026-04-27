<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cheap-first transaction extractor.
 *
 * This intentionally starts with deterministic parsing (regex) to reduce LLM cost.
 * If not confident, caller can fall back to an LLM provider (not implemented here).
 */
class TransactionExtractor
{
    public function extract($text, $todayYmd)
    {
        $text = trim((string)$text);
        if ($text === '') {
            return $this->clarify(['message'], 'Pesannya kosong. Mau catat transaksi apa?');
        }

        $lower = mb_strtolower($text, 'UTF-8');

        $type = $this->guess_type($lower);
        $amount = $this->parse_amount_idr($lower);
        $date = $this->parse_date($lower, $todayYmd);

        // Always build a partial draft so we can merge/continue on clarification loops.
        $title = $this->guess_title($lower);
        $draft = [
            'type' => $type ?: '',
            'amount' => ($amount === null) ? '' : $amount,
            'transaction_date' => $date ?: '',
            'category' => '', // Filled by server-side category classifier (AI-first).
            'title' => $title ?: $text,
            'payee' => '',
        ];

        $missing = [];
        if (!$type) $missing[] = 'type';
        if ($amount === null) $missing[] = 'amount';
        if (!$date) $missing[] = 'transaction_date';
        if (trim((string)($draft['title'] ?? '')) === '') $missing[] = 'title';

        if (!empty($missing)) {
            // Ask 1 most important question to proceed.
            if (in_array('amount', $missing, true)) {
                return $this->clarify($missing, 'Berapa jumlahnya (contoh: 25k, 35 ribu, 2jt)?', $draft);
            }
            if (in_array('type', $missing, true)) {
                return $this->clarify($missing, 'Ini pemasukan atau pengeluaran?', $draft);
            }
            return $this->clarify($missing, 'Tanggal berapa transaksinya? (contoh: hari ini / kemarin / 2026-04-27)', $draft);
        }

        return [
            'intent' => 'create_transaction',
            'draft' => $draft,
            'missing' => [],
            'questions' => [],
            'confidence' => 0.65,
        ];
    }

    private function clarify($missing, $question, $draft = null)
    {
        return [
            'intent' => 'clarify',
            'draft' => is_array($draft) ? $draft : null,
            'missing' => array_values($missing),
            'questions' => [$question],
            'confidence' => 0.0,
        ];
    }

    private function guess_type($lower)
    {
        // Very simple heuristics for Indonesian.
        $incomeWords = ['gaji', 'salary', 'masuk', 'income', 'pendapatan', 'bonus', 'refund', 'dibayar', 'terima'];
        $expenseWords = ['beli', 'bayar', 'jajan', 'makan', 'ngopi', 'expense', 'keluar', 'tagihan', 'top up', 'topup'];

        foreach ($incomeWords as $w) {
            if (strpos($lower, $w) !== false) return 'income';
        }
        foreach ($expenseWords as $w) {
            if (strpos($lower, $w) !== false) return 'expense';
        }
        return null;
    }

    /**
     * Parses Indonesian amount formats into integer IDR.
     * Supports: 35 ribu, 250k, 7.5jt, 7.500.000, 7500000
     */
    private function parse_amount_idr($lower)
    {
        // 7.500.000 or 7,500,000
        if (preg_match('/\\b(\\d{1,3}([\\.,]\\d{3})+)(?!\\d)\\b/u', $lower, $m)) {
            $n = preg_replace('/[\\.,]/', '', $m[1]);
            return is_numeric($n) ? (int)$n : null;
        }

        // 250k, 10k
        if (preg_match('/\\b(\\d+(?:[\\.,]\\d+)?)\\s*k\\b/u', $lower, $m)) {
            return (int)round(((float)str_replace(',', '.', $m[1])) * 1000);
        }

        // 7.5jt, 2jt
        if (preg_match('/\\b(\\d+(?:[\\.,]\\d+)?)\\s*(jt|juta)\\b/u', $lower, $m)) {
            return (int)round(((float)str_replace(',', '.', $m[1])) * 1000000);
        }

        // 35 ribu
        if (preg_match('/\\b(\\d+(?:[\\.,]\\d+)?)\\s*(rb|ribu)\\b/u', $lower, $m)) {
            return (int)round(((float)str_replace(',', '.', $m[1])) * 1000);
        }

        // Plain number (avoid matching dates like 2026-04-27 by requiring >= 3 digits)
        if (preg_match('/\\b(\\d{3,})\\b/u', $lower, $m)) {
            return (int)$m[1];
        }

        return null;
    }

    private function parse_date($lower, $todayYmd)
    {
        // ISO date
        if (preg_match('/\\b(\\d{4}-\\d{2}-\\d{2})\\b/u', $lower, $m)) {
            return $m[1];
        }

        // Relative
        if (strpos($lower, 'hari ini') !== false || strpos($lower, 'today') !== false) {
            return $todayYmd;
        }
        if (strpos($lower, 'kemarin') !== false || strpos($lower, 'yesterday') !== false) {
            return date('Y-m-d', strtotime($todayYmd . ' -1 day'));
        }

        // "tgl 25" or "tanggal 25"
        if (preg_match('/\\b(tgl|tanggal)\\s*(\\d{1,2})\\b/u', $lower, $m)) {
            $day = (int)$m[2];
            $year = (int)date('Y', strtotime($todayYmd));
            $month = (int)date('m', strtotime($todayYmd));
            if ($day >= 1 && $day <= 31) {
                $candidate = sprintf('%04d-%02d-%02d', $year, $month, $day);
                return $candidate;
            }
        }

        return $todayYmd; // Default to today for MVP
    }

    private function guess_title($lower)
    {
        // Remove common prefixes and amount tokens; keep first ~40 chars.
        $t = preg_replace('/\\b(beli|bayar|gaji|masuk|keluar|transfer|top\\s*up|topup)\\b/u', '', $lower);
        $t = preg_replace('/\\b\\d+(?:[\\.,]\\d+)?\\s*(k|jt|juta|rb|ribu)\\b/u', '', $t);
        $t = preg_replace('/\\b\\d{1,3}([\\.,]\\d{3})+\\b/u', '', $t);
        $t = trim(preg_replace('/\\s+/', ' ', $t));
        if ($t === '') return '';
        return mb_substr($t, 0, 40, 'UTF-8');
    }
}

