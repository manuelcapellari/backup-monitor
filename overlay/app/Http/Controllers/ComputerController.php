<?php

namespace App\Http\Controllers;

use App\Models\Computer;

class ComputerController extends Controller
{
    public function show(Computer $computer)
    {
        $computer->load([
            'tenant',
            'aliases',
            'backupEvents' => fn ($query) => $query->orderByDesc('event_at')->limit(100),
        ]);

        return view('computers.show', [
            'computer' => $computer,
        ]);
    }
}
