@extends('layouts.app')

@section('content')
  <div class="bg-white p-6 rounded-xl shadow">
    <h1 class="text-xl font-semibold mb-2">Thanks! Your booking request was submitted.</h1>
    <p class="text-sm mb-4">Booking ID: #{{ $booking->id }} | Status: <b>{{ $booking->status }}</b></p>
    <a class="underline" href="{{ url('/') }}">Back to home</a>
  </div>
@endsection
