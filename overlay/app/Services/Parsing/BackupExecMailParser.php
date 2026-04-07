<?php

namespace App\Services\Parsing;

class BackupExecMailParser
{
    public function supports(?string $fromAddress, ?string $subject, ?string $body): bool
    {
        $haystack = mb_strtolower(trim(($fromAddress ?? '').' '.($subject ?? '').' '.($body ?? '')));

        return str_contains($haystack, 'backup exec') || str_contains($haystack, 'backupexec');
    }

    /**
     * @return array{status:string,hostname:?string,job_name:?string,summary:string,vendor:string}
     */
    public function parse(?string $subject, ?string $body): array
    {
        $text = trim(($subject ?? '')."\n".($body ?? ''));
        $lower = mb_strtolower($text);

        $status = match (true) {
            str_contains($lower, 'failed'), str_contains($lower, 'failure'), str_contains($lower, 'error') => 'error',
            str_contains($lower, 'warning') => 'warning',
            str_contains($lower, 'successful'), str_contains($lower, 'success') => 'success',
            default => 'info',
        };

        $hostname = null;
        if (preg_match('/(server|client|computer|machine)\s*[:\-]\s*([A-Z0-9][A-Z0-9\-]{2,})/i', $text, $hostMatches) === 1) {
            $hostname = strtoupper(trim($hostMatches[2]));
        } elseif (preg_match('/\b([A-Z0-9][A-Z0-9\-]{3,})\b/', strtoupper($text), $hostFallback) === 1) {
            $hostname = $hostFallback[1];
        }

        $jobName = null;
        if (preg_match('/job\s*name\s*[:\-]\s*([^\r\n]+)/i', $text, $jobMatches) === 1) {
            $jobName = trim($jobMatches[1]);
        } elseif (preg_match('/job\s*[:\-]\s*([^\r\n]+)/i', $text, $jobFallback) === 1) {
            $jobName = trim($jobFallback[1]);
        }

        return [
            'status' => $status,
            'hostname' => $hostname,
            'job_name' => $jobName,
            'summary' => $subject ?: mb_substr($text, 0, 180),
            'vendor' => 'backup_exec',
        ];
    }
}
