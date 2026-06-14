<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DockCheck – Document Compliance Analysis</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-neutral-950 text-white antialiased">
<div class="min-h-screen bg-neutral-950 text-white">
    <header class="border-b border-white/10 bg-neutral-950/90 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
            <a href="/" class="text-lg font-semibold tracking-tight text-white">DockCheck</a>
            <nav class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-full border border-white/15 bg-white/5 px-4 py-2 text-sm font-medium text-white/90 hover:bg-white/10">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="rounded-full px-4 py-2 text-sm font-medium text-white/70 hover:text-white">
                        Log in
                    </a>
                    <a href="{{ route('register') }}" class="rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-neutral-950 hover:bg-indigo-100">
                        Start free
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-6 py-16 lg:px-8 lg:py-24">
        <section class="grid gap-16 lg:grid-cols-[1.15fr_0.85fr] lg:items-center">
            <div>
                <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-indigo-400/20 bg-indigo-400/10 px-4 py-1.5 text-xs font-semibold tracking-[0.18em] text-indigo-200 uppercase">
                    <span class="h-2 w-2 rounded-full bg-indigo-400"></span>
                    AI-powered document compliance
                </div>

                <h1 class="max-w-3xl text-5xl font-semibold tracking-tight text-white sm:text-6xl">
                    Check every document
                    <span class="block text-white/60">against your rules in minutes.</span>
                </h1>

                <p class="mt-6 max-w-2xl text-lg leading-8 text-white/65">
                    Upload contracts, policies, reports, or internal files, define the compliance logic that matters to your team, and let DockCheck surface gaps, risks, and inconsistencies automatically.
                </p>

                <div class="mt-10 flex flex-col gap-4 sm:flex-row">
                    @guest
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full bg-indigo-500 px-6 py-3 text-sm font-semibold text-white hover:bg-indigo-400">
                            Create workspace
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-full border border-white/15 bg-white/5 px-6 py-3 text-sm font-medium text-white/85 hover:bg-white/10">
                            Sign in
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-full bg-indigo-500 px-6 py-3 text-sm font-semibold text-white hover:bg-indigo-400">
                            Open dashboard
                        </a>
                    @endguest
                </div>
            </div>

            <div class="rounded-[2rem] border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/30">
                <div class="rounded-2xl border border-white/10 bg-neutral-900/80 p-5">
                    <div class="flex items-center justify-between">
                        <div class="flex gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-rose-400/80"></span>
                            <span class="h-2.5 w-2.5 rounded-full bg-amber-300/80"></span>
                            <span class="h-2.5 w-2.5 rounded-full bg-emerald-400/80"></span>
                        </div>
                        <span class="text-xs tracking-[0.16em] text-white/45 uppercase">Analysis Preview</span>
                    </div>

                    <div class="mt-5 space-y-4">
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <p class="text-sm font-medium text-white">Vendor-Contract-Q2.pdf</p>
                            <p class="mt-1 text-xs text-white/45">Uploaded 2 minutes ago</p>
                        </div>

                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold text-white">Compliance score</p>
                                <span class="text-xs text-white/45">8 active rules</span>
                            </div>
                            <div class="mt-3 text-4xl font-semibold text-white">87%</div>
                            <div class="mt-4 h-2 overflow-hidden rounded-full bg-white/10">
                                <div class="h-full w-[87%] rounded-full bg-gradient-to-r from-indigo-400 to-sky-400"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>
</body>
</html>
