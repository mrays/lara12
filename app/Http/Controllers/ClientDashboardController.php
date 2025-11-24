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
        
        // Get client's services and invoices with relationships
        $services = Service::where('client_id', $user->id)->orderBy('due_date', 'asc')->get();
        $invoices = Invoice::where('client_id', $user->id)
                          ->with(['service'])
                          ->orderBy('created_at', 'desc')
                          ->limit(5)
                          ->get();
        
        $stats = [
            'active_services' => $services->where('status', 'Active')->count(),
            'total_services' => $services->count(),
            'unpaid_invoices' => Invoice::where('client_id', $user->id)->unpaid()->count(),
            'total_invoices' => Invoice::where('client_id', $user->id)->count(),
            'overdue_invoices' => Invoice::where('client_id', $user->id)->overdue()->count(),
            'unpaid_amount' => Invoice::where('client_id', $user->id)->unpaid()->sum('total_amount'),
            'total_amount' => Invoice::where('client_id', $user->id)->sum('total_amount'),
        ];

        return view('client.dashboard', compact('user', 'services', 'invoices', 'stats'));
    }
}
