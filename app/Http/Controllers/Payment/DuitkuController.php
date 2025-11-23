<?php
namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Invoice;

class DuitkuController extends Controller
{
    public function callback(Request $request)
    {
        $duitkuConfig = new \Duitku\Config(env('DUITKU_API_KEY'), env('DUITKU_MERCHANT_CODE'));
        $duitkuConfig->setSandboxMode(true);
        $duitkuConfig->setDuitkuLogs(true);

        try {
            // library helper will read input and return json string
            $callbackJson = \Duitku\Pop::callback($duitkuConfig); // returns JSON string
            $payload = json_decode($callbackJson, true);
        } catch (\Exception $e) {
            Log::error('Duitku callback parse error: '.$e->getMessage());
            return response('Bad Request', 400);
        }

        Log::info('Duitku callback payload', $payload);

        $merchantOrderId = $payload['merchantOrderId'] ?? $payload['merchant_order_id'] ?? null;
        $statusCode = $payload['statusCode'] ?? $payload['resultCode'] ?? null;

        $invoice = null;
        if ($merchantOrderId) {
            $invoice = Invoice::where('merchant_order_id', $merchantOrderId)->first();
        } elseif (!empty($payload['reference'])) {
            $invoice = Invoice::where('reference', $payload['reference'])->first();
        }

        if (!$invoice) {
            Log::warning('Duitku callback invoice not found', $payload);
            return response('OK', 200); // still return 200 to avoid retry storms
        }

        // menurut doc: statusCode '00' => success
        if ((string)$statusCode === '00' || stripos($payload['message'] ?? '', 'SUCCESS') !== false) {
            $invoice->status = 'Paid';
            $invoice->save();
            Log::info('Invoice marked Paid by Duitku', ['invoice_id'=>$invoice->id]);
        } else {
            // tetap simpan/update info kalau perlu
            $invoice->status = 'Unpaid';
            $invoice->save();
        }

        return response('OK', 200);
    }

    public function return(Request $request)
    {
        // user redirected after payment — tampilkan halaman thanks/success
        return view('payment.return', ['payload' => $request->all()]);
    }
}
