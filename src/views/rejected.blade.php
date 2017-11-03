@extends('onepay::result')

@section('message')
  <div style="font-size: 40px; color: green;">
    Rejected code: {{ $rejectedCode }} - {{ $message }}
  </div>
@endsection
