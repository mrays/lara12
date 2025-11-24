<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\Service;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        // Use direct DB query to avoid relationship issues
        $invoices = \DB::table('invoices')
            ->leftJoin('users', 'invoices.client_id', '=', 'users.id')
            ->select('invoices.*', 'users.name as client_name', 'users.email as client_email')
            ->when($request->filled('status'), function($query) use ($request) {
                return $query->where('invoices.status', $request->status);
            })
            ->orderBy('invoices.created_at', 'desc')
            ->paginate(15);

        return view('admin.invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['client', 'service', 'items']);
        return view('admin.invoices.show', compact('invoice'));
    }

    public function create()
    {
        // Get clients using direct query for compatibility
        $clients = \DB::table('users')
            ->where('role', 'client')
            ->orderBy('name')
            ->get();
        
        return view('admin.invoices.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:users,id',
            'invoice_no' => 'required|string|max:255|unique:invoices,invoice_no',
            'due_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:Paid,Unpaid,Overdue,Cancelled,Sedang Dicek,Lunas,Belum Lunas',
            'description' => 'nullable|string'
        ]);

        // Create invoice using direct DB query for compatibility
        \DB::table('invoices')->insert([
            'client_id' => $validated['client_id'],
            'invoice_no' => $validated['invoice_no'],
            'due_date' => $validated['due_date'],
            'amount' => $validated['amount'],
            'total_amount' => $validated['amount'],
            'status' => $validated['status'],
            'description' => $validated['description'],
            'paid_at' => in_array($validated['status'], ['Paid', 'Lunas']) ? now() : null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('admin.invoices.index')
            ->with('success', 'Invoice created successfully!');
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load(['items']);
        $clients = Client::active()->orderBy('name')->get();
        $services = Service::active()->with('client')->orderBy('product')->get();
        
        return view('admin.invoices.edit', compact('invoice', 'clients', 'services'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        // Only allow editing if invoice is in Draft status
        if ($invoice->status !== 'Draft') {
            return redirect()->back()->with('error', 'Only draft invoices can be edited.');
        }

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'service_id' => 'nullable|exists:services,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        // Calculate totals
        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }

        $taxRate = $validated['tax_rate'] ?? 0;
        $taxAmount = ($subtotal * $taxRate) / 100;
        $discountAmount = $validated['discount_amount'] ?? 0;
        $totalAmount = $subtotal + $taxAmount - $discountAmount;

        // Update invoice
        $invoice->update([
            'client_id' => $validated['client_id'],
            'service_id' => $validated['service_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'subtotal' => $subtotal,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'issue_date' => $validated['issue_date'],
            'due_date' => $validated['due_date'],
            'notes' => $validated['notes'],
        ]);

        // Delete existing items and create new ones
        $invoice->items()->delete();
        foreach ($validated['items'] as $item) {
            $invoice->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        return redirect()->route('admin.invoices.show', $invoice)
                        ->with('success', 'Invoice updated successfully!');
    }

    public function destroy(Invoice $invoice)
    {
        // Only allow deletion if invoice is in Draft status
        if ($invoice->status !== 'Draft') {
            return redirect()->back()->with('error', 'Only draft invoices can be deleted.');
        }

        $invoice->delete();
        return redirect()->route('admin.invoices.index')
                        ->with('success', 'Invoice deleted successfully!');
    }

    public function send(Invoice $invoice)
    {
        if ($invoice->status !== 'Draft') {
            return redirect()->back()->with('error', 'Only draft invoices can be sent.');
        }

        $invoice->update(['status' => 'Sent']);
        
        // Here you would typically send an email to the client
        // Mail::to($invoice->client->email)->send(new InvoiceSent($invoice));

        return redirect()->back()->with('success', 'Invoice sent successfully!');
    }

    public function markAsPaid(Request $request, Invoice $invoice)
    {
        if ($invoice->status === 'Paid') {
            return redirect()->back()->with('error', 'Invoice is already paid.');
        }

        $validated = $request->validate([
            'payment_method' => 'nullable|string|max:255',
            'payment_reference' => 'nullable|string|max:255',
        ]);

        $invoice->markAsPaid(
            $validated['payment_method'] ?? null,
            $validated['payment_reference'] ?? null
        );

        return redirect()->back()->with('success', 'Invoice marked as paid!');
    }

    public function clientInvoices()
    {
        $user = auth()->user();
        $invoices = Invoice::forClient($user->id)
                          ->with(['service'])
                          ->orderBy('created_at', 'desc')
                          ->paginate(10);

        $stats = [
            'total' => Invoice::forClient($user->id)->count(),
            'paid' => Invoice::forClient($user->id)->paid()->count(),
            'unpaid' => Invoice::forClient($user->id)->unpaid()->count(),
            'overdue' => Invoice::forClient($user->id)->overdue()->count(),
            'total_amount' => Invoice::forClient($user->id)->sum('total_amount'),
            'unpaid_amount' => Invoice::forClient($user->id)->unpaid()->sum('total_amount'),
        ];

        return view('client.invoices.index', compact('invoices', 'stats'));
    }

    public function clientShow(Invoice $invoice)
    {
        // Ensure the invoice belongs to the authenticated client
        if ($invoice->client_id !== auth()->id()) {
            abort(403, 'Unauthorized access to invoice.');
        }

        $invoice->load(['client', 'service', 'items']);
        return view('client.invoices.show', compact('invoice'));
    }

    /**
     * Update invoice details (admin quick edit)
     */
    public function updateInvoice(Request $request, $invoiceId)
    {
        $request->validate([
            'due_date' => 'required|date',
            'invoice_no' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:Paid,Unpaid,Overdue,Cancelled,Sedang Dicek,Lunas,Belum Lunas'
        ]);

        \DB::table('invoices')
            ->where('id', $invoiceId)
            ->update([
                'due_date' => $request->due_date,
                'invoice_no' => $request->invoice_no,
                'total_amount' => $request->amount,
                'amount' => $request->amount, // Update both fields for compatibility
                'status' => $request->status,
                'paid_at' => in_array($request->status, ['Paid', 'Lunas']) ? now() : null,
                'updated_at' => now()
            ]);

        return redirect()->route('admin.invoices.index')
            ->with('success', 'Invoice updated successfully');
    }

    /**
     * Update invoice status only
     */
    public function updateStatus(Request $request, $invoiceId)
    {
        $request->validate([
            'status' => 'required|in:Paid,Unpaid,Overdue,Cancelled,Sedang Dicek,Lunas,Belum Lunas'
        ]);

        \DB::table('invoices')
            ->where('id', $invoiceId)
            ->update([
                'status' => $request->status,
                'paid_at' => in_array($request->status, ['Paid', 'Lunas']) ? now() : null,
                'updated_at' => now()
            ]);

        return redirect()->route('admin.invoices.index')
            ->with('success', 'Invoice status updated successfully');
    }

    /**
     * Delete invoice (admin quick delete)
     */
    public function deleteInvoice($invoiceId)
    {
        \DB::table('invoices')->where('id', $invoiceId)->delete();

        return redirect()->route('admin.invoices.index')
            ->with('success', 'Invoice deleted successfully');
    }
}
