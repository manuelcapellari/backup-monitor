<?php

namespace App\Jobs;

use App\Jobs\ParseRawEmailJob;
use App\Models\MailAccount;
use App\Models\RawEmail;
use App\Services\ImapMailboxPathBuilder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class PollImapAccountJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public int $mailAccountId, public bool $onlyUnseen = true)
    {
    }

    public function handle(ImapMailboxPathBuilder $pathBuilder): void
    {
        if (! extension_loaded('imap')) {
            throw new \RuntimeException('PHP extension "imap" is required for IMAP polling.');
        }

        $account = MailAccount::query()->findOrFail($this->mailAccountId);

        if (! $account->is_active || $account->protocol !== 'imap') {
            return;
        }

        $mailboxPath = $pathBuilder->fromAccount($account);
        $password = $account->decryptedPassword();

        $stream = @imap_open($mailboxPath, $account->username, $password, OP_READONLY);

        if (! $stream) {
            throw new \RuntimeException('IMAP connection failed: '.implode(' | ', imap_errors() ?: []));
        }

        try {
            $criteria = $this->onlyUnseen ? 'UNSEEN' : 'ALL';
            $messageNumbers = imap_search($stream, $criteria, SE_UID);

            if (empty($messageNumbers)) {
                return;
            }

            foreach ($messageNumbers as $uid) {
                $this->ingestMessage($stream, $account, (string) $uid);
            }
        } finally {
            imap_close($stream);
        }
    }

    private function ingestMessage($stream, MailAccount $account, string $uid): void
    {
        $exists = RawEmail::query()
            ->where('mail_account_id', $account->id)
            ->where('message_uid', $uid)
            ->exists();

        if ($exists) {
            return;
        }

        $msgNo = imap_msgno($stream, (int) $uid);

        if ($msgNo <= 0) {
            return;
        }

        $overview = imap_fetch_overview($stream, (string) $msgNo, 0)[0] ?? null;
        $headerInfo = imap_headerinfo($stream, $msgNo);

        $subject = $overview->subject ?? null;
        $messageId = $overview->message_id ?? null;
        $from = $this->extractFromAddress($headerInfo);
        $receivedAt = isset($overview->date) ? Carbon::parse($overview->date) : null;

        $bodyText = imap_body($stream, $msgNo, FT_PEEK) ?: null;

        $rawEmail = RawEmail::query()->create([
            'tenant_id' => $account->tenant_id,
            'mail_account_id' => $account->id,
            'message_uid' => $uid,
            'message_id' => $messageId,
            'subject' => $subject,
            'from_address' => $from,
            'received_at' => $receivedAt,
            'headers_json' => [
                'subject' => $subject,
                'message_id' => $messageId,
                'from' => $from,
                'date' => $overview->date ?? null,
            ],
            'body_text' => $bodyText,
            'ingest_status' => 'new',
        ]);

        ParseRawEmailJob::dispatch($rawEmail->id);
    }

    private function extractFromAddress(object $headerInfo): ?string
    {
        if (! isset($headerInfo->from[0])) {
            return null;
        }

        $from = $headerInfo->from[0];

        if (! isset($from->mailbox, $from->host)) {
            return null;
        }

        return $from->mailbox.'@'.$from->host;
    }
}
