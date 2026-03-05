@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 px-12 py-12">

    {{-- ═══ Main Content — dashboard, stats, table, etc. ═══ --}}
    <div class="w-full max-w-[1600px] mx-auto space-y-12">
        @yield('admin-content')
    </div>

</div>
@endsection