<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Invoice;

class ClientDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get client's services and invoices
        $services = Service::where('client_id', $user->id)->orderBy('due_date', 'asc')->get();
        $invoices = Invoice::where('client_id', $user->id)->orderBy('created_at', 'desc')->limit(5)->get();
        
        $stats = [
            'active_services' => $services->where('status', 'Active')->count(),
            'total_services' => $services->count(),
            'unpaid_invoices' => $invoices->where('status', 'Unpaid')->count(),
            'total_invoices' => Invoice::where('client_id', $user->id)->count(),
        ];

        return view('client.dashboard', compact('user', 'services', 'invoices', 'stats'));
    }
}
