<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Service;
use App\Models\Invoice;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'active_services' => Service::where('status','Active')->count(),
            'invoice_unpaid' => Invoice::where('status','Unpaid')->count(),
            'tickets_open' => 0, // kalau ada table tickets, ambil countnya
        ];

        // ambil beberapa invoice terbaru
        $invoices = Invoice::orderBy('created_at','desc')->limit(5)->get();

        // ambil layanan client (pada contoh UI kita ambil semua atau paginate)
        $services = Service::with('client')->orderBy('due_date','asc')->limit(100)->get();

        return view('admin.dashboard', compact('stats','invoices','services'));
    }
}
