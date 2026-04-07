<?php

namespace App\Http\Controllers;

use App\Models\RawEmail;
use Illuminate\View\View;

class RawEmailController extends Controller
{
    public function index(): View
    {
        return view('raw-emails.index', [
            'rawEmails' => RawEmail::query()
                ->with(['tenant', 'mailAccount'])
                ->orderByDesc('received_at')
                ->paginate(50),
        ]);
    }

    public function show(RawEmail $rawEmail): View
    {
        $rawEmail->load(['tenant', 'mailAccount', 'parserTraces.parserRule']);

        return view('raw-emails.show', [
            'rawEmail' => $rawEmail,
        ]);
    }
}
