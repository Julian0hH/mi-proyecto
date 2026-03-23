<?php

namespace App\Traits;

trait InputSanitizer
{
    protected function sanitize(?string $value): string
    {
        if ($value === null) return '';
        $value = str_replace("\0", '', $value);
        $value = strip_tags($value);
        return trim($value);
    }

    protected function hasDangerous(string $value): bool
    {
        $patterns = [
            '/<script[\s\S]*>/i',
            '/javascript\s*:/i',
            '/on\w+\s*=/i',
            '/vbscript\s*:/i',
            '/\bunion\b[\s\S]+\bselect\b/i',
            '/\bdrop\b[\s\S]+\b(table|database|schema)\b/i',
            '/\binsert\b[\s\S]+\binto\b/i',
            '/\bdelete\b[\s\S]+\bfrom\b/i',
            '/\bupdate\b[\s\S]+\bset\b/i',
            "/'\s*or\s+'?[\d']/i",
            '/;\s*(drop|delete|truncate|alter)\b/i',
            '/--\s/',
            '/\/\*[\s\S]*\*\//',
        ];
        foreach ($patterns as $p) {
            if (preg_match($p, $value)) return true;
        }
        return false;
    }

    protected function sanitizeField(?string $value, string $label = 'Campo'): array
    {
        $clean = $this->sanitize($value);
        if ($this->hasDangerous($clean)) {
            return ['value' => $clean, 'error' => "{$label}: contenido no permitido"];
        }
        return ['value' => $clean, 'error' => null];
    }
}
