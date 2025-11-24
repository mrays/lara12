<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\DuitkuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected $duitkuService;

    public function __construct(DuitkuService $duitkuService)
    {
        $this->duitkuService = $duitkuService;
    }

    /**
     * Show payment methods for an invoice
     */
    public function show(Invoice $invoice)
    {
        // Check if user can access this invoice
        if (Auth::user()->role !== 'admin' && $invoice->client_id !== Auth::id()) {
            abort(403, 'Unauthorized access to invoice');
        }

        // Check if invoice can be paid
        if (!in_array($invoice->status, ['Draft', 'Sent', 'Overdue'])) {
            return redirect()->back()->with('error', 'This invoice cannot be paid');
        }

        $paymentMethods = $this->duitkuService->getPaymentMethods();

        return view('payment.show', compact('invoice', 'paymentMethods'));
    }

    /**
     * Process payment request
     */
    public function process(Request $request, Invoice $invoice)
    {
        try {
            // Validate request
            $request->validate([
                'payment_method' => 'required|string'
            ]);

            // Check if user can access this invoice
            if (Auth::user()->role !== 'admin' && $invoice->client_id !== Auth::id()) {
                abort(403, 'Unauthorized access to invoice');
            }

            // Check if invoice can be paid
            if (!in_array($invoice->status, ['Draft', 'Sent', 'Overdue'])) {
                return redirect()->back()->with('error', 'This invoice cannot be paid');
            }

            $paymentMethod = $request->payment_method;

            // Create payment with Duitku
            $result = $this->duitkuService->createPayment($invoice, $paymentMethod);

            if ($result['success']) {
                // Redirect to Duitku payment page
                return redirect($result['payment_url']);
            } else {
                return redirect()->back()->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            Log::error('Payment Process Error', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Payment processing failed. Please try again.');
        }
    }

    /**
     * Handle payment callback from Duitku
     */
    public function callback(Request $request)
    {
        try {
            Log::info('Payment Callback Received', $request->all());

            $result = $this->duitkuService->handleCallback($request->all());

            if ($result['success']) {
                return response('OK', 200);
            } else {
                return response('FAILED', 400);
            }
        } catch (\Exception $e) {
            Log::error('Payment Callback Error', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response('ERROR', 500);
        }
    }

    /**
     * Handle payment return from Duitku
     */
    public function return(Request $request)
    {
        try {
            $merchantOrderId = $request->merchantOrderId;
            $resultCode = $request->resultCode;

            // Find invoice by merchant order ID
            $invoice = Invoice::where('duitku_merchant_code', $merchantOrderId)->first();

            if (!$invoice) {
                return redirect()->route('dashboard')->with('error', 'Invoice not found');
            }

            // Check payment status
            $status = $this->duitkuService->checkPaymentStatus($merchantOrderId);

            if ($resultCode === '00' || ($status && $status['statusCode'] === '00')) {
                // Payment successful
                $invoice->update([
                    'status' => 'Paid',
                    'paid_date' => now(),
                    'payment_method' => 'Duitku',
                    'payment_reference' => $request->reference ?? $status['reference'] ?? null
                ]);

                $message = 'Payment successful! Invoice ' . $invoice->number . ' has been paid.';
                $type = 'success';
            } else {
                // Payment failed or cancelled
                $message = 'Payment was cancelled or failed. Please try again.';
                $type = 'warning';
            }

            // Redirect based on user role
            if (Auth::check()) {
                if (Auth::user()->role === 'admin') {
                    return redirect()->route('admin.invoices.show', $invoice)->with($type, $message);
                } else {
                    return redirect()->route('client.invoices.show', $invoice)->with($type, $message);
                }
            } else {
                return redirect()->route('login')->with($type, $message);
            }
        } catch (\Exception $e) {
            Log::error('Payment Return Error', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return redirect()->route('dashboard')->with('error', 'Payment processing error');
        }
    }

    /**
     * Check payment status manually
     */
    public function checkStatus(Invoice $invoice)
    {
        try {
            // Check if user can access this invoice
            if (Auth::user()->role !== 'admin' && $invoice->client_id !== Auth::id()) {
                abort(403, 'Unauthorized access to invoice');
            }

            if (!$invoice->duitku_merchant_code) {
                return response()->json([
                    'success' => false,
                    'message' => 'No payment reference found'
                ]);
            }

            $status = $this->duitkuService->checkPaymentStatus($invoice->duitku_merchant_code);

            if ($status && $status['statusCode'] === '00') {
                // Update invoice if payment is successful
                $invoice->update([
                    'status' => 'Paid',
                    'paid_date' => now(),
                    'payment_method' => 'Duitku',
                    'payment_reference' => $status['reference'] ?? null
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Payment confirmed',
                    'status' => 'paid'
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment still pending',
                    'status' => 'pending'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Payment Status Check Error', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Status check failed'
            ]);
        }
    }

    /**
     * Cancel payment
     */
    public function cancel(Invoice $invoice)
    {
        try {
            // Check if user can access this invoice
            if (Auth::user()->role !== 'admin' && $invoice->client_id !== Auth::id()) {
                abort(403, 'Unauthorized access to invoice');
            }

            // Reset payment data
            $invoice->update([
                'duitku_merchant_code' => null,
                'duitku_reference' => null,
                'duitku_payment_url' => null,
                'status' => 'Sent'
            ]);

            return redirect()->back()->with('success', 'Payment cancelled successfully');
        } catch (\Exception $e) {
            Log::error('Payment Cancel Error', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to cancel payment');
        }
    }
}
