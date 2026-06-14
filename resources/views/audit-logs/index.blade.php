@extends('layouts.app')
@section('title', 'Audit Logs')

@section('header')
    <h2 class="font-semibold text-xl text-[#111110] dark:text-white leading-tight">Audit Logs</h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-semibold text-[#111110] dark:text-white mb-6">Audit Logs</h1>

            <div class="bg-white dark:bg-[#161615] border border-[#e5e5e0] dark:border-[#2a2a28] rounded-xl p-4 mb-6">
                <form method="GET" class="flex gap-4 items-end">
                    <div>
                        <label class="block text-xs text-[#6b6b66] dark:text-[#a8a8a2] uppercase mb-1">Action</label>
                        <select name="action" class="border border-[#e5e5e0] dark:border-[#3a3a38] bg-white dark:bg-[#161615] text-[#111110] dark:text-white rounded-lg px-3 py-2 text-sm">
                            <option value="">All</option>
                            @foreach($actions as $a)
                                <option value="{{ $a }}" @selected(request('action') === $a)>{{ $a }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-[#6b6b66] dark:text-[#a8a8a2] uppercase mb-1">Entity Type</label>
                        <select name="entity_type" class="border border-[#e5e5e0] dark:border-[#3a3a38] bg-white dark:bg-[#161615] text-[#111110] dark:text-white rounded-lg px-3 py-2 text-sm">
                            <option value="">All</option>
                            @foreach($entityTypes as $et)
                                <option value="{{ $et }}" @selected(request('entity_type') === $et)>{{ $et }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">Filter</button>
                </form>
            </div>

            <div class="bg-white dark:bg-[#161615] border border-[#e5e5e0] dark:border-[#2a2a28] rounded-xl overflow-hidden">
                <table class="min-w-full divide-y divide-[#e5e5e0] dark:divide-[#2a2a28]">
                    <thead class="bg-gray-50 dark:bg-[#1a1a18]">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#6b6b66] dark:text-[#a8a8a2] uppercase">Action</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#6b6b66] dark:text-[#a8a8a2] uppercase">Entity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#6b6b66] dark:text-[#a8a8a2] uppercase">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#6b6b66] dark:text-[#a8a8a2] uppercase">Payload</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#6b6b66] dark:text-[#a8a8a2] uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e5e5e0] dark:divide-[#2a2a28]">
                        @forelse ($logs as $log)
                            <tr class="hover:bg-gray-50 dark:hover:bg-[#1a1a18]">
                                <td class="px-6 py-4 text-sm font-medium text-[#111110] dark:text-white">{{ $log->action }}</td>
                                <td class="px-6 py-4 text-sm text-[#6b6b66] dark:text-[#a8a8a2]">{{ $log->entity_type }} #{{ $log->entity_id }}</td>
                                <td class="px-6 py-4 text-sm text-[#6b6b66] dark:text-[#a8a8a2]">{{ $log->user?->name ?? 'System' }}</td>
                                <td class="px-6 py-4 text-sm text-[#6b6b66] dark:text-[#a8a8a2] max-w-xs truncate">
                                    @if($log->payload)
                                        <code class="text-xs bg-gray-100 dark:bg-[#1a1a18] px-1 py-0.5 rounded">{{ json_encode($log->payload) }}</code>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-[#6b6b66] dark:text-[#a8a8a2]">{{ $log->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-[#6b6b66] dark:text-[#a8a8a2]">No audit logs found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $logs->links() }}</div>
        </div>
    </div>
@endsection
