<?php

namespace App\Http\Controllers;

use App\Models\Computer;

class DashboardController extends Controller
{
    public function index()
    {
        $computers = Computer::query()
            ->with('tenant')
            ->orderByDesc('last_event_at')
            ->paginate(25);

        return view('dashboard.index', [
            'computers' => $computers,
        ]);
    }
}
