<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            {{-- Left side --}}
            <div class="flex">
                {{-- Logo --}}
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('raffle.index') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                {{-- Desktop nav links --}}
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('raffle.index')"
                                :active="request()->routeIs('raffle.*')">
                        Raffles
                    </x-nav-link>

                    @if (Auth::guard('web')->check())
                        @php $pendingCount = \App\Models\TicketPayment::where('status', 'pending')->count(); @endphp

                        <x-nav-link :href="route('dashboard')"
                                    :active="request()->routeIs('dashboard')">
                            Dashboard
                        </x-nav-link>

                        <x-nav-link :href="route('admin.payments.index')"
                                    :active="request()->routeIs('admin.payments.*')">
                            Payments
                            @if ($pendingCount > 0)
                                <span class="ml-1 px-1.5 py-0.5 bg-yellow-100 text-yellow-700 text-xs rounded-full">
                                    {{ $pendingCount }}
                                </span>
                            @endif
                        </x-nav-link>

                        <x-nav-link :href="route('admin.settings.index')"
                                    :active="request()->routeIs('admin.settings.*')">
                            Settings
                        </x-nav-link>
                    @endif
                </div>
            </div>

            {{-- Right side --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">

                {{-- Admin dropdown --}}
                @if (Auth::guard('web')->check())
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::guard('web')->user()->name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                Profile
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('admin.logout')"
                                        onclick="event.preventDefault();
                                                 this.closest('form').submit();">
                                    Log Out
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endif

                {{-- Sponsor dropdown --}}
                @if (Auth::guard('sponsor')->check() && Auth::guard('sponsor')->user())
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::guard('sponsor')->user()->fullName() }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-2 text-xs text-gray-400 border-b">
                                {{ Auth::guard('sponsor')->user()->email }}
                            </div>

                            <form method="POST" action="{{ route('sponsor.logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('sponsor.logout')"
                                        onclick="event.preventDefault();
                                                 this.closest('form').submit();">
                                    Log Out
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endif

                {{-- Guest links --}}
                @if (! Auth::guard('web')->check() && ! Auth::guard('sponsor')->check())
                    <a href="{{ route('sponsor.login') }}"
                       class="text-sm text-gray-500 hover:text-gray-700 mr-4">
                        Sponsor Login
                    </a>
                    <a href="{{ route('admin.login') }}"
                       class="text-sm text-gray-500 hover:text-gray-700 mr-4">
                        Admin Login
                    </a>
                    <a href="{{ route('admin.register') }}"
                       class="text-sm text-gray-500 hover:text-gray-700">
                        Admin Register
                    </a>
                @endif

            </div>

            {{-- Hamburger --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }"
                              class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }"
                              class="hidden"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    {{-- Mobile menu --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">

        {{-- Mobile nav links --}}
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('raffle.index')"
                                   :active="request()->routeIs('raffle.*')">
                Raffles
            </x-responsive-nav-link>

            @if (Auth::guard('web')->check())
                <x-responsive-nav-link :href="route('dashboard')"
                                       :active="request()->routeIs('dashboard')">
                    Dashboard
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('admin.payments.index')"
                                       :active="request()->routeIs('admin.payments.*')">
                    Payments
                    @if (isset($pendingCount) && $pendingCount > 0)
                        <span class="ml-1 px-1.5 py-0.5 bg-yellow-100 text-yellow-700 text-xs rounded-full">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('admin.settings.index')"
                                       :active="request()->routeIs('admin.settings.*')">
                    Settings
                </x-responsive-nav-link>
            @endif
        </div>

        {{-- Mobile user info --}}
        <div class="pt-4 pb-1 border-t border-gray-200">

            {{-- Admin --}}
            @if (Auth::guard('web')->check())
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">
                        {{ Auth::guard('web')->user()->name }}
                    </div>
                    <div class="font-medium text-sm text-gray-500">
                        {{ Auth::guard('web')->user()->email }}
                    </div>
                </div>
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        Profile
                    </x-responsive-nav-link>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('admin.logout')"
                                onclick="event.preventDefault();
                                         this.closest('form').submit();">
                            Log Out
                        </x-responsive-nav-link>
                    </form>
                </div>
            @endif

            {{-- Sponsor --}}
            @if (Auth::guard('sponsor')->check() && Auth::guard('sponsor')->user())
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">
                        {{ Auth::guard('sponsor')->user()->fullName() }}
                    </div>
                    <div class="font-medium text-sm text-gray-500">
                        {{ Auth::guard('sponsor')->user()->email }}
                    </div>
                </div>
                <div class="mt-3 space-y-1">
                    <form method="POST" action="{{ route('sponsor.logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('sponsor.logout')"
                                onclick="event.preventDefault();
                                         this.closest('form').submit();">
                            Log Out
                        </x-responsive-nav-link>
                    </form>
                </div>
            @endif

            {{-- Guest --}}
            @if (! Auth::guard('web')->check() && ! Auth::guard('sponsor')->check())
                <div class="mt-3 space-y-1 px-4">
                    <a href="{{ route('sponsor.login') }}"
                       class="block text-sm text-gray-500 hover:text-gray-700 py-2">
                        Sponsor Login
                    </a>
                    <a href="{{ route('admin.login') }}"
                       class="block text-sm text-gray-500 hover:text-gray-700 py-2">
                        Admin Login
                    </a>
                    <a href="{{ route('admin.register') }}"
                       class="block text-sm text-gray-500 hover:text-gray-700 py-2">
                        Admin Register
                    </a>
                </div>
            @endif

        </div>
    </div>
</nav>