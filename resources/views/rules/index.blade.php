@extends('layouts.app')
@section('title', 'Rules')

@section('header')
    <h2 class="font-semibold text-xl text-[#111110] dark:text-white leading-tight">Compliance Rules</h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-[#111110] dark:text-white">Compliance Rules</h1>
                <a href="{{ route('rules.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">+ New Rule</a>
            </div>

            <div class="bg-white dark:bg-[#161615] border border-[#e5e5e0] dark:border-[#2a2a28] rounded-xl overflow-hidden">
                <table class="min-w-full divide-y divide-[#e5e5e0] dark:divide-[#2a2a28]">
                    <thead class="bg-gray-50 dark:bg-[#1a1a18]">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#6b6b66] dark:text-[#a8a8a2] uppercase">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#6b6b66] dark:text-[#a8a8a2] uppercase">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#6b6b66] dark:text-[#a8a8a2] uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#6b6b66] dark:text-[#a8a8a2] uppercase">Created by</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#6b6b66] dark:text-[#a8a8a2] uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e5e5e0] dark:divide-[#2a2a28]">
                        @forelse ($rules as $rule)
                            <tr class="hover:bg-gray-50 dark:hover:bg-[#1a1a18]">
                                <td class="px-6 py-4 text-sm font-medium text-[#111110] dark:text-white">{{ $rule->title }}</td>
                                <td class="px-6 py-4 text-sm text-[#6b6b66] dark:text-[#a8a8a2] max-w-md truncate">{{ $rule->description }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($rule->is_active) bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                                        @else bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-300 @endif">
                                        {{ $rule->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-[#6b6b66] dark:text-[#a8a8a2]">{{ $rule->creator?->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('rules.edit', $rule) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-[#6b6b66] dark:text-[#a8a8a2]">No rules yet. Create one!</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $rules->links() }}</div>
        </div>
    </div>
@endsection
