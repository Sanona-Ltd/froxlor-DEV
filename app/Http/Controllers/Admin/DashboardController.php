<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'customers' => User::where('is_admin', false)->count(),
            'admins'    => User::where('is_admin', true)->count(),
            'domains'   => Domain::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
