<?php

namespace App\Services\Parsing;

class VeeamMailParser
{
    public function supports(?string $fromAddress, ?string $subject, ?string $body): bool
    {
        $haystack = mb_strtolower(trim(($fromAddress ?? '').' '.($subject ?? '').' '.($body ?? '')));

        return str_contains($haystack, 'veeam');
    }

    /**
     * @return array{status:string,hostname:?string,job_name:?string,summary:string,vendor:string}
     */
    public function parse(?string $subject, ?string $body): array
    {
        $text = trim(($subject ?? '')."\n".($body ?? ''));
        $lower = mb_strtolower($text);

        $status = match (true) {
            str_contains($lower, 'fail'), str_contains($lower, 'error') => 'error',
            str_contains($lower, 'warning') => 'warning',
            str_contains($lower, 'success') => 'success',
            default => 'info',
        };

        $hostname = null;
        if (preg_match('/\b([A-Z0-9][A-Z0-9\-]{3,})\b/', strtoupper($text), $matches) === 1) {
            $hostname = $matches[1];
        }

        $jobName = null;
        if (preg_match('/job\s*[:\-]\s*([^\r\n]+)/i', $text, $jobMatches) === 1) {
            $jobName = trim($jobMatches[1]);
        }

        return [
            'status' => $status,
            'hostname' => $hostname,
            'job_name' => $jobName,
            'summary' => $subject ?: mb_substr($text, 0, 180),
            'vendor' => 'veeam',
        ];
    }
}
