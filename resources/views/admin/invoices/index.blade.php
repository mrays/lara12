@extends('layouts.admin')
@section('title','Invoices')

@section('content')
<div class="container-xxl">

  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Invoices</h5>
    </div>

    <div class="card-body">
      <table class="table">
        <thead>
          <tr>
            <th>Due Date</th>
            <th>No Invoice</th>
            <th class="text-end">Amount</th>
            <th class="text-center">Status</th>
            <th class="text-center">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($invoices as $inv)
            <tr>
              <td>{{ optional($inv->due_date)->format('Y-m-d H:i:s') }}</td>
              <td>{{ $inv->invoice_no }}</td>
              <td class="text-end">{{ number_format($inv->amount, 2, ',', '.') }}</td>
              <td class="text-center">
                @if($inv->status === 'Unpaid')
                  <span class="badge bg-warning text-dark">Unpaid</span>
                @elseif($inv->status === 'Paid')
                  <span class="badge bg-success">Paid</span>
                @else
                  <span class="badge bg-danger">{{ $inv->status }}</span>
                @endif
              </td>
              <td class="text-center">
                <a href="{{ route('admin.invoices.show', $inv) }}" class="btn btn-sm btn-outline-primary">View</a>

                @if($inv->status === 'Unpaid')
                  <form action="{{ route('admin.invoices.pay', $inv) }}" method="POST" style="display:inline" onsubmit="return confirm('Proceed to pay this invoice?');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-primary">Pay Now</button>
                  </form>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center">No invoices found.</td></tr>
          @endforelse
        </tbody>
      </table>

      <div class="mt-3">{{ $invoices->links() }}</div>
    </div>
  </div>

</div>
@endsection
