<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientDashboardController extends Controller
{
    public function index()
    {
        return view('dash.client_index', [
            'title' => 'Client Dashboard',
            'user' => auth()->user(),
        ]);
    }
}
