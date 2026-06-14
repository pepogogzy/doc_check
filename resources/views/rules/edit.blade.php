@extends('layouts.app')
@section('title', 'Edit Rule')

@section('header')
    <h2 class="font-semibold text-xl text-[#111110] dark:text-white leading-tight">Edit Rule</h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('rules.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">&larr; Back to Rules</a>
            </div>

            <div class="bg-white dark:bg-[#161615] border border-[#e5e5e0] dark:border-[#2a2a28] rounded-xl p-8 max-w-2xl">
                <h1 class="text-2xl font-semibold text-[#111110] dark:text-white mb-6">Edit Rule</h1>
                <form action="{{ route('rules.update', $rule) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-[#111110] dark:text-white mb-1">Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $rule->title) }}" required
                               class="w-full border border-[#e5e5e0] dark:border-[#3a3a38] bg-white dark:bg-[#161615] text-[#111110] dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-[#111110] dark:text-white mb-1">Description</label>
                        <textarea name="description" id="description" rows="6" required
                                  class="w-full border border-[#e5e5e0] dark:border-[#3a3a38] bg-white dark:bg-[#161615] text-[#111110] dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $rule->description) }}</textarea>
                        @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $rule->is_active))
                                   class="rounded border-[#e5e5e0] dark:border-[#3a3a38] text-indigo-600 shadow-sm focus:ring-indigo-500 bg-white dark:bg-[#161615]">
                            <span class="ml-2 text-sm text-[#111110] dark:text-white">Active</span>
                        </label>
                    </div>

                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">Update Rule</button>
                </form>
            </div>
        </div>
    </div>
@endsection
