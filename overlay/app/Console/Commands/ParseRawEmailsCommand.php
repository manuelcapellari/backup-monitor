<?php

namespace App\Console\Commands;

use App\Jobs\ParseRawEmailJob;
use App\Models\RawEmail;
use Illuminate\Console\Command;

class ParseRawEmailsCommand extends Command
{
    protected $signature = 'backup-monitor:parse-raw-emails {--limit=200}';

    protected $description = 'Parse raw emails and auto-assign backup events/computers.';

    public function handle(): int
    {
        $limit = max(1, (int) $this->option('limit'));

        $rawEmails = RawEmail::query()
            ->where('ingest_status', 'new')
            ->orderBy('received_at')
            ->limit($limit)
            ->get();

        if ($rawEmails->isEmpty()) {
            $this->info('Keine neuen Rohmails zum Parsen gefunden.');

            return self::SUCCESS;
        }

        foreach ($rawEmails as $rawEmail) {
            ParseRawEmailJob::dispatch($rawEmail->id);
        }

        $this->info(sprintf('%d Rohmails zur Verarbeitung in die Queue gelegt.', $rawEmails->count()));

        return self::SUCCESS;
    }
}
