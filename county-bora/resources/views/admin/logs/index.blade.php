@extends('layouts.admin')

@section('content')
<div class="p-8">
    <h1 class="text-xl font-black uppercase text-gray-800">System Audit Logs</h1>
    <div class="mt-6 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        @if($logs->isEmpty())
            <p class="text-xs text-gray-400">No logs found yet.</p>
        @else
            <table class="w-full text-xs">
                <thead>
                    <tr class="text-gray-400 uppercase tracking-widest text-left">
                        <th class="pb-4">Admin</th>
                        <th class="pb-4">Action</th>
                        <th class="pb-4">Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr class="border-t border-gray-50">
                            <td class="py-4">{{ $log->admin->first_name ?? 'System' }}</td>
                            <td class="py-4">{{ $log->action }}</td>
                            <td class="py-4">{{ $log->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection