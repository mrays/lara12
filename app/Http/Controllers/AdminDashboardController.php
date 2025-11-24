<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Service;
use App\Models\Invoice;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Stats untuk dashboard - gunakan query langsung tanpa relasi
        $stats = [
            'total_clients' => \DB::table('users')->where('role', 'client')->count(),
            'total_services' => \DB::table('services')->count(),
            'active_services' => \DB::table('services')->where('status', 'Active')->count(), // Gunakan 'Active' dulu
            'total_invoices' => \DB::table('invoices')->count(),
            'paid_invoices' => \DB::table('invoices')->where('status', 'Paid')->count(),
            'unpaid_invoices' => \DB::table('invoices')->where('status', 'Unpaid')->count(),
            'overdue_invoices' => \DB::table('invoices')->where('status', 'Overdue')->count(),
            'total_revenue' => \DB::table('invoices')->where('status', 'Paid')->sum('total_amount') ?? 0,
            'pending_revenue' => \DB::table('invoices')->where('status', 'Unpaid')->sum('total_amount') ?? 0,
        ];

        // Semua invoices dengan join ke users table
        $invoices = \DB::table('invoices')
            ->leftJoin('users', 'invoices.client_id', '=', 'users.id')
            ->select('invoices.*', 'users.name as client_name', 'users.email as client_email')
            ->orderBy('invoices.created_at', 'desc')
            ->paginate(10, ['*'], 'invoices_page');

        // Semua services dengan join ke users table
        $services = \DB::table('services')
            ->leftJoin('users', 'services.client_id', '=', 'users.id')
            ->select('services.*', 'users.name as client_name', 'users.email as client_email')
            ->orderBy('services.created_at', 'desc')
            ->paginate(10, ['*'], 'services_page');

        return view('admin.dashboard', compact('stats', 'invoices', 'services'));
    }

    /**
     * Update invoice status
     */
    public function updateInvoiceStatus(Request $request, $invoiceId)
    {
        $request->validate([
            'status' => 'required|in:Paid,Unpaid,Overdue,Cancelled'
        ]);

        \DB::table('invoices')
            ->where('id', $invoiceId)
            ->update([
                'status' => $request->status,
                'paid_at' => $request->status === 'Paid' ? now() : null,
                'updated_at' => now()
            ]);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Invoice status updated successfully');
    }

    /**
     * Update service status
     */
    public function updateServiceStatus(Request $request, $serviceId)
    {
        $request->validate([
            'status' => 'required|in:Active,Suspended,Terminated,Pending,Dibatalkan,Disuspen,Sedang Dibuat,Ditutup'
        ]);

        \DB::table('services')
            ->where('id', $serviceId)
            ->update([
                'status' => $request->status,
                'updated_at' => now()
            ]);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Service status updated successfully');
    }
}
