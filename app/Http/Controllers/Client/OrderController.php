<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServicePackage;
use App\Models\Service;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Show order form with available service packages
     */
    public function create()
    {
        $packages = ServicePackage::where('is_active', true)
            ->orderBy('base_price', 'asc')
            ->get();
            
        return view('client.orders.create', compact('packages'));
    }

    /**
     * Store new order and create invoice
     */
    public function store(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:service_packages,id',
            'domain' => 'required|string|max:255',
            'billing_cycle' => 'required|in:monthly,annually',
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = auth()->user();
        $package = ServicePackage::findOrFail($request->package_id);
        
        // Calculate price (gunakan base_price langsung, tanpa dikali 12)
        $price = $package->base_price;

        try {
            DB::beginTransaction();

            // Create service with pending status
            $service = Service::create([
                'client_id' => $user->id,
                'package_id' => $package->id,
                'product' => $package->name,
                'domain' => $request->domain,
                'price' => $price,
                'billing_cycle' => $request->billing_cycle,
                'registration_date' => now(),
                'status' => 'Pending',
                'due_date' => now()->addDays(7), // Due in 7 days for first payment
                'notes' => $request->notes,
            ]);

            // Generate invoice number
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            
            // Create invoice
            $invoice = Invoice::create([
                'number' => $invoiceNumber,
                'client_id' => $user->id,
                'service_id' => $service->id,
                'amount' => $price,
                'status' => 'Unpaid',
                'due_date' => now()->addDays(7),
                'description' => "Order: {$package->name} - {$request->domain} ({$request->billing_cycle})",
                'notes' => $request->notes,
            ]);

            DB::commit();

            return redirect()->route('client.orders.success', $invoice->id)
                ->with('success', 'Order berhasil dibuat! Silakan lakukan pembayaran.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat order: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show order success page with invoice
     */
    public function success($invoiceId)
    {
        $invoice = Invoice::with(['service'])->findOrFail($invoiceId);
        
        // Ensure this invoice belongs to current user
        if ($invoice->client_id !== auth()->id()) {
            abort(403);
        }

        return view('client.orders.success', compact('invoice'));
    }

    /**
     * Get package details via AJAX
     */
    public function getPackageDetails($id)
    {
        $package = ServicePackage::findOrFail($id);
        
        return response()->json([
            'id' => $package->id,
            'name' => $package->name,
            'description' => $package->description,
            'base_price' => $package->base_price,
            'monthly_price' => $package->base_price,
            'annual_price' => $package->base_price * 12 * 0.9,
            'features' => $package->features,
        ]);
    }
}
