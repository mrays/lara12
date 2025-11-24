<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->number }} - PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.6;
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        .company-info h1 {
            color: #007bff;
            margin: 0;
            font-size: 28px;
        }
        .company-info p {
            margin: 5px 0;
            color: #666;
        }
        .invoice-details {
            text-align: right;
        }
        .invoice-details h2 {
            color: #007bff;
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        .invoice-details p {
            margin: 5px 0;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
        }
        .status-paid { background-color: #d4edda; color: #155724; }
        .status-unpaid { background-color: #fff3cd; color: #856404; }
        .status-overdue { background-color: #f8d7da; color: #721c24; }
        .status-cancelled { background-color: #e2e3e5; color: #383d41; }
        .billing-info {
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
        }
        .bill-to, .invoice-info {
            width: 48%;
        }
        .bill-to h3, .invoice-info h3 {
            color: #007bff;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        .invoice-table th,
        .invoice-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .invoice-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
        }
        .invoice-table .text-center {
            text-align: center;
        }
        .invoice-table .text-right {
            text-align: right;
        }
        .invoice-summary {
            margin-top: 20px;
            text-align: right;
        }
        .invoice-summary table {
            margin-left: auto;
            border-collapse: collapse;
        }
        .invoice-summary td {
            padding: 8px 15px;
            border: none;
        }
        .invoice-summary .total-row {
            border-top: 2px solid #007bff;
            font-weight: bold;
            font-size: 18px;
            color: #007bff;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        .notes {
            margin: 30px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
        }
        .notes h4 {
            margin-top: 0;
            color: #007bff;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <!-- Print Button (hidden when printing) -->
    <div class="no-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" style="background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer;">
            Print Invoice
        </button>
        <button onclick="window.close()" style="background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; margin-left: 10px;">
            Close
        </button>
    </div>

    <!-- Invoice Header -->
    <div class="invoice-header">
        <div class="company-info">
            <h1>EXPUTRA CLOUD</h1>
            <p>Cloud Hosting & Web Services</p>
            <p>Email: info@exputra.cloud</p>
            <p>Website: https://exputra.cloud</p>
        </div>
        <div class="invoice-details">
            <h2>INVOICE</h2>
            <p><strong>Invoice #:</strong> {{ $invoice->number }}</p>
            <p><strong>Issue Date:</strong> {{ $invoice->issue_date ? $invoice->issue_date->format('M d, Y') : 'N/A' }}</p>
            <p><strong>Due Date:</strong> {{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'N/A' }}</p>
            <p><strong>Status:</strong> 
                <span class="status-badge status-{{ strtolower($invoice->status) }}">
                    {{ $invoice->status }}
                </span>
            </p>
        </div>
    </div>

    <!-- Billing Information -->
    <div class="billing-info">
        <div class="bill-to">
            <h3>Bill To:</h3>
            <p><strong>{{ $invoice->client->name ?? 'N/A' }}</strong></p>
            <p>{{ $invoice->client->email ?? 'N/A' }}</p>
            @if($invoice->client->phone)
                <p>{{ $invoice->client->phone }}</p>
            @endif
            @if($invoice->client->address)
                <p>{{ $invoice->client->address }}</p>
            @endif
        </div>
        <div class="invoice-info">
            <h3>Invoice Information:</h3>
            <p><strong>Title:</strong> {{ $invoice->title ?? 'Service Invoice' }}</p>
            @if($invoice->service)
                <p><strong>Service:</strong> {{ $invoice->service->product }}</p>
                @if($invoice->service->domain)
                    <p><strong>Domain:</strong> {{ $invoice->service->domain }}</p>
                @endif
            @endif
            @if($invoice->paid_date)
                <p><strong>Paid Date:</strong> {{ $invoice->paid_date->format('M d, Y') }}</p>
            @endif
        </div>
    </div>

    <!-- Invoice Items -->
    <table class="invoice-table">
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-center">Quantity</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>{{ $invoice->title ?? 'Service Payment' }}</strong>
                    @if($invoice->description)
                        <br><small style="color: #666;">{{ $invoice->description }}</small>
                    @endif
                </td>
                <td class="text-center">1</td>
                <td class="text-right">{{ $invoice->formatted_subtotal }}</td>
                <td class="text-right">{{ $invoice->formatted_total }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Invoice Summary -->
    <div class="invoice-summary">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">{{ $invoice->formatted_subtotal }}</td>
            </tr>
            <tr class="total-row">
                <td><strong>Total Amount:</strong></td>
                <td class="text-right"><strong>{{ $invoice->formatted_total }}</strong></td>
            </tr>
        </table>
    </div>

    <!-- Notes -->
    @if($invoice->description)
    <div class="notes">
        <h4>Notes:</h4>
        <p>{{ $invoice->description }}</p>
    </div>
    @endif

    <!-- Payment Status -->
    @if($invoice->status === 'Paid' || $invoice->status === 'Lunas')
        <div style="background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 4px; margin: 20px 0;">
            <h4 style="color: #155724; margin-top: 0;">✓ Payment Received</h4>
            <p style="color: #155724; margin-bottom: 0;">
                This invoice has been paid in full on {{ $invoice->paid_date ? $invoice->paid_date->format('M d, Y') : 'N/A' }}.
            </p>
        </div>
    @elseif($invoice->status === 'Overdue')
        <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 4px; margin: 20px 0;">
            <h4 style="color: #721c24; margin-top: 0;">⚠ Payment Overdue</h4>
            <p style="color: #721c24; margin-bottom: 0;">
                This invoice is overdue. Please make payment as soon as possible to avoid service interruption.
            </p>
        </div>
    @else
        <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 4px; margin: 20px 0;">
            <h4 style="color: #856404; margin-top: 0;">💳 Payment Pending</h4>
            <p style="color: #856404; margin-bottom: 0;">
                Please make payment by {{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'the due date' }} to avoid late fees.
            </p>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Thank you for your business!</p>
        <p>This is a computer-generated invoice. No signature required.</p>
        <p>Generated on {{ now()->format('M d, Y H:i:s') }}</p>
    </div>

    <script>
        // Auto-print when opened in new window
        window.onload = function() {
            // Uncomment the line below to auto-print
            // window.print();
        }
    </script>
</body>
</html>
