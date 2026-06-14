@extends('layouts.app')
@section('title', $document->filename)

@section('header')
    <h2 class="font-semibold text-xl text-[#111110] dark:text-white leading-tight">{{ $document->filename }}</h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('documents.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">&larr; Back to Documents</a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-[#161615] border border-[#e5e5e0] dark:border-[#2a2a28] rounded-xl p-6">
                        <h2 class="text-lg font-semibold text-[#111110] dark:text-white mb-4">File Info</h2>
                        <dl class="space-y-3">
                            <div><dt class="text-xs text-[#6b6b66] dark:text-[#a8a8a2] uppercase">Filename</dt><dd class="text-sm text-[#111110] dark:text-white">{{ $document->filename }}</dd></div>
                            <div><dt class="text-xs text-[#6b6b66] dark:text-[#a8a8a2] uppercase">MIME Type</dt><dd class="text-sm text-[#111110] dark:text-white">{{ $document->mime_type }}</dd></div>
                            <div><dt class="text-xs text-[#6b6b66] dark:text-[#a8a8a2] uppercase">Size</dt><dd class="text-sm text-[#111110] dark:text-white">{{ number_format($document->size / 1024, 1) }} KB</dd></div>
                            <div><dt class="text-xs text-[#6b6b66] dark:text-[#a8a8a2] uppercase">Status</dt>
                                <dd>
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($document->status === 'pending') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300
                                        @elseif($document->status === 'analyzed') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                                        @else bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 @endif">
                                        {{ $document->status }}
                                    </span>
                                </dd>
                            </div>
                            <div><dt class="text-xs text-[#6b6b66] dark:text-[#a8a8a2] uppercase">Uploaded by</dt><dd class="text-sm text-[#111110] dark:text-white">{{ $document->user?->name ?? 'N/A' }}</dd></div>
                            <div><dt class="text-xs text-[#6b6b66] dark:text-[#a8a8a2] uppercase">Uploaded at</dt><dd class="text-sm text-[#111110] dark:text-white">{{ $document->created_at->format('Y-m-d H:i') }}</dd></div>
                        </dl>

                        @if($document->status === 'pending')
                            <form action="{{ route('documents.analyze', $document) }}" method="POST" class="mt-6">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">Run Analysis</button>
                            </form>
                        @else
                            <form action="{{ route('documents.analyze', $document) }}" method="POST" class="mt-6">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 text-sm">Re-analyze</button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-6">
                    @if($document->analysis)
                        <div class="bg-white dark:bg-[#161615] border border-[#e5e5e0] dark:border-[#2a2a28] rounded-xl p-6">
                            <h2 class="text-lg font-semibold text-[#111110] dark:text-white mb-4">Summary</h2>
                            <p class="text-sm text-[#6b6b66] dark:text-[#a8a8a2]">{{ $document->analysis->summary }}</p>
                        </div>

                        <div class="bg-white dark:bg-[#161615] border border-[#e5e5e0] dark:border-[#2a2a28] rounded-xl p-6">
                            <h2 class="text-lg font-semibold text-[#111110] dark:text-white mb-4">Key Points</h2>
                            @if(count($document->analysis->key_points ?? []) > 0)
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach($document->analysis->key_points as $point)
                                        <li class="text-sm text-[#6b6b66] dark:text-[#a8a8a2]">{{ $point }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-[#6b6b66] dark:text-[#a8a8a2]">No key points extracted.</p>
                            @endif
                        </div>

                        <div class="bg-white dark:bg-[#161615] border border-[#e5e5e0] dark:border-[#2a2a28] rounded-xl p-6">
                            <h2 class="text-lg font-semibold text-[#111110] dark:text-white mb-4">
                                Inconsistencies
                                <span class="ml-2 px-2 py-0.5 text-xs rounded-full
                                    @if(count($document->analysis->inconsistencies ?? []) > 0) bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300
                                    @else bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 @endif">
                                    {{ count($document->analysis->inconsistencies ?? []) }} found
                                </span>
                            </h2>

                            @forelse($document->analysis->inconsistencies ?? [] as $inc)
                                <div class="border rounded-lg p-4 mb-3 @if($inc['severity'] === 'high') border-red-300 dark:border-red-800 bg-red-50 dark:bg-red-900/20
                                    @elseif($inc['severity'] === 'medium') border-yellow-300 dark:border-yellow-800 bg-yellow-50 dark:bg-yellow-900/20
                                    @else border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800/20 @endif">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h3 class="text-sm font-medium text-[#111110] dark:text-white">{{ $inc['rule_title'] }}</h3>
                                            <p class="text-sm text-[#6b6b66] dark:text-[#a8a8a2] mt-1">{{ $inc['description'] }}</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs rounded-full
                                            @if($inc['severity'] === 'high') bg-red-200 dark:bg-red-900/40 text-red-800 dark:text-red-300
                                            @elseif($inc['severity'] === 'medium') bg-yellow-200 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-300
                                            @else bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-300 @endif">
                                            {{ $inc['severity'] }}
                                        </span>
                                    </div>
                                    @if($inc['rule_id'])
                                        <p class="text-xs text-[#6b6b66] dark:text-[#a8a8a2] mt-2">Rule #{{ $inc['rule_id'] }}</p>
                                    @endif
                                </div>
                            @empty
                                <p class="text-sm text-[#6b6b66] dark:text-[#a8a8a2]">No inconsistencies detected.</p>
                            @endforelse
                        </div>
                    @else
                        <div class="bg-white dark:bg-[#161615] border border-[#e5e5e0] dark:border-[#2a2a28] rounded-xl p-6 text-center text-[#6b6b66] dark:text-[#a8a8a2]">
                            <p>No analysis available yet. Click "Run Analysis" to start.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
