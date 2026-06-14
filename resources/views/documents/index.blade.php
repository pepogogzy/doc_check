@extends('layouts.app')
@section('title', 'Documents')

@section('header')
    <h2 class="font-semibold text-xl text-[#111110] dark:text-white leading-tight">Documents</h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-[#161615] border border-[#e5e5e0] dark:border-[#2a2a28] rounded-xl p-6 mb-6">
                <h2 class="text-lg font-medium text-[#111110] dark:text-white mb-4">Upload Document</h2>
                <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="flex items-end gap-4">
                    @csrf
                    <div class="flex-1">
                        <input type="file" name="document" required
                               class="block w-full text-sm text-[#6b6b66] dark:text-[#a8a8a2] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-900/30 file:text-indigo-700 dark:file:text-indigo-300 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-900/50">
                        @error('document')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">Upload</button>
                </form>
            </div>

            <div class="bg-white dark:bg-[#161615] border border-[#e5e5e0] dark:border-[#2a2a28] rounded-xl overflow-hidden">
                <table class="min-w-full divide-y divide-[#e5e5e0] dark:divide-[#2a2a28]">
                    <thead class="bg-gray-50 dark:bg-[#1a1a18]">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#6b6b66] dark:text-[#a8a8a2] uppercase">Filename</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#6b6b66] dark:text-[#a8a8a2] uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#6b6b66] dark:text-[#a8a8a2] uppercase">Size</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#6b6b66] dark:text-[#a8a8a2] uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#6b6b66] dark:text-[#a8a8a2] uppercase">Uploaded by</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-[#6b6b66] dark:text-[#a8a8a2] uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e5e5e0] dark:divide-[#2a2a28]">
                        @forelse ($documents as $doc)
                            <tr class="hover:bg-gray-50 dark:hover:bg-[#1a1a18] cursor-pointer" onclick="window.location='{{ route('documents.show', $doc) }}'">
                                <td class="px-6 py-4 text-sm font-medium text-indigo-600 dark:text-indigo-400">{{ $doc->filename }}</td>
                                <td class="px-6 py-4 text-sm text-[#6b6b66] dark:text-[#a8a8a2]">{{ $doc->mime_type }}</td>
                                <td class="px-6 py-4 text-sm text-[#6b6b66] dark:text-[#a8a8a2]">{{ number_format($doc->size / 1024, 1) }} KB</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($doc->status === 'pending') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300
                                        @elseif($doc->status === 'analyzed') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                                        @else bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 @endif">
                                        {{ $doc->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-[#6b6b66] dark:text-[#a8a8a2]">{{ $doc->user?->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-[#6b6b66] dark:text-[#a8a8a2]">{{ $doc->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-8 text-center text-[#6b6b66] dark:text-[#a8a8a2]">No documents yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $documents->links() }}</div>
        </div>
    </div>
@endsection
