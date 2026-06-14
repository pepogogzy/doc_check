@extends('layouts.app')
@section('title', 'Dashboard')

@section('header')
    <h2 class="font-semibold text-xl text-[#111110] dark:text-white leading-tight">
        {{ __('Dashboard') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-6 mb-8">
                <a href="{{ route('documents.index') }}" class="bg-white dark:bg-[#161615] border border-[#e5e5e0] dark:border-[#2a2a28] rounded-xl p-6 hover:shadow-md transition-all hover:-translate-y-0.5">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-[#111110] dark:text-white">Upload Document</h3>
                            <p class="text-sm text-[#6b6b66] dark:text-[#a8a8a2]">Upload a PDF, DOCX, or TXT file for analysis</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('rules.index') }}" class="bg-white dark:bg-[#161615] border border-[#e5e5e0] dark:border-[#2a2a28] rounded-xl p-6 hover:shadow-md transition-all hover:-translate-y-0.5">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-[#111110] dark:text-white">Manage Rules</h3>
                            <p class="text-sm text-[#6b6b66] dark:text-[#a8a8a2]">Create and manage compliance rules</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('audit-logs.index') }}" class="bg-white dark:bg-[#161615] border border-[#e5e5e0] dark:border-[#2a2a28] rounded-xl p-6 hover:shadow-md transition-all hover:-translate-y-0.5">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-[#111110] dark:text-white">Audit Logs</h3>
                            <p class="text-sm text-[#6b6b66] dark:text-[#a8a8a2]">View analysis history and results</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="bg-white dark:bg-[#161615] border border-[#e5e5e0] dark:border-[#2a2a28] rounded-xl">
                <div class="p-6 text-[#6b6b66] dark:text-[#a8a8a2]">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
@endsection
