<!DOCTYPE html>
<html lang="id" class="h-full bg-[#0B0F17] text-slate-100 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'KostOS Enterprise — Modern Property Management' }}</title>
    
    <!-- Google Fonts: Plus Jakarta Sans & Inter for World-Class SaaS Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#EEF2FF',
                            100: '#E0E7FF',
                            500: '#6366F1',
                            600: '#4F46E5',
                            700: '#4338CA',
                        },
                        surface: {
                            base: '#0B0F17',
                            card: '#111827',
                            cardAlt: '#151E31',
                            border: 'rgba(255, 255, 255, 0.08)',
                            borderLight: 'rgba(255, 255, 255, 0.14)',
                        }
                    },
                    boxShadow: {
                        'glow-emerald': '0 0 25px -5px rgba(16, 185, 129, 0.25)',
                        'glow-indigo': '0 0 25px -5px rgba(99, 102, 241, 0.25)',
                        'subtle-card': '0 4px 20px -2px rgba(0, 0, 0, 0.35)',
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        /* Modern sleek scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #0B0F17;
        }
        ::-webkit-scrollbar-thumb {
            background: #1F293D;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #334155;
        }
        [x-cloak] { display: none !important; }
    </style>
    
    @livewireStyles
</head>
<body class="h-full bg-[#0B0F17] text-slate-200 antialiased font-sans selection:bg-indigo-500/30 selection:text-indigo-200"
      x-data="{ mobileMenuOpen: false, searchOpen: false, notifOpen: false }">

    <div class="flex min-h-screen">
        
        <!-- MOBILE SIDEBAR BACKDROP -->
        <div x-show="mobileMenuOpen" 
             x-cloak
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="mobileMenuOpen = false" 
             class="fixed inset-0 z-40 bg-black/70 backdrop-blur-sm lg:hidden">
        </div>

        <!-- SIDEBAR NAVIGATION -->
        <aside :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 left-0 z-50 w-72 bg-[#0E1422] border-r border-slate-800/80 flex flex-col justify-between transition-transform duration-300 ease-in-out lg:static lg:translate-x-0">
            
            <div class="flex-1 overflow-y-auto px-5 py-6 space-y-6">
                
                <!-- BRAND & APP IDENTITY -->
                <div class="flex items-center justify-between pb-5 border-b border-slate-800/80">
                    <div class="flex items-center gap-3">
                        <div class="relative flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-emerald-400 p-[1px] shadow-glow-indigo">
                            <div class="w-full h-full bg-[#0E1422] rounded-[11px] flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                    <polyline points="9 22 9 12 15 12 15 22"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-extrabold text-sm tracking-tight text-white">KOST<span class="text-indigo-400">OS</span></span>
                                <span class="px-1.5 py-0.5 text-[9px] font-bold rounded bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 uppercase tracking-widest font-mono">PRO</span>
                            </div>
                            <p class="text-[11px] text-slate-400 font-medium">Property Portfolio Suite</p>
                        </div>
                    </div>
                    <!-- Close button on mobile -->
                    <button @click="mobileMenuOpen = false" class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/60">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- ACTIVE PROPERTY CARD / SELECTOR -->
                <div class="p-3.5 rounded-xl bg-gradient-to-br from-slate-900/90 to-slate-950 border border-slate-800/80 shadow-subtle-card">
                    <div class="flex items-center justify-between text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-1.5">
                        <span class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Properti Aktif
                        </span>
                        <span class="text-slate-500">IDR</span>
                    </div>
                    <div class="font-bold text-xs text-white truncate">
                        {{ \Illuminate\Support\Facades\Schema::hasTable('properties') ? (\App\Models\Property::first()?->name ?? 'Kost & Kontrakan Utama') : 'Kost & Kontrakan Utama' }}
                    </div>
                    <div class="mt-1 flex items-center justify-between text-[11px] text-slate-400">
                        <span>{{ \Illuminate\Support\Facades\Schema::hasTable('rooms') ? \App\Models\Room::count() : 0 }} Total Unit</span>
                        <span class="text-emerald-400 font-medium">{{ \Illuminate\Support\Facades\Schema::hasTable('rooms') ? \App\Models\Room::where('status', 'OCCUPIED')->count() : 0 }} Terisi</span>
                    </div>
                </div>

                <!-- MAIN NAVIGATION SECTION -->
                <div class="space-y-1">
                    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Platform Core</p>

                    <a href="{{ route('dashboard') }}" 
                       class="group flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('dashboard') ? 'bg-indigo-600/15 text-indigo-300 border border-indigo-500/30 shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-indigo-400' : 'text-slate-400 group-hover:text-slate-300' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="7" height="9" x="3" y="3" rx="1"/>
                                <rect width="7" height="5" x="14" y="3" rx="1"/>
                                <rect width="7" height="9" x="14" y="12" rx="1"/>
                                <rect width="7" height="5" x="3" y="16" rx="1"/>
                            </svg>
                            <span>Dashboard & Metrik</span>
                        </div>
                        @if(request()->routeIs('dashboard'))
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 shadow-glow-indigo"></span>
                        @endif
                    </a>

                    <a href="{{ route('units.index') }}" 
                       class="group flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('units.*') ? 'bg-indigo-600/15 text-indigo-300 border border-indigo-500/30 shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 {{ request()->routeIs('units.*') ? 'text-indigo-400' : 'text-slate-400 group-hover:text-slate-300' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                <polyline points="9 22 9 12 15 12 15 22"/>
                            </svg>
                            <span>Unit & Denah Properti</span>
                        </div>
                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-slate-800 text-slate-300 font-mono font-bold">{{ \Illuminate\Support\Facades\Schema::hasTable('rooms') ? \App\Models\Room::count() : 0 }}</span>
                    </a>

                    <a href="{{ route('tenants.index') }}" 
                       class="group flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('tenants.*') ? 'bg-indigo-600/15 text-indigo-300 border border-indigo-500/30 shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 {{ request()->routeIs('tenants.*') ? 'text-indigo-400' : 'text-slate-400 group-hover:text-slate-300' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <span>Penyewa (CRM)</span>
                        </div>
                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-slate-800 text-slate-300 font-mono font-bold">{{ \Illuminate\Support\Facades\Schema::hasTable('customers') ? \App\Models\Customer::count() : 0 }}</span>
                    </a>
                </div>

                <!-- FINANCIAL OPERATIONS SECTION -->
                <div class="space-y-1 pt-2">
                    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Finance & Operations</p>

                    <a href="{{ route('payments.index') }}" 
                       class="group flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('payments.*') ? 'bg-indigo-600/15 text-indigo-300 border border-indigo-500/30 shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 {{ request()->routeIs('payments.*') ? 'text-indigo-400' : 'text-slate-400 group-hover:text-slate-300' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="20" height="14" x="2" y="5" rx="2"/>
                                <line x1="2" x2="22" y1="10" y2="10"/>
                            </svg>
                            <span>Pembayaran & Tagihan</span>
                        </div>
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    </a>

                    <a href="{{ route('expenses.index') }}" 
                       class="group flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150 {{ request()->routeIs('expenses.*') ? 'bg-indigo-600/15 text-indigo-300 border border-indigo-500/30 shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 {{ request()->routeIs('expenses.*') ? 'text-indigo-400' : 'text-slate-400 group-hover:text-slate-300' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                            </svg>
                            <span>Beban OPEX & CAPEX</span>
                        </div>
                    </a>

                    <a href="{{ route('home') }}" target="_blank"
                       class="group flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 transition-all">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                <polyline points="15 3 21 3 21 9"/>
                                <line x1="10" x2="21" y1="14" y2="3"/>
                            </svg>
                            <span>Lihat Katalog Publik</span>
                        </div>
                        <span class="text-[10px] text-slate-500 font-mono">↗</span>
                    </a>
                </div>
            </div>

            <!-- SIDEBAR FOOTER: USER STATUS & SYSTEM INFO -->
            <div class="p-4 border-t border-slate-800/80 bg-[#0B0F17]/40 space-y-3">
                <div class="flex items-center justify-between px-1 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span class="text-[11px] text-slate-400 font-medium">Database Synced</span>
                    </div>
                    <span class="px-1.5 py-0.5 text-[9px] font-mono text-slate-500 bg-slate-900 border border-slate-800 rounded">v2.4.0</span>
                </div>

                <div class="flex items-center justify-between p-2 rounded-xl bg-slate-900/60 border border-slate-800/80">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-indigo-500 to-indigo-700 flex items-center justify-center text-white font-bold text-xs uppercase shadow-sm flex-shrink-0">
                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name ?? 'Administrator' }}</p>
                            <span class="text-[10px] text-slate-400 truncate block">{{ auth()->user()->email ?? 'admin@kost.com' }}</span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Keluar dari sistem" class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded-lg transition-colors">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <polyline points="16 17 21 12 16 7"/>
                                <line x1="21" x2="9" y1="12" y2="12"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT WRAPPER -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <!-- MASTER HEADER -->
            <header class="h-16 bg-[#0E1422]/80 backdrop-blur-xl border-b border-slate-800/80 px-4 sm:px-6 lg:px-8 flex items-center justify-between sticky top-0 z-30">
                
                <div class="flex items-center gap-3">
                    <!-- Mobile Hamburger -->
                    <button @click="mobileMenuOpen = true" class="lg:hidden p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/80 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>

                    <!-- Breadcrumbs & Context Pill -->
                    <div class="flex items-center gap-2 text-xs">
                        <span class="text-slate-400 font-medium">Kost & Kontrakan</span>
                        <span class="text-slate-600">/</span>
                        <span class="text-slate-200 font-semibold">{{ $breadcrumb ?? 'Dashboard Management' }}</span>
                    </div>
                </div>

                <!-- Right Header Actions -->
                <div class="flex items-center gap-3">
                    
                    <!-- Search Input & Shortcut Badge -->
                    <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-900/80 border border-slate-800 text-xs text-slate-400 w-64 focus-within:border-indigo-500/50 focus-within:ring-1 focus-within:ring-indigo-500/20 transition-all">
                        <svg class="w-3.5 h-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        <input type="text" placeholder="Cari kamar, penyewa..." class="bg-transparent border-none outline-none text-slate-200 text-xs w-full placeholder:text-slate-400">
                        <kbd class="px-1.5 py-0.5 text-[10px] font-mono rounded bg-slate-800 text-slate-400 border border-slate-700/60">⌘K</kbd>
                    </div>

                    <!-- Date Range Indicator -->
                    <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-900/60 border border-slate-800 text-xs text-slate-300">
                        <svg class="w-3.5 h-3.5 text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                        <span class="font-medium">{{ date('F Y') }}</span>
                    </div>

                    <!-- Notifications Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="relative p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/80 border border-transparent hover:border-slate-700/60 transition-all">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-indigo-500 shadow-glow-indigo"></span>
                        </button>

                        <div x-show="open" 
                             x-cloak
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 mt-2 w-80 rounded-2xl bg-[#111827] border border-slate-800 shadow-2xl p-4 z-50">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                                <span class="text-xs font-bold text-white">Notifikasi Realtime</span>
                                <span class="text-[10px] text-indigo-400 font-medium cursor-pointer hover:underline">Tandai Dibaca</span>
                            </div>
                            <div class="py-2 space-y-2 text-xs">
                                <div class="p-2.5 rounded-xl bg-slate-900/80 border border-slate-800/80 flex items-start gap-2.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400 mt-1 flex-shrink-0"></span>
                                    <div>
                                        <p class="font-semibold text-slate-200">Sistem Database Siap</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Seluruh unit kontrakan dan kamar kost tersinkronisasi.</p>
                                    </div>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-900/80 border border-slate-800/80 flex items-start gap-2.5">
                                    <span class="w-2 h-2 rounded-full bg-indigo-400 mt-1 flex-shrink-0"></span>
                                    <div>
                                        <p class="font-semibold text-slate-200">Laporan Keuangan Bulanan</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Analisis Gross Yield & NOI siap ditinjau.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Role Badge -->
                    <span class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        {{ auth()->user()->role->name ?? 'Pengelola' }}
                    </span>
                </div>
            </header>

            <!-- DYNAMIC VIEW BODY -->
            <main class="flex-1 overflow-y-auto px-4 py-6 sm:px-6 lg:px-8">
                @yield('content')
            </main>
        </div>

    </div>

    @livewireScripts
</body>
</html>