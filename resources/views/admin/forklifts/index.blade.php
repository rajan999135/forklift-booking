@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-semibold text-slate-800">Forklifts</h1>
    <a href="{{ route('admin.forklifts.create') }}"
       class="bg-sky-600 text-white px-4 py-2 rounded-md hover:bg-sky-700">New Forklift</a>
  </div>

  <table class="min-w-full border rounded-md bg-white">
    <thead class="bg-slate-100 text-sm">
      <tr>
        <th class="px-3 py-2 text-left">ID</th>
        <th class="px-3 py-2 text-left">Name</th>
        <th class="px-3 py-2 text-left">Rate</th>
        <th class="px-3 py-2 text-left">Capacity</th>
        <th class="px-3 py-2 text-left">Actions</th>
      </tr>
    </thead>
    <tbody class="divide-y text-sm">
      @foreach($forklifts as $f)
      <tr>
        <td class="px-3 py-2">{{ $f->id }}</td>
        <td class="px-3 py-2">{{ $f->name }}</td>
        <td class="px-3 py-2">${{ number_format($f->hourly_rate,2) }}</td>
        <td class="px-3 py-2">{{ $f->capacity_kg }} kg</td>
        <td class="px-3 py-2">
          <a href="{{ route('admin.forklifts.edit', $f) }}" class="text-blue-600 hover:underline">Edit</a>
          <form action="{{ route('admin.forklifts.destroy', $f) }}" method="POST" class="inline">
            @csrf @method('DELETE')
            <button type="submit" class="text-red-600 hover:underline"
                    onclick="return confirm('Delete this forklift?')">Delete</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
