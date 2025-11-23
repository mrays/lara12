<!-- resources/views/payment/goto.blade.php -->
@extends('layouts.admin')
@section('content')
<div class="container">
  <h3>Proceed to payment</h3>
  <p>Kamu akan diarahkan ke halaman pembayaran. Jika tidak, klik tombol di bawah.</p>
  <a id="btn" class="btn btn-primary" href="{{ $payUrl }}" target="_blank">Go to Payment</a>
  <script>
    setTimeout(function(){
      window.location.href = "{{ $payUrl }}";
    }, 2000);
  </script>
</div>
@endsection
