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
            'title' => 'required|string|max:255',
            'invoice_no' => 'required|string|max:255|unique:invoices,number',
            'due_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:Unpaid,Sent,Paid,Overdue,Cancelled,Lunas',
            'description' => 'nullable|string'
        ]);

        // Create invoice using direct DB query for compatibility
        \DB::table('invoices')->insert([
            'client_id' => $validated['client_id'],
            'title' => $validated['title'], // Add required title field
            'number' => $validated['invoice_no'],
            'due_date' => $validated['due_date'],
            'subtotal' => $validated['amount'],
            'total_amount' => $validated['amount'],
            'status' => $validated['status'],
            'description' => $validated['description'] ?? '',
            'issue_date' => now(),
            'paid_date' => in_array($validated['status'], ['Paid', 'Lunas']) ? now() : null,
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
        
        // Use direct DB query for better compatibility
        $invoicesData = \DB::table('invoices')
            ->leftJoin('users', 'invoices.client_id', '=', 'users.id')
            ->leftJoin('services', 'invoices.service_id', '=', 'services.id')
            ->select('invoices.*', 'users.name as client_name', 'services.product as service_name')
            ->where('invoices.client_id', $user->id)
            ->orderBy('invoices.created_at', 'desc')
            ->paginate(10);

        // Add status_color property to each invoice
        $invoices = $invoicesData;
        $invoices->getCollection()->transform(function ($invoice) {
            $invoice->status_color = $this->getStatusColor($invoice->status);
            $invoice->formatted_amount = 'Rp ' . number_format($invoice->total_amount, 0, ',', '.');
            $invoice->due_date_formatted = $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') : 'N/A';
            return $invoice;
        });

        // Calculate stats using direct queries
        $stats = [
            'total' => \DB::table('invoices')->where('client_id', $user->id)->count(),
            'paid' => \DB::table('invoices')->where('client_id', $user->id)->whereIn('status', ['Paid', 'Lunas'])->count(),
            'unpaid' => \DB::table('invoices')->where('client_id', $user->id)->whereIn('status', ['Unpaid', 'Overdue'])->count(),
            'overdue' => \DB::table('invoices')->where('client_id', $user->id)->where('status', 'Overdue')->count(),
            'total_amount' => \DB::table('invoices')->where('client_id', $user->id)->sum('total_amount'),
            'unpaid_amount' => \DB::table('invoices')->where('client_id', $user->id)->whereIn('status', ['Unpaid', 'Overdue'])->sum('total_amount'),
        ];

        return view('client.invoices.index', compact('invoices', 'stats'));
    }

    public function clientShow($invoiceId)
    {
        // Get invoice with client and service data using direct query
        $invoiceData = \DB::table('invoices')
            ->leftJoin('users', 'invoices.client_id', '=', 'users.id')
            ->leftJoin('services', 'invoices.service_id', '=', 'services.id')
            ->select('invoices.*', 'users.name as client_name', 'users.email as client_email',
                     'services.product as service_name', 'services.domain as service_domain')
            ->where('invoices.id', $invoiceId)
            ->first();

        if (!$invoiceData) {
            abort(404, 'Invoice not found');
        }

        // Ensure the invoice belongs to the authenticated client
        if ($invoiceData->client_id !== auth()->id()) {
            abort(403, 'Unauthorized access to invoice.');
        }

        // Convert to object and add computed properties
        $invoice = (object) [
            'id' => $invoiceData->id,
            'client_id' => $invoiceData->client_id,
            'service_id' => $invoiceData->service_id,
            'number' => $invoiceData->number,
            'title' => $invoiceData->title,
            'description' => $invoiceData->description,
            'notes' => $invoiceData->notes ?? null,
            'payment_method' => $invoiceData->payment_method ?? null,
            'subtotal' => $invoiceData->subtotal,
            'total_amount' => $invoiceData->total_amount,
            'status' => $invoiceData->status,
            'issue_date' => $invoiceData->issue_date ? \Carbon\Carbon::parse($invoiceData->issue_date) : null,
            'due_date' => $invoiceData->due_date ? \Carbon\Carbon::parse($invoiceData->due_date) : null,
            'paid_date' => $invoiceData->paid_date ? \Carbon\Carbon::parse($invoiceData->paid_date) : null,
            'created_at' => $invoiceData->created_at ? \Carbon\Carbon::parse($invoiceData->created_at) : null,
            'updated_at' => $invoiceData->updated_at ? \Carbon\Carbon::parse($invoiceData->updated_at) : null,
            // Client info as object for compatibility
            'client' => (object) [
                'name' => $invoiceData->client_name,
                'email' => $invoiceData->client_email,
                'phone' => null, // Column doesn't exist in users table
                'address' => null, // Column doesn't exist in users table
            ],
            // Service info as object for compatibility
            'service' => $invoiceData->service_name ? (object) [
                'product' => $invoiceData->service_name,
                'domain' => $invoiceData->service_domain,
            ] : null,
            // Computed properties
            'status_color' => $this->getStatusColor($invoiceData->status),
            'formatted_total' => 'Rp ' . number_format($invoiceData->total_amount, 0, ',', '.'),
            'formatted_subtotal' => 'Rp ' . number_format($invoiceData->subtotal ?? $invoiceData->total_amount, 0, ',', '.'),
        ];

        return view('client.invoices.show', compact('invoice'));
    }

    /**
     * Update invoice details (admin quick edit)
     */
    public function updateInvoice(Request $request, $invoiceId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'required|date',
            'invoice_no' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:Unpaid,Sent,Paid,Overdue,Cancelled,Lunas',
            'description' => 'nullable|string'
        ]);

        $updated = \DB::table('invoices')
            ->where('id', $invoiceId)
            ->update([
                'title' => $request->title,
                'due_date' => $request->due_date,
                'number' => $request->invoice_no,
                'subtotal' => $request->amount,
                'total_amount' => $request->amount,
                'status' => $request->status,
                'description' => $request->description ?? '',
                'paid_date' => in_array($request->status, ['Paid', 'Lunas']) ? now() : null,
                'updated_at' => now()
            ]);

        if ($updated) {
            return redirect()->route('admin.invoices.index')
                ->with('success', "Invoice updated successfully. Status changed to: {$request->status}");
        } else {
            return redirect()->route('admin.invoices.index')
                ->with('error', 'Failed to update invoice. Please try again.');
        }
    }

    /**
     * Update invoice status only
     */
    public function updateStatus(Request $request, $invoiceId)
    {
        $request->validate([
            'status' => 'required|in:Unpaid,Sent,Paid,Overdue,Cancelled,Lunas'
        ]);

        $updated = \DB::table('invoices')
            ->where('id', $invoiceId)
            ->update([
                'status' => $request->status,
                'paid_date' => in_array($request->status, ['Paid', 'Lunas']) ? now() : null,
                'updated_at' => now()
            ]);

        if ($updated) {
            return redirect()->route('admin.invoices.index')
                ->with('success', "Invoice status updated to: {$request->status}");
        } else {
            return redirect()->route('admin.invoices.index')
                ->with('error', 'Failed to update invoice status. Please try again.');
        }
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

    /**
     * Get status color for badge display
     */
    private function getStatusColor($status)
    {
        return match($status) {
            'Unpaid' => 'warning',
            'Sent' => 'info', 
            'Paid' => 'success',
            'Lunas' => 'success',
            'Overdue' => 'danger',
            'Cancelled' => 'secondary',
            default => 'secondary'
        };
    }

    /**
     * Get status badge class
     */
    private function getStatusBadgeClass($status)
    {
        return match($status) {
            'Unpaid' => 'badge bg-warning',
            'Sent' => 'badge bg-info',
            'Paid' => 'badge bg-success', 
            'Lunas' => 'badge bg-success',
            'Overdue' => 'badge bg-danger',
            'Cancelled' => 'badge bg-secondary',
            default => 'badge bg-secondary'
        };
    }

    /**
     * Download invoice as PDF
     */
    public function downloadPDF($invoice)
    {
        // Get invoice data using the same method as clientShow
        $invoiceData = \DB::table('invoices')
            ->leftJoin('users', 'invoices.client_id', '=', 'users.id')
            ->leftJoin('services', 'invoices.service_id', '=', 'services.id')
            ->select('invoices.*', 'users.name as client_name', 'users.email as client_email',
                     'services.product as service_name', 'services.domain as service_domain')
            ->where('invoices.id', $invoice)
            ->first();

        if (!$invoiceData) {
            abort(404, 'Invoice not found');
        }

        // Ensure the invoice belongs to the authenticated client (if accessed by client)
        if (auth()->user()->role === 'client' && $invoiceData->client_id !== auth()->id()) {
            abort(403, 'Unauthorized access to invoice.');
        }

        // Convert to object with computed properties (same as clientShow)
        $invoice = (object) [
            'id' => $invoiceData->id,
            'client_id' => $invoiceData->client_id,
            'service_id' => $invoiceData->service_id,
            'number' => $invoiceData->number,
            'title' => $invoiceData->title,
            'description' => $invoiceData->description,
            'subtotal' => $invoiceData->subtotal,
            'total_amount' => $invoiceData->total_amount,
            'status' => $invoiceData->status,
            'issue_date' => $invoiceData->issue_date ? \Carbon\Carbon::parse($invoiceData->issue_date) : null,
            'due_date' => $invoiceData->due_date ? \Carbon\Carbon::parse($invoiceData->due_date) : null,
            'paid_date' => $invoiceData->paid_date ? \Carbon\Carbon::parse($invoiceData->paid_date) : null,
            'created_at' => $invoiceData->created_at ? \Carbon\Carbon::parse($invoiceData->created_at) : null,
            'updated_at' => $invoiceData->updated_at ? \Carbon\Carbon::parse($invoiceData->updated_at) : null,
            // Client info as object for compatibility
            'client' => (object) [
                'name' => $invoiceData->client_name,
                'email' => $invoiceData->client_email,
                'phone' => null,
                'address' => null,
            ],
            // Service info as object for compatibility
            'service' => $invoiceData->service_name ? (object) [
                'product' => $invoiceData->service_name,
                'domain' => $invoiceData->service_domain,
            ] : null,
            // Computed properties
            'status_color' => $this->getStatusColor($invoiceData->status),
            'formatted_total' => 'Rp ' . number_format($invoiceData->total_amount, 0, ',', '.'),
            'formatted_subtotal' => 'Rp ' . number_format($invoiceData->subtotal ?? $invoiceData->total_amount, 0, ',', '.'),
        ];

        // For now, return a simple PDF view or redirect to show page
        // You can implement actual PDF generation later using libraries like DomPDF or wkhtmltopdf
        return view('client.invoices.pdf', compact('invoice'));
    }
}
