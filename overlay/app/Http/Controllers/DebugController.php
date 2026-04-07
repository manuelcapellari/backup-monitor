<?php

namespace App\Http\Controllers;

use App\Models\BackupEvent;
use App\Models\MailAccount;
use App\Models\ParserRule;
use App\Models\RawEmail;
use App\Models\Tenant;
use Illuminate\View\View;

class DebugController extends Controller
{
    public function index(): View
    {
        return view('debug.index', [
            'stats' => [
                'tenants' => Tenant::count(),
                'mail_accounts' => MailAccount::count(),
                'raw_emails_total' => RawEmail::count(),
                'raw_emails_new' => RawEmail::where('ingest_status', 'new')->count(),
                'raw_emails_error' => RawEmail::where('ingest_status', 'error')->count(),
                'backup_events' => BackupEvent::count(),
                'parser_rules_active' => ParserRule::where('is_active', true)->count(),
                'imap_extension' => extension_loaded('imap') ? 'loaded' : 'missing',
            ],
            'latestRawErrors' => RawEmail::where('ingest_status', 'error')->latest()->limit(20)->get(),
        ]);
    }
}
