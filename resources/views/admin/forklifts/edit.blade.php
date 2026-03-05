@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
  <h1 class="text-2xl font-semibold mb-4">Edit Forklift</h1>

  <form action="{{ route('admin.forklifts.update', $forklift) }}" method="POST" enctype="multipart/form-data">
    @method('PUT')
    @include('admin.forklifts._form', [
      'forklift'  => $forklift,
      'locations' => $locations
    ])
  </form>
</div>
@endsection
