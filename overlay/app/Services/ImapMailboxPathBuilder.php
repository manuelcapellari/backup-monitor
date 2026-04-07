<?php

namespace App\Services;

use App\Models\MailAccount;

class ImapMailboxPathBuilder
{
    public function fromAccount(MailAccount $mailAccount): string
    {
        $mailbox = $mailAccount->mailbox ?: 'INBOX';

        return sprintf('{%s:%d%s}%s', $mailAccount->host, $mailAccount->port, $mailAccount->mailboxPathFlag(), $mailbox);
    }
}
