<?php

namespace Database\Seeders;

use App\Models\BackupEvent;
use App\Models\Computer;
use App\Models\ComputerAlias;
use App\Models\MailAccount;
use App\Models\ParserRule;
use App\Models\RawEmail;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class BackupMonitorDemoSeeder extends Seeder
{
    public function run(): void
    {
        $tenantA = Tenant::query()->create([
            'name' => 'Mandant A',
            'slug' => 'mandant-a',
            'is_active' => true,
        ]);

        $tenantB = Tenant::query()->create([
            'name' => 'Mandant B',
            'slug' => 'mandant-b',
            'is_active' => true,
        ]);

        $imapTls = MailAccount::query()->create([
            'tenant_id' => $tenantA->id,
            'name' => 'Veeam Inbox A (TLS)',
            'protocol' => 'imap',
            'host' => 'imap-tls.example.local',
            'port' => 143,
            'encryption' => 'tls',
            'username' => 'backup-a@example.local',
            'mailbox' => 'INBOX',
            'poll_interval_minutes' => 5,
            'is_active' => true,
        ]);

        $imapSsl = MailAccount::query()->create([
            'tenant_id' => $tenantB->id,
            'name' => 'BackupExec Inbox B (SSL)',
            'protocol' => 'imap',
            'host' => 'imap-ssl.example.local',
            'port' => 993,
            'encryption' => 'ssl',
            'username' => 'backup-b@example.local',
            'mailbox' => 'INBOX',
            'poll_interval_minutes' => 5,
            'is_active' => true,
        ]);

        ParserRule::query()->insert([
            [
                'name' => 'Veeam Success',
                'vendor' => 'veeam',
                'match_field' => 'body',
                'match_pattern' => 'veeam',
                'status' => 'success',
                'priority' => 10,
                'hostname_regex' => '([A-Z0-9][A-Z0-9\-]{3,})',
                'job_name_regex' => 'job\s*[:\-]\s*([^\r\n]+)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Veeam Warning',
                'vendor' => 'veeam',
                'match_field' => 'body',
                'match_pattern' => 'warning',
                'status' => 'warning',
                'priority' => 20,
                'hostname_regex' => '([A-Z0-9][A-Z0-9\-]{3,})',
                'job_name_regex' => 'job\s*[:\-]\s*([^\r\n]+)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Veeam Error',
                'vendor' => 'veeam',
                'match_field' => 'body',
                'match_pattern' => 'error',
                'status' => 'error',
                'priority' => 30,
                'hostname_regex' => '([A-Z0-9][A-Z0-9\-]{3,})',
                'job_name_regex' => 'job\s*[:\-]\s*([^\r\n]+)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Backup Exec Success',
                'vendor' => 'backup_exec',
                'match_field' => 'subject',
                'match_pattern' => 'Backup Exec Job Completed Successfully',
                'status' => 'success',
                'priority' => 40,
                'hostname_regex' => 'server\s*[:\-]\s*([A-Z0-9][A-Z0-9\-]{2,})',
                'job_name_regex' => 'job\s*name\s*[:\-]\s*([^\r\n]+)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ignore Vendor Update Mails',
                'vendor' => 'custom',
                'match_field' => 'subject',
                'match_pattern' => 'update',
                'status' => 'other_unmatched',
                'priority' => 900,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $computer = Computer::query()->create([
            'tenant_id' => $tenantA->id,
            'hostname' => 'DESKTOP-X1WFASDF',
            'display_name' => 'Buchhaltung-PC Müller',
            'last_status' => 'yellow',
            'last_event_at' => now()->subMinutes(45),
        ]);

        ComputerAlias::query()->create([
            'computer_id' => $computer->id,
            'alias' => 'Buchhaltung-PC',
        ]);

        RawEmail::query()->create([
            'tenant_id' => $tenantA->id,
            'mail_account_id' => $imapTls->id,
            'message_uid' => '1001',
            'message_id' => '<demo-1001@example.local>',
            'subject' => 'Backup job warning',
            'from_address' => 'veeam@example.local',
            'received_at' => now()->subMinutes(45),
            'headers_json' => ['x-source' => 'demo'],
            'body_text' => 'Incremental backup completed with warning',
            'ingest_status' => 'new',
        ]);

        RawEmail::query()->create([
            'tenant_id' => $tenantB->id,
            'mail_account_id' => $imapSsl->id,
            'message_uid' => '2001',
            'message_id' => '<demo-2001@example.local>',
            'subject' => 'Backup Exec Job Completed Successfully',
            'from_address' => 'backupexec@example.local',
            'received_at' => now()->subMinutes(25),
            'headers_json' => ['x-source' => 'demo'],
            'body_text' => 'Server: SRV-ARCHIVE-01 Job Name: Daily Full Backup Status: Successful',
            'ingest_status' => 'new',
        ]);

        BackupEvent::query()->insert([
            [
                'computer_id' => $computer->id,
                'status' => 'warning',
                'summary' => 'Incremental backup with warning (locked file)',
                'source_vendor' => 'veeam',
                'event_at' => now()->subMinutes(45),
                'raw_email_ref' => 'demo-msg-1001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'computer_id' => $computer->id,
                'status' => 'success',
                'summary' => 'Backup completed successfully',
                'source_vendor' => 'veeam',
                'event_at' => now()->subDay(),
                'raw_email_ref' => 'demo-msg-1000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Computer::query()->create([
            'tenant_id' => $tenantB->id,
            'hostname' => 'SRV-ARCHIVE-01',
            'display_name' => 'Archivserver',
            'last_status' => 'green',
            'last_event_at' => now()->subMinutes(20),
        ]);
    }
}
