<?php

namespace App\Http\Controllers;

use App\Models\MailAccount;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MailAccountController extends Controller
{
    public function index(): View
    {
        return view('mail-accounts.index', [
            'mailAccounts' => MailAccount::query()->with('tenant')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('mail-accounts.form', [
            'mailAccount' => new MailAccount([
                'protocol' => 'imap',
                'port' => 993,
                'encryption' => 'tls',
                'mailbox' => 'INBOX',
                'poll_interval_minutes' => 5,
                'is_active' => true,
            ]),
            'tenants' => Tenant::query()->orderBy('name')->get(),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateRequest($request, true);
        MailAccount::create($data);

        return redirect()->route('mail-accounts.index')->with('status', 'Mailkonto angelegt.');
    }

    public function edit(MailAccount $mailAccount): View
    {
        return view('mail-accounts.form', [
            'mailAccount' => $mailAccount,
            'tenants' => Tenant::query()->orderBy('name')->get(),
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, MailAccount $mailAccount): RedirectResponse
    {
        $data = $this->validateRequest($request, false);

        if (! array_key_exists('password_encrypted', $data)) {
            unset($data['password_encrypted']);
        }

        $mailAccount->update($data);

        return redirect()->route('mail-accounts.index')->with('status', 'Mailkonto aktualisiert.');
    }

    private function validateRequest(Request $request, bool $isCreate): array
    {
        $validated = $request->validate([
            'tenant_id' => ['nullable', Rule::exists('tenants', 'id')],
            'name' => ['required', 'string', 'max:120'],
            'protocol' => ['required', Rule::in(['imap', 'pop3'])],
            'host' => ['required', 'string', 'max:190'],
            'port' => ['required', 'integer', 'between:1,65535'],
            'encryption' => ['nullable', Rule::in(['none', 'ssl', 'tls'])],
            'username' => ['required', 'string', 'max:190'],
            'password_plain' => [$isCreate ? 'required' : 'nullable', 'string', 'max:255'],
            'mailbox' => ['nullable', 'string', 'max:120'],
            'poll_interval_minutes' => ['required', 'integer', 'between:1,1440'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);
        $validated['mailbox'] = $validated['mailbox'] ?? 'INBOX';
        $validated['encryption'] = $validated['encryption'] ?? 'tls';

        if (! empty($validated['password_plain'])) {
            $validated['password_encrypted'] = Crypt::encryptString($validated['password_plain']);
        }

        unset($validated['password_plain']);

        return $validated;
    }
}
