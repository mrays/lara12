<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\Invoice;
use App\Models\Client;


class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $invoices = \DB::table('invoices')
            ->leftJoin('users', 'invoices.client_id', '=', 'users.id')
            ->select('invoices.*', 'users.name as client_name', 'users.email as client_email')
            ->when($q, fn($b) => $b->where('invoices.number','like',"%$q%"))
            ->orderBy('invoices.due_date','desc')
            ->paginate(15)
            ->withQueryString();
        return view('admin.invoices.index', compact('invoices'));
    }

    public function create()
    {
        $clients = \DB::table('users')->where('role', 'client')->orderBy('name')->get();
        return view('admin.invoices.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id'=>'required|exists:users,id', // Fix: should be users table
            'title'=>'required|string|max:255',
            'description'=>'nullable|string',
            'invoice_no'=>'required|string|max:50|unique:invoices,number', // Fix: should be number field
            'due_date'=>'nullable|date',
            'amount'=>'required|numeric',
            'status'=>'required|in:Draft,Sent,Paid,Overdue,Cancelled',
        ]);
        
        // Map form fields to database fields
        $invoiceData = [
            'client_id' => $data['client_id'],
            'number' => $data['invoice_no'], // Map invoice_no to number
            'title' => $data['title'],
            'description' => $data['description'] ?? '',
            'subtotal' => $data['amount'],
            'total_amount' => $data['amount'], // Same as subtotal for now
            'status' => $data['status'],
            'issue_date' => now(),
            'due_date' => $data['due_date'],
        ];
        
        Invoice::create($invoiceData);
        return redirect()->route('admin.invoices.index')->with('success','Invoice created');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('client');
        return view('admin.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $clients = \DB::table('users')->where('role', 'client')->orderBy('name')->get();
        return view('admin.invoices.edit', compact('invoice','clients'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'client_id'=>'required|exists:users,id', // Fix: should be users table
            'title'=>'required|string|max:255',
            'description'=>'nullable|string',
            'invoice_no'=>['required','string','max:50', \Illuminate\Validation\Rule::unique('invoices','number')->ignore($invoice->id)],
            'due_date'=>'nullable|date',
            'amount'=>'required|numeric',
            'status'=>'required|in:Draft,Sent,Paid,Overdue,Cancelled',
        ]);
        
        // Map form fields to database fields
        $invoiceData = [
            'client_id' => $data['client_id'],
            'number' => $data['invoice_no'], // Map invoice_no to number
            'title' => $data['title'],
            'description' => $data['description'] ?? '',
            'subtotal' => $data['amount'],
            'total_amount' => $data['amount'], // Same as subtotal for now
            'status' => $data['status'],
            'due_date' => $data['due_date'],
        ];
        
        $invoice->update($invoiceData);
        return redirect()->route('admin.invoices.index')->with('success','Invoice updated');
    }
    
    public function pay(Request $request, Invoice $invoice)
    {
        if ($invoice->status === 'Paid') {
            return redirect()->back()->with('info', 'Invoice already paid.');
        }

        // build params sesuai README (Duitku POP)
        $merchantOrderId = 'INV-'.$invoice->id.'-'.time();
        $paymentAmount   = (int) round($invoice->amount);
        $productDetails  = "Invoice #{$invoice->invoice_no}";
        $email           = $invoice->client->email ?? 'customer@example.com';
        $phoneNumber     = $invoice->client->phone ?? '';
        $callbackUrl     = env('DUITKU_CALLBACK_URL');
        $returnUrl       = env('DUITKU_RETURN_URL');
        $expiryPeriod    = 60; // minutes

        // config object
        $duitkuConfig = new \Duitku\Config(env('DUITKU_API_KEY'), env('DUITKU_MERCHANT_CODE'));
        $duitkuConfig->setSandboxMode(true); // true = sandbox, false = production
        $duitkuConfig->setSanitizedMode(false);
        $duitkuConfig->setDuitkuLogs(true);

        // prepare params array per README
        $item1 = [
            'name' => $productDetails,
            'price' => $paymentAmount,
            'quantity' => 1
        ];
        $itemDetails = [$item1];

        $address = [
            'firstName' => $invoice->client->name ?? 'Customer',
            'lastName' => '',
            'address' => '',
            'city' => '',
            'postalCode' => '',
            'phone' => $phoneNumber,
            'countryCode' => 'ID'
        ];

        $customerDetail = [
            'firstName' => $invoice->client->name ?? 'Customer',
            'lastName' => '',
            'email' => $email,
            'phoneNumber' => $phoneNumber,
            'billingAddress' => $address,
            'shippingAddress' => $address
        ];

        $params = [
            'paymentAmount' => $paymentAmount,
            'merchantOrderId' => $merchantOrderId,
            'productDetails' => $productDetails,
            'additionalParam' => '',
            'merchantUserInfo' => "Invoice {$invoice->id}",
            'customerVaName' => $invoice->client->name ?? 'Customer',
            'email' => $email,
            'phoneNumber' => $phoneNumber,
            'itemDetails' => $itemDetails,
            'customerDetail' => $customerDetail,
            'callbackUrl' => $callbackUrl,
            'returnUrl' => $returnUrl,
            'expiryPeriod' => $expiryPeriod
        ];

        try {
            // create invoice (Duitku POP)
            $response = \Duitku\Pop::createInvoice($params, $duitkuConfig);
            $data = json_decode($response, true);
        } catch (\Exception $e) {
            \Log::error('Duitku createInvoice error: '.$e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return redirect()->route('admin.invoices.index')->with('error', 'Payment gateway error.');
        }

        // contoh response: cek 'reference' di response
        $reference = $data['reference'] ?? ($data['data']['reference'] ?? null);

        if ($reference) {
            // simpan ke invoice
            $invoice->merchant_order_id = $merchantOrderId;
            $invoice->reference = $reference;
            $invoice->save();

            // redirect to demo payment page (sandbox)
            $payUrl = 'https://sandbox.duitku.com/payment/demopage.aspx?reference=' . urlencode($reference);
            return redirect()->away($payUrl);
        }

        \Log::error('Duitku unexpected response', ['resp'=>$data]);
        return redirect()->route('admin.invoices.index')->with('error', 'Payment initiation failed.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('admin.invoices.index')->with('success','Invoice deleted');
    }
    
    
}
