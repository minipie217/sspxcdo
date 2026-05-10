<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $settings = $settings ?? [];
            $defaults = [
                'site_name' => config('app.name', 'RafflePress'),
                'site_logo' => null,
                'homepage_badge' => 'Raffles, sponsors, tickets, and payments in one flow',
                'homepage_hero_background' => null,
                'homepage_hero_title' => 'Build a raffle page that sells trust first.',
                'homepage_hero_body' => 'Give sponsors a clear public experience, guide them into ticket selection, and keep the admin side organized from launch to draw day.',
                'homepage_primary_cta' => 'View active raffles',
                'homepage_secondary_cta' => 'Become a sponsor',
                'homepage_feature_intro' => 'Built in sections',
                'homepage_feature_heading' => 'Every part of the homepage has a clear job.',
                'homepage_feature_one_title' => 'Public confidence',
                'homepage_feature_one_body' => 'Lead with prize clarity, draw timing, ticket counts, and direct routes into active raffles.',
                'homepage_feature_two_title' => 'Sponsor conversion',
                'homepage_feature_two_body' => 'Make registration and ticket reservation feel like one connected campaign journey.',
                'homepage_feature_three_title' => 'Admin momentum',
                'homepage_feature_three_body' => 'Surface the operational pieces that matter: raffles, payments, availability, and status.',
                'homepage_workflow_heading' => 'From first visit to confirmed ticket.',
                'homepage_workflow_body' => 'The homepage frames the app like a real product, then moves visitors toward the actions your Laravel routes already support.',
                'homepage_sections_heading' => 'A Shopify-style rhythm without copying Shopify.',
                'homepage_sections_body' => 'Large bands, focused messages, strong calls to action, and repeated visual blocks give the page a commercial feel while keeping the content specific to raffle management.',
                'homepage_final_cta_heading' => 'Ready to send visitors into the raffle flow?',
                'homepage_final_cta_body' => 'Use the homepage as the front door for sponsors while admins keep running raffles from the dashboard.',
                'homepage_stat_tickets' => '2,500',
                'homepage_stat_sold' => '1,842',
                'homepage_stat_price' => 'P100',
            ];

            $content = $defaults;
            foreach ($settings as $key => $value) {
                $content[$key] = $value;
            }

            $siteName = filled($content['site_name']) ? $content['site_name'] : config('app.name', 'RafflePress');
            $has = fn (string $key): bool => filled($content[$key] ?? null);

            $stats = collect([
                ['label' => 'Tickets', 'value' => $content['homepage_stat_tickets']],
                ['label' => 'Sold', 'value' => $content['homepage_stat_sold']],
                ['label' => 'Price', 'value' => $content['homepage_stat_price']],
            ])->filter(fn ($stat) => filled($stat['value']));
            $statsGridClass = match ($stats->count()) {
                1 => 'sm:grid-cols-1',
                2 => 'sm:grid-cols-2',
                default => 'sm:grid-cols-3',
            };

            $featureCards = collect([
                ['title' => $content['homepage_feature_one_title'], 'body' => $content['homepage_feature_one_body'], 'class' => 'bg-[#f6f1e8]', 'mark' => 'bg-[#073f2d]'],
                ['title' => $content['homepage_feature_two_title'], 'body' => $content['homepage_feature_two_body'], 'class' => 'bg-[#ecf7f0]', 'mark' => 'bg-[#d6ff61]'],
                ['title' => $content['homepage_feature_three_title'], 'body' => $content['homepage_feature_three_body'], 'class' => 'bg-[#fff7df]', 'mark' => 'bg-[#ffb84d]'],
            ])->filter(fn ($card) => filled($card['title']) || filled($card['body']));

            $homeLayout = $homeLayout ?? [
                'sections' => [
                    ['key' => 'hero', 'label' => 'Hero Banner', 'visible' => true],
                    ['key' => 'features', 'label' => 'Features', 'visible' => true],
                    ['key' => 'workflow', 'label' => 'Workflow', 'visible' => true],
                    ['key' => 'section_system', 'label' => 'Section System', 'visible' => true],
                    ['key' => 'testimonials', 'label' => 'Testimonials', 'visible' => true],
                    ['key' => 'final_cta', 'label' => 'Final CTA', 'visible' => true],
                ],
            ];

            $layoutSections = collect($homeLayout['sections'] ?? [])->keyBy('key');
            $layoutVisible = fn (string $key): bool => (bool) data_get($layoutSections->get($key), 'visible', true);

            $showHero = $layoutVisible('hero') && (
                $has('homepage_badge') ||
                $has('homepage_hero_title') ||
                $has('homepage_hero_body') ||
                $has('homepage_primary_cta') ||
                $has('homepage_secondary_cta') ||
                $has('homepage_hero_background')
            );
            $showFeatures = $layoutVisible('features') && ($has('homepage_feature_intro') || $has('homepage_feature_heading') || $featureCards->isNotEmpty());
            $showWorkflow = $layoutVisible('workflow') && ($has('homepage_workflow_heading') || $has('homepage_workflow_body'));
            $showSections = $layoutVisible('section_system') && ($has('homepage_sections_heading') || $has('homepage_sections_body'));
            $showTestimonials = $layoutVisible('testimonials');
            $showFinalCta = $layoutVisible('final_cta') && ($has('homepage_final_cta_heading') || $has('homepage_final_cta_body'));

            $sectionRegistry = [
                'hero' => ['label' => 'Hero', 'anchor' => 'hero', 'visible' => $showHero],
                'features' => ['label' => 'Features', 'anchor' => 'features', 'visible' => $showFeatures],
                'workflow' => ['label' => 'Workflow', 'anchor' => 'workflow', 'visible' => $showWorkflow],
                'section_system' => ['label' => 'Sections', 'anchor' => 'sections', 'visible' => $showSections],
                'testimonials' => ['label' => 'Testimonials', 'anchor' => 'testimonials', 'visible' => $showTestimonials],
                'final_cta' => ['label' => 'Get Started', 'anchor' => 'final-cta', 'visible' => $showFinalCta],
            ];

            $orderedSections = collect($homeLayout['sections'] ?? [])
                ->pluck('key')
                ->filter(fn ($section) => is_string($section) && array_key_exists($section, $sectionRegistry))
                ->unique()
                ->values()
                ->all();
            $visibleOrderedSections = array_values(array_filter(
                $orderedSections,
                fn ($section) => $sectionRegistry[$section]['visible'] ?? false
            ));
        @endphp

        <title>{{ $siteName }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-stone-950 bg-[#f6f1e8]">
        <div class="min-h-screen overflow-hidden">
            <header class="sticky top-0 z-50 border-b border-stone-900/10 bg-[#f6f1e8]/90 backdrop-blur">
                <nav class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 lg:px-8" aria-label="Main navigation">
                    <a href="{{ url('/') }}" class="flex items-center gap-3">
                        @if ($has('site_logo'))
                            <img src="{{ Storage::url($content['site_logo']) }}" alt="{{ $siteName }}" class="h-10 w-auto rounded-md object-contain">
                        @else
                            <span class="grid h-10 w-10 place-items-center rounded-lg bg-[#073f2d] text-lg font-black text-white">{{ strtoupper(substr($siteName, 0, 1)) }}</span>
                        @endif
                        <span class="text-lg font-extrabold tracking-normal">{{ $siteName }}</span>
                    </a>

                    <div class="hidden items-center gap-8 text-sm font-semibold text-stone-700 md:flex">
                        @foreach ($visibleOrderedSections as $section)
                            <a href="#{{ $sectionRegistry[$section]['anchor'] }}" class="hover:text-stone-950">{{ $sectionRegistry[$section]['label'] }}</a>
                        @endforeach
                        <a href="{{ route('raffle.index') }}" class="hover:text-stone-950">Browse raffles</a>
                    </div>

                    <div class="flex items-center gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="hidden rounded-lg px-4 py-2 text-sm font-bold text-stone-800 hover:bg-white/70 sm:inline-flex">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('admin.login') }}" class="hidden rounded-lg px-4 py-2 text-sm font-bold text-stone-800 hover:bg-white/70 sm:inline-flex">
                                Admin login
                            </a>
                        @endauth

                        <a href="{{ route('sponsor.register') }}" class="rounded-lg bg-[#073f2d] px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-[#0b513b]">
                            Sponsor
                        </a>
                    </div>
                </nav>
            </header>

            <main>
                @foreach ($visibleOrderedSections as $section)
                    @switch($section)
                        @case('hero')
                @if ($has('homepage_hero_background'))
                    <section id="hero" class="relative min-h-[680px] bg-cover bg-center text-white" style="background-image: url('{{ Storage::url($content['homepage_hero_background']) }}')">
                        <div class="absolute inset-0 bg-[#073f2d]/70"></div>
                        <div class="relative mx-auto flex min-h-[680px] max-w-7xl items-center px-5 py-20 lg:px-8">
                            <div class="max-w-3xl">
                                @if ($has('homepage_badge'))
                                    <p class="mb-5 w-fit rounded-full border border-white/25 bg-white/15 px-4 py-2 text-sm font-bold text-white backdrop-blur">
                                        {{ $content['homepage_badge'] }}
                                    </p>
                                @endif
                                @if ($has('homepage_hero_title'))
                                    <h1 class="text-5xl font-extrabold leading-[1.02] tracking-normal sm:text-6xl lg:text-7xl">
                                        {{ $content['homepage_hero_title'] }}
                                    </h1>
                                @endif
                                @if ($has('homepage_hero_body'))
                                    <p class="mt-6 max-w-2xl text-lg leading-8 text-white/85">
                                        {{ $content['homepage_hero_body'] }}
                                    </p>
                                @endif
                                @if ($has('homepage_primary_cta') || $has('homepage_secondary_cta'))
                                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                                        @if ($has('homepage_primary_cta'))
                                            <a href="{{ route('raffle.index') }}" class="inline-flex items-center justify-center rounded-lg bg-[#d6ff61] px-6 py-3 text-base font-bold text-[#073f2d] transition hover:bg-white">
                                                {{ $content['homepage_primary_cta'] }}
                                            </a>
                                        @endif
                                        @if ($has('homepage_secondary_cta'))
                                            <a href="{{ route('sponsor.register') }}" class="inline-flex items-center justify-center rounded-lg border border-white/35 bg-white/10 px-6 py-3 text-base font-bold text-white backdrop-blur transition hover:bg-white/20">
                                                {{ $content['homepage_secondary_cta'] }}
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </section>
                @else
                <section id="hero" class="relative bg-[#f6f1e8]">
                    <div class="mx-auto grid max-w-7xl gap-12 px-5 py-16 lg:grid-cols-[0.92fr_1.08fr] lg:px-8 lg:py-24">
                        <div class="flex flex-col justify-center">
                            @if ($has('homepage_badge'))
                                <p class="mb-5 w-fit rounded-full border border-[#073f2d]/20 bg-white/65 px-4 py-2 text-sm font-bold text-[#073f2d]">
                                    {{ $content['homepage_badge'] }}
                                </p>
                            @endif
                            @if ($has('homepage_hero_title'))
                                <h1 class="max-w-4xl text-5xl font-extrabold leading-[1.02] tracking-normal text-[#073f2d] sm:text-6xl lg:text-7xl">
                                    {{ $content['homepage_hero_title'] }}
                                </h1>
                            @endif
                            @if ($has('homepage_hero_body'))
                                <p class="mt-6 max-w-2xl text-lg leading-8 text-stone-700">
                                    {{ $content['homepage_hero_body'] }}
                                </p>
                            @endif
                            @if ($has('homepage_primary_cta') || $has('homepage_secondary_cta'))
                                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                                    @if ($has('homepage_primary_cta'))
                                        <a href="{{ route('raffle.index') }}" class="inline-flex items-center justify-center rounded-lg bg-[#073f2d] px-6 py-3 text-base font-bold text-white transition hover:bg-[#0b513b]">
                                            {{ $content['homepage_primary_cta'] }}
                                        </a>
                                    @endif
                                    @if ($has('homepage_secondary_cta'))
                                        <a href="{{ route('sponsor.register') }}" class="inline-flex items-center justify-center rounded-lg border border-stone-300 bg-white px-6 py-3 text-base font-bold text-stone-950 transition hover:border-stone-500">
                                            {{ $content['homepage_secondary_cta'] }}
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="relative">
                            <div class="rounded-lg border border-stone-900/10 bg-white p-3 shadow-2xl shadow-stone-900/10">
                                <div class="overflow-hidden rounded-md bg-[#073f2d]">
                                    <div class="grid gap-4 p-5 sm:grid-cols-[1.1fr_0.9fr]">
                                        <div class="rounded-md bg-[#fff7df] p-5">
                                            <div class="mb-8 flex items-center justify-between">
                                                <span class="rounded-full bg-[#073f2d] px-3 py-1 text-xs font-bold text-white">Live raffle</span>
                                                <span class="text-sm font-bold text-stone-600">Draw in 12 days</span>
                                            </div>
                                            <h2 class="text-3xl font-extrabold leading-tight text-stone-950">Community Grand Prize Fund</h2>
                                            <p class="mt-4 text-sm leading-6 text-stone-700">A polished public campaign area with details, prizes, sponsor actions, and ticket availability.</p>
                                            @if ($stats->isNotEmpty())
                                                <div class="mt-8 grid gap-3 {{ $statsGridClass }}">
                                                    @foreach ($stats as $stat)
                                                        <div class="rounded-md bg-white p-3">
                                                            <p class="text-xs font-bold text-stone-500">{{ $stat['label'] }}</p>
                                                            <p class="text-xl font-extrabold">{{ $stat['value'] }}</p>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>

                                        <div class="space-y-4">
                                            <div class="rounded-md bg-white p-4">
                                                <p class="text-sm font-bold text-stone-500">Ticket picker</p>
                                                <div class="mt-4 grid grid-cols-5 gap-2">
                                                    @foreach (range(1, 25) as $ticket)
                                                        <span @class([
                                                            'grid aspect-square place-items-center rounded text-xs font-bold',
                                                            'bg-[#073f2d] text-white' => in_array($ticket, [3, 7, 8, 14, 19]),
                                                            'bg-stone-100 text-stone-500' => ! in_array($ticket, [3, 7, 8, 14, 19]),
                                                        ])>{{ $ticket }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="rounded-md bg-[#d6ff61] p-4">
                                                <p class="text-sm font-extrabold text-[#073f2d]">Payment proof ready</p>
                                                <p class="mt-2 text-sm text-[#073f2d]">Sponsors submit proof and admins confirm each ticket from the dashboard.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                @endif
                            @break

                        @case('features')
                            <section id="features" class="bg-white py-20">
                                <div class="mx-auto max-w-7xl px-5 lg:px-8">
                                    @if ($has('homepage_feature_intro') || $has('homepage_feature_heading'))
                                        <div class="max-w-3xl">
                                            @if ($has('homepage_feature_intro'))
                                                <p class="text-sm font-extrabold uppercase tracking-normal text-[#0f7b56]">{{ $content['homepage_feature_intro'] }}</p>
                                            @endif
                                            @if ($has('homepage_feature_heading'))
                                                <h2 class="mt-3 text-4xl font-extrabold tracking-normal text-stone-950 sm:text-5xl">{{ $content['homepage_feature_heading'] }}</h2>
                                            @endif
                                        </div>
                                    @endif

                                    @if ($featureCards->isNotEmpty())
                                        <div class="mt-12 grid gap-4 md:grid-cols-3">
                                            @foreach ($featureCards as $card)
                                                <article class="rounded-lg border border-stone-200 {{ $card['class'] }} p-6">
                                                    <div class="mb-8 h-12 w-12 rounded-lg {{ $card['mark'] }}"></div>
                                                    @if (filled($card['title']))
                                                        <h3 class="text-xl font-extrabold">{{ $card['title'] }}</h3>
                                                    @endif
                                                    @if (filled($card['body']))
                                                        <p class="mt-3 leading-7 text-stone-700">{{ $card['body'] }}</p>
                                                    @endif
                                                </article>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </section>
                            @break

                        @case('workflow')
                            <section id="workflow" class="bg-[#f6f1e8] py-20">
                                <div class="mx-auto grid max-w-7xl gap-10 px-5 lg:grid-cols-[0.8fr_1.2fr] lg:px-8">
                                    <div>
                                        <p class="text-sm font-extrabold uppercase tracking-normal text-[#0f7b56]">Simple workflow</p>
                                        @if ($has('homepage_workflow_heading'))
                                            <h2 class="mt-3 text-4xl font-extrabold tracking-normal text-stone-950">{{ $content['homepage_workflow_heading'] }}</h2>
                                        @endif
                                        @if ($has('homepage_workflow_body'))
                                            <p class="mt-5 leading-8 text-stone-700">{{ $content['homepage_workflow_body'] }}</p>
                                        @endif
                                    </div>

                                    <div class="grid gap-4 sm:grid-cols-2">
                                        @foreach ([
                                            ['01', 'Create a raffle', 'Admins publish raffle details, prizes, ticket pricing, and draw dates.'],
                                            ['02', 'Invite sponsors', 'Sponsors register, verify, and browse public raffle pages.'],
                                            ['03', 'Reserve tickets', 'Sponsors pick available ticket numbers and hold them while they pay.'],
                                            ['04', 'Confirm payments', 'Admins review proof, confirm payments, and keep records clean.'],
                                        ] as [$step, $title, $copy])
                                            <div class="rounded-lg border border-stone-900/10 bg-white p-6">
                                                <p class="text-sm font-black text-[#0f7b56]">{{ $step }}</p>
                                                <h3 class="mt-5 text-xl font-extrabold">{{ $title }}</h3>
                                                <p class="mt-3 leading-7 text-stone-700">{{ $copy }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </section>
                            @break

                        @case('section_system')
                            <section id="sections" class="bg-[#073f2d] py-20 text-white">
                                <div class="mx-auto max-w-7xl px-5 lg:px-8">
                                    <div class="grid gap-10 lg:grid-cols-[1fr_1fr] lg:items-center">
                                        <div>
                                            <p class="text-sm font-extrabold uppercase tracking-normal text-[#d6ff61]">Section system</p>
                                            @if ($has('homepage_sections_heading'))
                                                <h2 class="mt-3 text-4xl font-extrabold tracking-normal sm:text-5xl">{{ $content['homepage_sections_heading'] }}</h2>
                                            @endif
                                            @if ($has('homepage_sections_body'))
                                                <p class="mt-5 leading-8 text-white/75">{{ $content['homepage_sections_body'] }}</p>
                                            @endif
                                        </div>

                                        <div class="grid gap-4">
                                            <div class="rounded-lg bg-white p-5 text-stone-950">
                                                <div class="flex items-center justify-between gap-4">
                                                    <div>
                                                        <p class="text-sm font-bold text-stone-500">Homepage section</p>
                                                        <h3 class="mt-1 text-2xl font-extrabold">Campaign proof</h3>
                                                    </div>
                                                    <span class="rounded-full bg-[#d6ff61] px-3 py-1 text-sm font-black text-[#073f2d]">Live</span>
                                                </div>
                                            </div>
                                            <div class="grid gap-4 sm:grid-cols-2">
                                                <div class="rounded-lg bg-[#ecf7f0] p-5 text-stone-950">
                                                    <p class="text-sm font-bold text-stone-500">Ticket status</p>
                                                    <p class="mt-8 text-3xl font-black">74%</p>
                                                </div>
                                                <div class="rounded-lg bg-[#fff7df] p-5 text-stone-950">
                                                    <p class="text-sm font-bold text-stone-500">Admin queue</p>
                                                    <p class="mt-8 text-3xl font-black">18</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            @break

                        @case('testimonials')
                            <section id="testimonials" class="bg-white py-20">
                                <div class="mx-auto max-w-7xl px-5 lg:px-8">
                                    <div class="grid gap-4 lg:grid-cols-3">
                                        <figure class="rounded-lg border border-stone-200 p-6">
                                            <blockquote class="text-xl font-bold leading-8 text-stone-950">"The sponsor journey is clear from the first screen. Nobody has to guess where tickets or payments live."</blockquote>
                                            <figcaption class="mt-6 text-sm font-bold text-stone-500">Campaign coordinator</figcaption>
                                        </figure>
                                        <figure class="rounded-lg border border-stone-200 p-6">
                                            <blockquote class="text-xl font-bold leading-8 text-stone-950">"The layout feels like a product homepage, but every section still points to the raffle workflow."</blockquote>
                                            <figcaption class="mt-6 text-sm font-bold text-stone-500">Admin operator</figcaption>
                                        </figure>
                                        <figure class="rounded-lg border border-stone-200 p-6">
                                            <blockquote class="text-xl font-bold leading-8 text-stone-950">"Sponsors can register, choose tickets, and understand the payment step without extra instructions."</blockquote>
                                            <figcaption class="mt-6 text-sm font-bold text-stone-500">Sponsor lead</figcaption>
                                        </figure>
                                    </div>
                                </div>
                            </section>
                            @break

                        @case('final_cta')
                            <section id="final-cta" class="bg-[#d6ff61] py-20">
                                <div class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-8 px-5 lg:flex-row lg:items-center lg:px-8">
                                    <div class="max-w-3xl">
                                        @if ($has('homepage_final_cta_heading'))
                                            <h2 class="text-4xl font-extrabold tracking-normal text-[#073f2d] sm:text-5xl">{{ $content['homepage_final_cta_heading'] }}</h2>
                                        @endif
                                        @if ($has('homepage_final_cta_body'))
                                            <p class="mt-5 text-lg leading-8 text-[#073f2d]/80">{{ $content['homepage_final_cta_body'] }}</p>
                                        @endif
                                    </div>
                                    <div class="flex flex-col gap-3 sm:flex-row">
                                        <a href="{{ route('raffle.index') }}" class="inline-flex items-center justify-center rounded-lg bg-[#073f2d] px-6 py-3 text-base font-bold text-white transition hover:bg-[#0b513b]">
                                            Browse raffles
                                        </a>
                                        <a href="{{ route('admin.login') }}" class="inline-flex items-center justify-center rounded-lg border border-[#073f2d]/30 bg-white/80 px-6 py-3 text-base font-bold text-[#073f2d] transition hover:bg-white">
                                            Admin access
                                        </a>
                                    </div>
                                </div>
                            </section>
                            @break
                    @endswitch
                @endforeach
            </main>

            <footer class="bg-stone-950 py-10 text-white">
                <div class="mx-auto flex max-w-7xl flex-col justify-between gap-6 px-5 text-sm lg:flex-row lg:items-center lg:px-8">
                    <p class="font-bold">{{ $siteName }}</p>
                    <div class="flex flex-wrap gap-5 text-white/70">
                        <a href="{{ route('raffle.index') }}" class="hover:text-white">Raffles</a>
                        <a href="{{ route('sponsor.login') }}" class="hover:text-white">Sponsor login</a>
                        <a href="{{ route('admin.login') }}" class="hover:text-white">Admin login</a>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
