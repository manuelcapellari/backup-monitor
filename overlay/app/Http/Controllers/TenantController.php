<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TenantController extends Controller
{
    public function index(): View
    {
        return view('tenants.index', [
            'tenants' => Tenant::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('tenants.form', [
            'tenant' => new Tenant(['is_active' => true]),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'alpha_dash', 'max:120', 'unique:tenants,slug'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        Tenant::create($data);

        return redirect()->route('tenants.index')->with('status', 'Mandant angelegt.');
    }

    public function edit(Tenant $tenant): View
    {
        return view('tenants.form', [
            'tenant' => $tenant,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => [
                'required',
                'alpha_dash',
                'max:120',
                Rule::unique('tenants', 'slug')->ignore($tenant->id),
            ],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $tenant->update($data);

        return redirect()->route('tenants.index')->with('status', 'Mandant aktualisiert.');
    }
}
