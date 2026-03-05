@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
  <h1 class="text-2xl font-semibold mb-4">Add Forklift</h1>

  <form action="{{ route('admin.forklifts.store') }}" method="POST" enctype="multipart/form-data">
    @include('admin.forklifts._form', [
      'forklift'  => new \App\Models\Forklift(),
      'locations' => $locations
    ])
  </form>
</div>
@endsection
