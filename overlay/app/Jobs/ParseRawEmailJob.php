<?php

namespace App\Jobs;

use App\Models\BackupEvent;
use App\Models\Computer;
use App\Models\ParserTrace;
use App\Models\RawEmail;
use App\Services\Parsing\RawEmailClassifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ParseRawEmailJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public int $rawEmailId)
    {
    }

    public function handle(RawEmailClassifier $classifier): void
    {
        $rawEmail = RawEmail::query()->find($this->rawEmailId);

        if (! $rawEmail || $rawEmail->ingest_status === 'parsed') {
            return;
        }

        try {
            $classification = $classifier->classifyWithTrace($rawEmail);
            $result = $classification['result'];
            $traces = $classification['traces'];

            $rawEmail->parserTraces()->delete();
            foreach ($traces as $trace) {
                ParserTrace::query()->create([
                    'raw_email_id' => $rawEmail->id,
                    'parser_rule_id' => $trace['parser_rule_id'] ?? null,
                    'matched' => (bool) ($trace['matched'] ?? false),
                    'reason' => $trace['reason'] ?? null,
                    'result_json' => $trace['result_json'] ?? null,
                ]);
            }

            if ($result['classification'] === 'backup_event') {
                $computer = $this->resolveComputer($rawEmail->tenant_id, $result['hostname']);

                BackupEvent::query()->create([
                    'computer_id' => $computer->id,
                    'status' => $result['status'],
                    'summary' => $result['summary'],
                    'source_vendor' => $result['vendor'],
                    'event_at' => $rawEmail->received_at ?? now(),
                    'raw_email_ref' => (string) $rawEmail->id,
                ]);

                $computer->update([
                    'last_status' => $this->toComputerStatus($result['status']),
                    'last_event_at' => $rawEmail->received_at ?? now(),
                ]);

                $rawEmail->update([
                    'ingest_status' => 'parsed',
                    'headers_json' => array_merge($rawEmail->headers_json ?? [], ['parse_result' => $result]),
                ]);

                return;
            }

            $rawEmail->update([
                'ingest_status' => 'ignored',
                'headers_json' => array_merge($rawEmail->headers_json ?? [], ['parse_result' => $result]),
            ]);
        } catch (\Throwable $throwable) {
            $rawEmail->update([
                'ingest_status' => 'error',
                'ingest_error' => $throwable->getMessage(),
            ]);

            throw $throwable;
        }
    }

    private function resolveComputer(?int $tenantId, ?string $hostname): Computer
    {
        $normalizedHostname = $hostname ?: 'UNASSIGNED-HOST';

        return Computer::query()->firstOrCreate(
            ['tenant_id' => $tenantId, 'hostname' => $normalizedHostname],
            [
                'display_name' => $normalizedHostname,
                'last_status' => 'gray',
            ]
        );
    }

    private function toComputerStatus(string $eventStatus): string
    {
        return match ($eventStatus) {
            'success' => 'green',
            'warning' => 'yellow',
            'error' => 'red',
            default => 'gray',
        };
    }
}
