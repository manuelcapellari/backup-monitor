<?php

namespace App\Console\Commands;

use App\Jobs\PollImapAccountJob;
use App\Models\MailAccount;
use Illuminate\Console\Command;

class PollImapCommand extends Command
{
    protected $signature = 'backup-monitor:poll-imap {--account-id=} {--all : Poll all messages, not only unseen}';

    protected $description = 'Poll active IMAP mail accounts and store raw emails.';

    public function handle(): int
    {
        $accountId = $this->option('account-id');
        $onlyUnseen = ! $this->option('all');

        $query = MailAccount::query()
            ->where('is_active', true)
            ->where('protocol', 'imap');

        if ($accountId) {
            $query->whereKey((int) $accountId);
        }

        $accounts = $query->get();

        if ($accounts->isEmpty()) {
            $this->warn('Keine aktiven IMAP-Konten gefunden.');

            return self::SUCCESS;
        }

        foreach ($accounts as $account) {
            PollImapAccountJob::dispatch($account->id, $onlyUnseen);
            $this->info("Polling queued: {$account->name} ({$account->host}:{$account->port}, {$account->encryption})");
        }

        return self::SUCCESS;
    }
}
