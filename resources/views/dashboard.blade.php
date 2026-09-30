@extends('layouts.app')

@section('content')
@php
    $totalRooms = $rooms->count();
    $occupiedRooms = $rooms->where('status', 'OCCUPIED')->count();
    $availableRooms = $rooms->where('status', 'AVAILABLE')->count();
    $avgRent = $totalRooms > 0 ? ($rooms->avg('base_price') ?? 0) : 0;
    $topUnitTypes = $rooms->groupBy('room_type')->map(fn ($group) => $group->count())->sortDesc();
    $allFacilities = \App\Models\Facility::query()->limit(8)->get();
    $occupancyRate = $occupancyData['occupancy_rate'] ?? ($totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0);
    $grossRevenue = $pnlData['gross_revenue'] ?? 0;
    $netOperatingIncome = $pnlData['net_operating_income'] ?? 0;
    $totalOpex = $pnlData['total_opex'] ?? 0;
    $grossYield = $yieldData['gross_yield'] ?? 0;
@endphp

<div x-data="{
    filterTab: 'all',
    selectedRoom: null,
    showDrawer: false,
    openRoom(room) {
        this.selectedRoom = room;
        this.showDrawer = true;
    }
}" class="space-y-6 pb-12">

    <!-- FEEDBACK ALERT NOTIFICATIONS -->
    @if (session('success'))
        <div class="flex items-center gap-3 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm shadow-glow-emerald">
            <svg class="w-5 h-5 flex-shrink-0 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            <div class="font-medium">{{ session('success') }}</div>
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm">
            <div class="flex items-center gap-2 font-bold text-rose-200 mb-1">
                <svg class="w-4 h-4 text-rose-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                Pendaftaran Sewa Belum Berhasil
            </div>
            <ul class="list-disc list-inside text-xs space-y-0.5 text-rose-300/90 pl-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- EXECUTIVE DASHBOARD HERO HEADER -->
    <section class="relative overflow-hidden rounded-3xl border border-slate-800 bg-gradient-to-br from-[#111827] via-[#0E1526] to-[#0B0F17] p-6 lg:p-8 shadow-subtle-card">
        <!-- Ambient Decorative Glow -->
        <div class="pointer-events-none absolute -top-24 -right-24 h-96 w-96 rounded-full bg-indigo-600/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-24 -left-24 h-96 w-96 rounded-full bg-emerald-500/5 blur-3xl"></div>

        <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 rounded-full border border-indigo-500/30 bg-indigo-500/10 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-indigo-300">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Portfolio Overview & Live Analytics
                </div>
                <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-white sm:text-3xl lg:text-4xl">
                    Selamat Datang, {{ auth()->user()->name }}
                </h1>
                <p class="mt-2 text-xs sm:text-sm text-slate-400 leading-relaxed">
                    Pengelolaan terpadu <span class="text-white font-semibold">{{ $property?->name ?? 'Kost & Kontrakan Utama' }}</span>. Pantau okupansi kamar, arus kas masuk, dan performa imbal hasil properti secara realtime.
                </p>
                <div class="mt-4 flex flex-wrap items-center gap-2 text-xs">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-slate-900 border border-slate-800 text-slate-300">
                        <svg class="w-3.5 h-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        {{ $property?->address ?? 'Jl. Merdeka No. 45' }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 font-medium">
                        Investasi Awal: Rp {{ number_format($property?->initial_investment ?? 1500000000, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- Quick Action Shortcuts -->
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('units.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition-all shadow-sm">
                    <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                    Kelola Semua Unit
                </a>
                <a href="{{ route('payments.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-glow-indigo transition-all">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    Catat Pembayaran
                </a>
            </div>
        </div>
    </section>

    <!-- SMART ACTION & ATTENTION REQUIRED BAR -->
    @if ($availableRooms > 0 || $pendingChargesCount > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if ($availableRooms > 0)
                <div class="flex items-center justify-between p-4 rounded-2xl bg-emerald-950/20 border border-emerald-500/20 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 flex-shrink-0">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-white">{{ $availableRooms }} Unit Kamar Tersedia</p>
                            <p class="text-[11px] text-slate-400">Siap untuk dipasarkan dan disewakan kepada penyewa baru.</p>
                        </div>
                    </div>
                    <button @click="filterTab = 'available'" class="px-3 py-1.5 rounded-lg bg-emerald-500/20 text-emerald-300 hover:bg-emerald-500/30 text-[11px] font-semibold transition-colors">
                        Pilih Unit →
                    </button>
                </div>
            @endif

            @if ($pendingChargesCount > 0)
                <div class="flex items-center justify-between p-4 rounded-2xl bg-amber-950/20 border border-amber-500/20 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 flex-shrink-0">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-white">{{ $pendingChargesCount }} Tagihan Menunggu Pembayaran</p>
                            <p class="text-[11px] text-slate-400">Ada tagihan sewa berjalan yang perlu dikonfirmasi pembayarannya.</p>
                        </div>
                    </div>
                    <a href="{{ route('payments.index') }}" class="px-3 py-1.5 rounded-lg bg-amber-500/20 text-amber-300 hover:bg-amber-500/30 text-[11px] font-semibold transition-colors">
                        Cek Tagihan →
                    </a>
                </div>
            @else
                <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 flex-shrink-0">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-white">Status Arus Kas Terkendali</p>
                            <p class="text-[11px] text-slate-400">Seluruh pencatatan pembayaran dan pengeluaran tersinkronisasi.</p>
                        </div>
                    </div>
                    <a href="{{ route('expenses.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-800 text-slate-300 hover:bg-slate-700 text-[11px] font-semibold transition-colors">
                        Kelola Biaya →
                    </a>
                </div>
            @endif
        </div>
    @endif

    <!-- 5 EXECUTIVE KPI CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
        
        <!-- CARD 1: OCCUPANCY RATE -->
        <div class="p-5 rounded-2xl bg-[#111827] border border-slate-800 shadow-subtle-card relative overflow-hidden flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tingkat Okupansi</span>
                    <span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded-md bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Realtime</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-white tracking-tight">{{ $occupancyRate }}%</span>
                    <span class="text-xs text-slate-400 font-medium">({{ $occupiedRooms }}/{{ $totalRooms }} Unit)</span>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-slate-800/80 h-2 rounded-full overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-full rounded-full transition-all duration-700 shadow-glow-emerald" style="width: {{ $occupancyRate }}%"></div>
                </div>
                <div class="mt-2 flex items-center justify-between text-[11px] text-slate-400">
                    <span>{{ $availableRooms }} unit kosong</span>
                    <span class="text-emerald-400 font-semibold">{{ $occupiedRooms }} terisi</span>
                </div>
            </div>
        </div>

        <!-- CARD 2: GROSS REVENUE -->
        <div class="p-5 rounded-2xl bg-[#111827] border border-slate-800 shadow-subtle-card flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Gross Revenue</span>
                    <span class="text-indigo-400 text-[10px] font-bold uppercase tracking-wider">Cash Flow</span>
                </div>
                <div class="text-2xl font-extrabold text-white tracking-tight">
                    Rp {{ number_format($grossRevenue, 0, ',', '.') }}
                </div>
                <p class="mt-1 text-[11px] text-slate-400">Pendapatan sewa masuk bulan ini</p>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-800/80 flex items-center justify-between text-[11px]">
                <span class="text-slate-400">Rata-rata sewa</span>
                <span class="text-slate-200 font-bold font-mono">Rp {{ number_format((int) $avgRent, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- CARD 3: NET OPERATING INCOME -->
        <div class="p-5 rounded-2xl bg-[#111827] border border-slate-800 shadow-subtle-card flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Net Operating Income</span>
                    <span class="text-emerald-400 text-[10px] font-bold uppercase tracking-wider">Profit Bersih</span>
                </div>
                <div class="text-2xl font-extrabold text-emerald-400 tracking-tight">
                    Rp {{ number_format($netOperatingIncome, 0, ',', '.') }}
                </div>
                <p class="mt-1 text-[11px] text-slate-400">Setelah dikurangi beban OPEX</p>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-800/80 flex items-center justify-between text-[11px]">
                <span class="text-slate-400">OPEX Rutin</span>
                <span class="text-rose-400 font-bold font-mono">Rp {{ number_format($totalOpex, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- CARD 4: GROSS YIELD -->
        <div class="p-5 rounded-2xl bg-[#111827] border border-slate-800 shadow-subtle-card flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Gross Yield (ROI)</span>
                    <span class="text-amber-400 text-[10px] font-bold uppercase tracking-wider">Yield Tahunan</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-white tracking-tight">{{ $grossYield }}%</span>
                    <span class="text-xs text-slate-400">/ Tahun</span>
                </div>
                <p class="mt-1 text-[11px] text-slate-400">Imbal hasil dari modal properti</p>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-800/80 flex items-center justify-between text-[11px]">
                <span class="text-slate-400">Nilai Aset</span>
                <span class="text-slate-300 font-semibold">Rp 1.5 M</span>
            </div>
        </div>

        <!-- CARD 5: TENANTS CRM -->
        <div class="p-5 rounded-2xl bg-[#111827] border border-slate-800 shadow-subtle-card flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Customer & Penyewa</span>
                    <a href="{{ route('tenants.index') }}" class="text-indigo-400 hover:text-indigo-300 text-[10px] font-bold uppercase tracking-wider">Lihat CRM →</a>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-white tracking-tight">{{ $occupiedRooms }}</span>
                    <span class="text-xs text-slate-400">Penyewa Aktif</span>
                </div>
                <p class="mt-1 text-[11px] text-slate-400">{{ $totalRooms }} Total kapasitas unit</p>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-800/80 flex items-center justify-between text-[11px]">
                <span class="text-slate-400">Kontrak Sewa</span>
                <span class="text-indigo-400 font-semibold">{{ $occupiedRooms }} Aktif</span>
            </div>
        </div>

    </div>

    <!-- RENT COLLECTION ADVANCED ANALYTICS CHART (EXACT MATCH TO INSPIRATION) -->
    <section class="rounded-3xl bg-[#0E1526] border border-slate-800/90 p-6 sm:p-8 shadow-subtle-card relative overflow-hidden">
        <!-- Ambient Background Glows -->
        <div class="pointer-events-none absolute -top-20 -left-20 h-64 w-64 rounded-full bg-purple-600/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-20 -right-20 h-64 w-64 rounded-full bg-cyan-500/10 blur-3xl"></div>

        <!-- Section Header -->
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-800/80">
            <div>
                <h2 class="text-xl font-bold tracking-tight text-white flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-gradient-to-r from-purple-500 to-cyan-400"></span>
                    Rent Collection
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Analisis ketercapaian penerimaan sewa & tren pemungutan kas bulanan</p>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 text-xs font-semibold text-slate-300">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Live Collection Tracker
                </span>
                <div class="inline-flex p-1 rounded-xl bg-slate-900 border border-slate-800 text-[11px] font-semibold text-slate-400">
                    <span class="px-2.5 py-1 rounded-lg bg-indigo-600 text-white shadow-sm">30 Hari</span>
                    <span class="px-2.5 py-1 text-slate-400">YTD</span>
                </div>
            </div>
        </div>

        <div class="relative z-10 mt-6 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            
            <!-- LEFT COLUMN: SEGMENTED DONUT RING + KEY METRICS -->
            <div class="lg:col-span-4 flex flex-col items-center justify-center p-4 lg:border-r border-slate-800/80">
                
                <!-- Donut Chart SVG Container -->
                <div class="relative w-56 h-56 flex items-center justify-center">
                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 220 220">
                        <defs>
                            <!-- Segment 1: Cyan / Blue Gradient -->
                            <linearGradient id="segCyan" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#06B6D4" />
                                <stop offset="100%" stop-color="#3B82F6" />
                            </linearGradient>
                            <!-- Segment 2: Pink / Magenta Gradient -->
                            <linearGradient id="segPink" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#EC4899" />
                                <stop offset="100%" stop-color="#D946EF" />
                            </linearGradient>
                            <!-- Segment 3: Electric Violet Gradient -->
                            <linearGradient id="segViolet" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#A855F7" />
                                <stop offset="100%" stop-color="#8B5CF6" />
                            </linearGradient>
                            <!-- Segment 4: Deep Purple Gradient -->
                            <linearGradient id="segPurple" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#7E22CE" />
                                <stop offset="100%" stop-color="#6366F1" />
                            </linearGradient>
                            <!-- Segment 5: Indigo Slate Gradient -->
                            <linearGradient id="segIndigo" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#6366F1" />
                                <stop offset="100%" stop-color="#4F46E5" />
                            </linearGradient>
                            <!-- Segment 6: Light Sky Gradient -->
                            <linearGradient id="segSky" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#38BDF8" />
                                <stop offset="100%" stop-color="#0284C7" />
                            </linearGradient>

                            <filter id="glowDonut" x="-20%" y="-20%" width="140%" height="140%">
                                <feDropShadow dx="0" dy="0" stdDeviation="4" flood-color="#8B5CF6" flood-opacity="0.35" />
                            </filter>
                        </defs>

                        <!-- Base background ring track -->
                        <circle cx="110" cy="110" r="74" fill="none" stroke="#1E293B" stroke-width="26" opacity="0.4" />

                        <!-- Segment 1 (Top-Right): Length 68 -->
                        <circle cx="110" cy="110" r="74" fill="none" stroke="url(#segCyan)" stroke-width="26"
                                stroke-dasharray="68 397" stroke-dashoffset="0" stroke-linecap="round" />

                        <!-- Segment 2 (Right): Length 72 -->
                        <circle cx="110" cy="110" r="74" fill="none" stroke="url(#segPink)" stroke-width="26"
                                stroke-dasharray="72 393" stroke-dashoffset="-76" stroke-linecap="round" />

                        <!-- Segment 3 (Bottom-Right): Length 64 -->
                        <circle cx="110" cy="110" r="74" fill="none" stroke="url(#segViolet)" stroke-width="26"
                                stroke-dasharray="64 401" stroke-dashoffset="-156" stroke-linecap="round" />

                        <!-- Segment 4 (Bottom-Left): Length 60 -->
                        <circle cx="110" cy="110" r="74" fill="none" stroke="url(#segPurple)" stroke-width="26"
                                stroke-dasharray="60 405" stroke-dashoffset="-228" stroke-linecap="round" />

                        <!-- Segment 5 (Left): Length 68 -->
                        <circle cx="110" cy="110" r="74" fill="none" stroke="url(#segIndigo)" stroke-width="26"
                                stroke-dasharray="68 397" stroke-dashoffset="-296" stroke-linecap="round" />

                        <!-- Segment 6 (Top-Left): Length 80 -->
                        <circle cx="110" cy="110" r="74" fill="none" stroke="url(#segSky)" stroke-width="26"
                                stroke-dasharray="80 385" stroke-dashoffset="-372" stroke-linecap="round" />
                    </svg>

                    <!-- Center Label: Dynamic Score -->
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center select-none pointer-events-none">
                        <span class="text-3xl font-extrabold text-white font-mono tracking-tight drop-shadow-md">{{ $collectionChart['donut_score'] ?? '+321' }}</span>
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-widest mt-0.5">SCORE</span>
                    </div>

                    <!-- Subtle constellation dots next to donut (as in screenshot) -->
                    <div class="absolute right-0 bottom-6 flex items-center gap-1 opacity-70">
                        <span class="w-1.5 h-1.5 rounded-full bg-purple-400 animate-ping"></span>
                        <span class="w-1 h-1 rounded-full bg-cyan-400"></span>
                    </div>
                </div>

                <!-- Two Metric Pillars Below Donut -->
                <div class="mt-6 w-full grid grid-cols-2 gap-4 text-center">
                    <div class="p-3 rounded-2xl bg-slate-900/60 border border-slate-800/80">
                        <span class="text-[11px] font-semibold text-slate-400 block truncate">Koleksi Tepat Waktu</span>
                        <span class="text-2xl font-extrabold bg-gradient-to-r from-fuchsia-400 to-pink-500 bg-clip-text text-transparent font-mono tracking-tight block mt-1">
                            +{{ $collectionChart['on_time_rate'] ?? 79 }}%
                        </span>
                        <span class="text-[10px] text-slate-400">Periode berjalan</span>
                    </div>

                    <div class="p-3 rounded-2xl bg-slate-900/60 border border-slate-800/80">
                        <span class="text-[11px] font-semibold text-slate-400 block truncate">Rasio Penagihan</span>
                        <span class="text-2xl font-extrabold bg-gradient-to-r from-emerald-400 to-cyan-400 bg-clip-text text-transparent font-mono tracking-tight block mt-1">
                            • {{ $collectionChart['collection_rate'] ?? 85 }}%
                        </span>
                        <span class="text-[10px] text-slate-400">Target tercapai</span>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: BAR + DUAL SPLINE CURVED LINE CHART -->
            <div class="lg:col-span-8 flex flex-col justify-between space-y-4">
                
                <!-- Chart Top Subtitle -->
                <div class="flex items-center justify-between text-xs px-2">
                    <span class="font-bold text-slate-300 font-mono flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                        Rp {{ number_format($collectionChart['total_collected'] ?? 0, 0, ',', '.') }} Realisasi Kas ({{ $occupiedRooms }} Unit Aktif)
                    </span>
                    <div class="flex items-center gap-4 text-[11px] text-slate-400">
                        <span class="flex items-center gap-1.5">
                            <span class="w-3 h-1 rounded-full bg-cyan-400"></span>
                            Realisasi Kas
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-3 h-1 rounded-full bg-fuchsia-400"></span>
                            Target Penagihan
                        </span>
                    </div>
                </div>

                <!-- Responsive Combined SVG Chart -->
                <div class="w-full overflow-x-auto">
                    <svg class="w-full h-64 min-w-[500px]" viewBox="0 0 580 240" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <!-- Bar Gradients -->
                            <linearGradient id="barGrad1" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#4F46E5" />
                                <stop offset="100%" stop-color="#1E1B4B" />
                            </linearGradient>
                            <linearGradient id="barGrad2" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#7C3AED" />
                                <stop offset="100%" stop-color="#311042" />
                            </linearGradient>
                            <linearGradient id="barGrad3" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#9333EA" />
                                <stop offset="100%" stop-color="#3B0764" />
                            </linearGradient>
                            <linearGradient id="barGrad4" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#C026D3" />
                                <stop offset="100%" stop-color="#4A044E" />
                            </linearGradient>
                            <linearGradient id="barGrad5" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#8B5CF6" />
                                <stop offset="100%" stop-color="#2E1065" />
                            </linearGradient>
                            <linearGradient id="barGrad6" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#6366F1" />
                                <stop offset="100%" stop-color="#1E1B4B" />
                            </linearGradient>
                            <linearGradient id="barGrad7" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#38BDF8" />
                                <stop offset="100%" stop-color="#1D4ED8" />
                            </linearGradient>

                            <!-- Glow Filter for Lines -->
                            <filter id="cyanGlow" x="-20%" y="-20%" width="140%" height="140%">
                                <feDropShadow dx="0" dy="0" stdDeviation="3" flood-color="#38BDF8" flood-opacity="0.6" />
                            </filter>
                            <filter id="fuchsiaGlow" x="-20%" y="-20%" width="140%" height="140%">
                                <feDropShadow dx="0" dy="0" stdDeviation="3" flood-color="#E879F9" flood-opacity="0.6" />
                            </filter>
                        </defs>

                        <!-- Y-AXIS HORIZONTAL GRID LINES -->
                        <g opacity="0.15">
                            <line x1="45" y1="35" x2="560" y2="35" stroke="#FFFFFF" stroke-dasharray="4 4" />
                            <line x1="45" y1="75" x2="560" y2="75" stroke="#FFFFFF" stroke-dasharray="4 4" />
                            <line x1="45" y1="115" x2="560" y2="115" stroke="#FFFFFF" stroke-dasharray="4 4" />
                            <line x1="45" y1="155" x2="560" y2="155" stroke="#FFFFFF" stroke-dasharray="4 4" />
                            <line x1="45" y1="195" x2="560" y2="195" stroke="#FFFFFF" />
                        </g>

                        <!-- Rotated Y-Axis Title (Matches 'Mhtntremets' in screenshot) -->
                        <text x="-120" y="16" transform="rotate(-90)" font-family="system-ui, sans-serif" font-size="8" fill="#64748B" font-weight="700" letter-spacing="1">NOMINAL (RP)</text>

                        <!-- Y-AXIS LABELS -->
                        <g font-family="monospace" font-size="10" fill="#64748B" font-weight="600" text-anchor="end">
                            <text x="35" y="38">200</text>
                            <text x="35" y="78">100</text>
                            <text x="35" y="118">50</text>
                            <text x="35" y="158">20</text>
                            <text x="35" y="198">0</text>
                        </g>

                        <!-- VERTICAL BARS (7 REALTIME DYNAMIC INTERVALS) -->
                        @foreach ($collectionChart['intervals'] ?? [] as $inv)
                            <rect x="{{ $inv['x'] }}" y="{{ $inv['bar_y'] }}" width="{{ $inv['width'] }}" height="{{ $inv['bar_height'] }}" rx="5" fill="{{ $inv['gradient'] }}" class="transition-all duration-300 hover:opacity-80 cursor-pointer">
                                <title>{{ $inv['label'] }} (Tgl {{ $inv['code'] }}): Realisasi Rp {{ number_format($inv['actual'], 0, ',', '.') }} • Target Rp {{ number_format($inv['target'], 0, ',', '.') }}</title>
                            </rect>
                            @if ($inv['bar_pct'] > 0)
                                <text x="{{ $inv['cx'] }}" y="{{ max(25, $inv['bar_y'] - 6) }}" font-family="monospace" font-size="9" fill="{{ $loop->last ? '#38BDF8' : '#C084FC' }}" text-anchor="middle" font-weight="{{ $loop->last ? '700' : '600' }}">
                                    {{ $inv['bar_pct'] }}%
                                </text>
                            @endif
                        @endforeach

                        <!-- SPLINE LINE 1: CYAN / SKY BLUE (REALISASI KAS) -->
                        <path d="{{ $collectionChart['line_actual_path'] ?? 'M 45,188 C 85,186 125,180 162,170 C 200,160 220,115 232,108 C 265,92 340,90 392,68 C 435,50 485,32 522,14' }}"
                              fill="none" stroke="#38BDF8" stroke-width="2.8" stroke-linecap="round" filter="url(#cyanGlow)" />

                        <!-- SPLINE LINE 2: MAGENTA / FUCHSIA (TARGET PENAGIHAN) -->
                        <path d="{{ $collectionChart['line_target_path'] ?? 'M 45,185 C 95,183 135,176 162,172 C 195,168 225,142 270,136 C 310,130 350,130 402,96 C 445,68 490,40 522,18' }}"
                              fill="none" stroke="#D946EF" stroke-width="2.8" stroke-linecap="round" filter="url(#fuchsiaGlow)" />

                        <!-- WHITE GLOWING DATA POINT MARKERS (LINE 1: REALISASI KAS) -->
                        @foreach ($collectionChart['actual_points'] ?? [] as $pt)
                            <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="{{ $loop->last ? '4.5' : '3.5' }}" fill="#FFFFFF" stroke="#38BDF8" stroke-width="{{ $loop->last ? '2.5' : '2' }}" class="transition-all hover:r-5 cursor-pointer">
                                <title>{{ $pt['label'] ?? 'Awal' }}: Realisasi Rp {{ number_format($pt['actual'] ?? 0, 0, ',', '.') }}</title>
                            </circle>
                        @endforeach

                        <!-- WHITE GLOWING DATA POINT MARKERS (LINE 2: TARGET PENAGIHAN) -->
                        @foreach ($collectionChart['target_points'] ?? [] as $pt)
                            <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="{{ $loop->last ? '4' : '3' }}" fill="#FFFFFF" stroke="#D946EF" stroke-width="2" class="transition-all hover:r-5 cursor-pointer">
                                <title>{{ $pt['label'] ?? 'Awal' }}: Target Rp {{ number_format($pt['target'] ?? 0, 0, ',', '.') }}</title>
                            </circle>
                        @endforeach

                        <!-- X-AXIS LABELS -->
                        <g font-family="monospace" font-size="11" fill="#94A3B8" font-weight="600" text-anchor="middle">
                            @foreach ($collectionChart['intervals'] ?? [] as $inv)
                                <text x="{{ $inv['cx'] }}" y="215">{{ $inv['code'] }}</text>
                            @endforeach
                        </g>
                    </svg>
                </div>

            </div>

        </div>
    </section>

    <!-- MAIN GRID: FINANCIAL & OCCUPANCY ANALYTICS + BREAKDOWNS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- FINANCIAL PERFORMANCE OVERVIEW (2 COLS) -->
        <section class="lg:col-span-2 rounded-3xl bg-[#111827] border border-slate-800 p-6 shadow-subtle-card">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-800/80">
                <div>
                    <h2 class="text-base font-bold text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        Ringkasan Finansial & Arus Kas Operasional
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Analisis pendapatan bruto sewa, pengeluaran rutin, dan margin laba bersih</p>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-800 text-slate-300 font-mono">{{ date('F Y') }}</span>
                </div>
            </div>

            <!-- Visual Bar Comparison -->
            <div class="mt-6 space-y-5">
                <div>
                    <div class="flex items-center justify-between text-xs mb-2">
                        <span class="font-medium text-slate-300 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                            Gross Revenue (Kas Masuk Sewa)
                        </span>
                        <span class="font-bold text-white font-mono">Rp {{ number_format($grossRevenue, 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full bg-slate-900 h-3 rounded-full overflow-hidden p-0.5 border border-slate-800">
                        <div class="bg-indigo-500 h-full rounded-full transition-all duration-700" style="width: {{ $grossRevenue > 0 ? 100 : 5 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between text-xs mb-2">
                        <span class="font-medium text-slate-300 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                            Beban Operasional Rutin (OPEX)
                        </span>
                        <span class="font-bold text-rose-400 font-mono">Rp {{ number_format($totalOpex, 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full bg-slate-900 h-3 rounded-full overflow-hidden p-0.5 border border-slate-800">
                        <div class="bg-rose-500 h-full rounded-full transition-all duration-700" style="width: {{ $grossRevenue > 0 ? min(100, round(($totalOpex / max(1, $grossRevenue)) * 100)) : 10 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between text-xs mb-2">
                        <span class="font-medium text-slate-300 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            Net Operating Income (NOI / Laba Operasional)
                        </span>
                        <span class="font-bold text-emerald-400 font-mono">Rp {{ number_format($netOperatingIncome, 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full bg-slate-900 h-3 rounded-full overflow-hidden p-0.5 border border-slate-800">
                        <div class="bg-emerald-500 h-full rounded-full transition-all duration-700" style="width: {{ $grossRevenue > 0 ? max(5, min(100, round(($netOperatingIncome / max(1, $grossRevenue)) * 100))) : 5 }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Financial Key Figures Footnote -->
            <div class="mt-6 pt-5 border-t border-slate-800/80 grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800">
                    <span class="text-[10px] uppercase font-bold text-slate-400">Total OPEX</span>
                    <p class="text-sm font-bold text-slate-200 mt-1 font-mono">Rp {{ number_format($totalOpex, 0, ',', '.') }}</p>
                </div>
                <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800">
                    <span class="text-[10px] uppercase font-bold text-slate-400">Total CAPEX</span>
                    <p class="text-sm font-bold text-slate-200 mt-1 font-mono">Rp {{ number_format($pnlData['total_capex'] ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800">
                    <span class="text-[10px] uppercase font-bold text-slate-400">Net Cash Flow</span>
                    <p class="text-sm font-bold text-emerald-400 mt-1 font-mono">Rp {{ number_format($pnlData['net_cash_flow'] ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800">
                    <span class="text-[10px] uppercase font-bold text-slate-400">Estimasi Yield</span>
                    <p class="text-sm font-bold text-amber-400 mt-1 font-mono">{{ $grossYield }}% / thn</p>
                </div>
            </div>
        </section>

        <!-- UNIT COMPOSITION & PORTFOLIO METRICS (1 COL) -->
        <section class="rounded-3xl bg-[#111827] border border-slate-800 p-6 shadow-subtle-card flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2 pb-4 border-b border-slate-800/80">
                    <svg class="w-4 h-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" x2="12" y1="22.08" y2="12"/></svg>
                    Komposisi & Fasilitas Properti
                </h3>

                <!-- Unit Types Distribution -->
                <div class="mt-5 space-y-4">
                    @forelse ($topUnitTypes as $type => $count)
                        @php
                            $percentage = $totalRooms > 0 ? round(($count / $totalRooms) * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span class="font-medium text-slate-300">{{ $type }}</span>
                                <span class="font-mono text-slate-400">{{ $count }} unit <span class="text-slate-400">({{ $percentage }}%)</span></span>
                            </div>
                            <div class="h-2 w-full bg-slate-900 rounded-full overflow-hidden border border-slate-800">
                                <div class="h-full rounded-full bg-indigo-500" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="py-4 text-center text-xs text-slate-400">Data unit belum tersedia.</div>
                    @endforelse
                </div>
            </div>

            <!-- Facilities Tags -->
            <div class="mt-6 pt-5 border-t border-slate-800/80">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Fasilitas Unggulan</span>
                <div class="mt-3 flex flex-wrap gap-1.5">
                    @foreach ($allFacilities as $facility)
                        <span class="px-2.5 py-1 text-[11px] rounded-lg bg-slate-900 border border-slate-800 text-slate-300">
                            {{ $facility->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        </section>

    </div>

    <!-- INTERACTIVE ROOM / UNIT MANAGEMENT GRID -->
    <section class="rounded-3xl bg-[#111827] border border-slate-800 p-6 shadow-subtle-card">
        
        <!-- Header & Filter Tabs -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-800/80">
            <div>
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                    Denah Status Unit & Pendaftaran Sewa
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Pilih unit untuk melihat spesifikasi, mengaktifkan kontrak penyewa baru, atau mencatat pembayaran</p>
            </div>

            <!-- Status Filter Tabs -->
            <div class="inline-flex p-1 rounded-xl bg-slate-900 border border-slate-800 text-xs font-semibold">
                <button @click="filterTab = 'all'" 
                        :class="filterTab === 'all' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white'"
                        class="px-3 py-1.5 rounded-lg transition-all">
                    Semua ({{ $totalRooms }})
                </button>
                <button @click="filterTab = 'available'" 
                        :class="filterTab === 'available' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-400 hover:text-white'"
                        class="px-3 py-1.5 rounded-lg transition-all">
                    Tersedia ({{ $availableRooms }})
                </button>
                <button @click="filterTab = 'occupied'" 
                        :class="filterTab === 'occupied' ? 'bg-rose-600 text-white shadow-sm' : 'text-slate-400 hover:text-white'"
                        class="px-3 py-1.5 rounded-lg transition-all">
                    Terisi ({{ $occupiedRooms }})
                </button>
            </div>
        </div>

        <!-- Room Cards Grid -->
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @forelse ($rooms as $room)
                @php
                    $isOccupied = $room->status === 'OCCUPIED';
                    $statusBadge = $isOccupied 
                        ? 'bg-rose-500/10 text-rose-300 border-rose-500/20' 
                        : 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20';
                    $borderCard = $isOccupied 
                        ? 'border-slate-800 hover:border-rose-500/40 bg-slate-900/60' 
                        : 'border-slate-800 hover:border-emerald-500/40 bg-slate-900/60';
                @endphp

                <div x-show="filterTab === 'all' || (filterTab === 'available' && '{{ $room->status }}' === 'AVAILABLE') || (filterTab === 'occupied' && '{{ $room->status }}' === 'OCCUPIED')"
                     @click="openRoom({{ json_encode($room) }})"
                     class="group cursor-pointer rounded-2xl border {{ $borderCard }} p-5 transition-all duration-200 hover:-translate-y-1 hover:shadow-subtle-card flex flex-col justify-between">
                    
                    <div>
                        <!-- Header Card -->
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-base font-extrabold text-white group-hover:text-indigo-300 transition-colors font-mono">
                                {{ $room->room_number }}
                            </span>
                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full border {{ $statusBadge }}">
                                {{ $room->status }}
                            </span>
                        </div>

                        <!-- Details -->
                        <p class="text-xs font-semibold text-slate-300">{{ $room->room_type }}</p>
                        <p class="text-[11px] text-slate-400 mt-1 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 3H3v18h18V3z"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                            Ukuran: {{ $room->size ?? 'Standar' }}
                        </p>
                    </div>

                    <!-- Pricing & Action Footer -->
                    <div class="mt-5 pt-4 border-t border-slate-800/80 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase font-semibold block">Tarif Sewa</span>
                            <span class="text-sm font-extrabold text-white font-mono">
                                Rp {{ number_format($room->base_price, 0, ',', '.') }}<span class="text-[10px] text-slate-400 font-normal">/bln</span>
                            </span>
                        </div>
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-indigo-400 group-hover:text-indigo-300 transition-colors">
                            @if($isOccupied)
                                Detail →
                            @else
                                Sewa →
                            @endif
                        </span>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-slate-400">
                    <p class="text-sm">Belum ada unit yang terdaftar.</p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- RECENT TRANSACTIONS & ACTIVE LEASES FEED -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- RECENT PAYMENTS -->
        <section class="rounded-3xl bg-[#111827] border border-slate-800 p-6 shadow-subtle-card">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800/80">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                    <h3 class="text-sm font-bold text-white">Riwayat Pembayaran Terbaru</h3>
                </div>
                <a href="{{ route('payments.index') }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300">Lihat Semua →</a>
            </div>

            <div class="mt-4 divide-y divide-slate-800/60">
                @forelse ($recentPayments as $payment)
                    <div class="py-3 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 font-bold font-mono text-xs flex-shrink-0">
                                Rp
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-white truncate">{{ $payment->customer->name ?? 'Penyewa' }}</p>
                                <p class="text-[11px] text-slate-400 truncate">Kamar {{ $payment->lease->room->room_number ?? '-' }} • {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-emerald-400 font-mono">+Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                            <span class="block text-[10px] text-slate-400">{{ $payment->method ?? 'Transfer' }}</span>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-400">
                        Belum ada riwayat pembayaran sewa terbaru.
                    </div>
                @endforelse
            </div>
        </section>

        <!-- RECENT ACTIVE LEASES -->
        <section class="rounded-3xl bg-[#111827] border border-slate-800 p-6 shadow-subtle-card">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800/80">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    <h3 class="text-sm font-bold text-white">Kontrak Sewa Aktif</h3>
                </div>
                <a href="{{ route('tenants.index') }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300">Lihat Semua →</a>
            </div>

            <div class="mt-4 divide-y divide-slate-800/60">
                @forelse ($recentLeases as $lease)
                    <div class="py-3 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 font-bold font-mono text-xs flex-shrink-0">
                                {{ $lease->room->room_number ?? 'U' }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-white truncate">{{ $lease->customer->name ?? '-' }}</p>
                                <p class="text-[11px] text-slate-400 truncate">{{ $lease->customer->phone ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-white font-mono">Rp {{ number_format($lease->rental_rate, 0, ',', '.') }}/bln</span>
                            <span class="block text-[10px] text-slate-400">Mulai: {{ \Carbon\Carbon::parse($lease->start_date)->format('d M Y') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-slate-400">
                        Belum ada kontrak sewa aktif.
                    </div>
                @endforelse
            </div>
        </section>

    </div>

    <!-- INTERACTIVE SLIDE-OVER DRAWER FOR ROOM ONBOARDING / DETAIL -->
    <div x-show="showDrawer"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex justify-end overflow-hidden bg-black/75 backdrop-blur-sm">

        <div @click.away="showDrawer = false"
             x-show="showDrawer"
             x-transition:enter="transform transition ease-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="h-full w-full max-w-lg overflow-y-auto border-l border-slate-800 bg-[#0E1526] p-6 lg:p-8 shadow-2xl flex flex-col justify-between">
            
            <div>
                <!-- Drawer Header -->
                <div class="flex items-center justify-between pb-5 border-b border-slate-800/80">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-lg font-extrabold text-white font-mono">Unit <span x-text="selectedRoom?.room_number" class="text-indigo-400"></span></span>
                            <span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded-md border"
                                  :class="selectedRoom?.status === 'OCCUPIED' ? 'bg-rose-500/10 text-rose-300 border-rose-500/20' : 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20'"
                                  x-text="selectedRoom?.status">
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1" x-text="selectedRoom?.room_type"></p>
                    </div>
                    <button @click="showDrawer = false" class="p-2 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800/80 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- AVAILABLE: FORM ONBOARDING SEWA -->
                <template x-if="selectedRoom?.status === 'AVAILABLE'">
                    <div class="mt-6 space-y-6">
                        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-xs text-emerald-300 flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-emerald-400 mt-0.5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                            <div>
                                <p class="font-bold">Unit Kamar Siap Disewa</p>
                                <p class="text-[11px] text-emerald-300/80 mt-0.5">Isi identitas penyewa untuk mengaktifkan kontrak sewa dan menerbitkan tagihan pertama.</p>
                            </div>
                        </div>

                        <form action="{{ route('leases.store') }}" method="POST" class="space-y-4" x-data="{
                            formatRupiah(value) {
                                if (!value) return '0';
                                let numberString = value.toString().replace(/[^,\d]/g, '');
                                let split = numberString.split(',');
                                let sisa = split[0].length % 3;
                                let rupiah = split[0].substr(0, sisa);
                                let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
                                if (ribuan) {
                                    let separator = sisa ? '.' : '';
                                    rupiah += separator + ribuan.join('.');
                                }
                                return split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
                            },
                            initValues() {
                                let price = this.selectedRoom?.base_price || 0;
                                let cleanPrice = Math.floor(Number(price)).toString();
                                this.rentRaw = cleanPrice;
                                this.rentFormatted = this.formatRupiah(cleanPrice);
                                this.depositFormatted = '0';
                                this.depositRaw = 0;
                            },
                            rentFormatted: '',
                            rentRaw: 0,
                            depositFormatted: '0',
                            depositRaw: 0
                        }" x-init="$watch('selectedRoom', () => initValues())">
                            @csrf
                            <input type="hidden" name="room_id" :value="selectedRoom?.id">

                            <div>
                                <label class="mb-1.5 block text-xs font-semibold text-slate-300">Nama Lengkap Penyewa *</label>
                                <input type="text" name="tenant_name" required placeholder="Contoh: Ahmad Rizki" class="w-full rounded-xl border border-slate-800 bg-[#0B0F17] px-3.5 py-2.5 text-xs text-white placeholder:text-slate-400 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/20 outline-none transition-all">
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-semibold text-slate-300">Nomor WhatsApp / HP *</label>
                                <input type="text" name="phone_number" required placeholder="081234567890 (Wajib diawali 08, 10-15 digit)" class="w-full rounded-xl border border-slate-800 bg-[#0B0F17] px-3.5 py-2.5 text-xs text-white placeholder:text-slate-400 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/20 outline-none transition-all">
                                <span class="text-[10px] text-slate-400 mt-1 block">Format: diawali '08' murni angka tanpa spasi/simbol.</span>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-semibold text-slate-300">Tanggal Mulai Sewa *</label>
                                <input type="date" name="start_date" required value="{{ date('Y-m-d') }}" class="w-full rounded-xl border border-slate-800 bg-[#0B0F17] px-3.5 py-2.5 text-xs text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/20 outline-none transition-all">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="mb-1.5 block text-xs font-semibold text-slate-300">Tarif Sewa (Rp / bln) *</label>
                                    <input type="text" x-model="rentFormatted" @input="rentRaw = $event.target.value.replace(/\./g, ''); rentFormatted = formatRupiah($event.target.value);" required class="w-full rounded-xl border border-slate-800 bg-[#0B0F17] px-3.5 py-2.5 text-xs text-white font-mono focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/20 outline-none transition-all">
                                    <input type="hidden" name="rent_amount" x-model="rentRaw">
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-xs font-semibold text-slate-300">Deposit Jaminan (Rp)</label>
                                    <input type="text" x-model="depositFormatted" @input="depositRaw = $event.target.value.replace(/\./g, ''); depositFormatted = formatRupiah($event.target.value);" class="w-full rounded-xl border border-slate-800 bg-[#0B0F17] px-3.5 py-2.5 text-xs text-white font-mono focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/20 outline-none transition-all">
                                    <input type="hidden" name="deposit" x-model="depositRaw">
                                </div>
                            </div>

                            <div class="pt-3">
                                <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 px-4 py-3 text-xs font-bold text-white shadow-glow-emerald transition-all">
                                    ✓ Simpan & Aktifkan Kontrak Sewa
                                </button>
                            </div>
                        </form>
                    </div>
                </template>

                <!-- OCCUPIED: STATUS & ACTIONS -->
                <template x-if="selectedRoom?.status === 'OCCUPIED'">
                    <div class="mt-6 space-y-5">
                        <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-xs text-rose-300 flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-rose-400 mt-0.5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" x2="9" y1="9" y2="15"/><line x1="9" x2="15" y1="9" y2="15"/></svg>
                            <div>
                                <p class="font-bold">Kamar Sedang Terisi</p>
                                <p class="text-[11px] text-rose-300/80 mt-0.5">Unit ini memiliki kontrak sewa aktif dan penyewa terdaftar.</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 text-xs">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                                <span class="text-slate-400">Tarif Standar</span>
                                <span class="font-bold text-white font-mono">Rp <span x-text="Number(selectedRoom?.base_price).toLocaleString('id-ID')"></span>/bulan</span>
                            </div>
                            <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                                <span class="text-slate-400">Ukuran Ruang</span>
                                <span class="font-semibold text-slate-200" x-text="selectedRoom?.size ?? 'Standar'"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Tipe Properti</span>
                                <span class="font-semibold text-slate-200" x-text="selectedRoom?.room_type"></span>
                            </div>
                        </div>

                        <div class="space-y-2 pt-2">
                            <a href="{{ route('payments.index') }}" class="flex items-center justify-center gap-2 w-full rounded-xl bg-indigo-600 hover:bg-indigo-500 px-4 py-3 text-center text-xs font-bold text-white shadow-glow-indigo transition-all">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                                Catat Pembayaran Sewa
                            </a>
                            <a href="{{ route('tenants.index') }}" class="flex items-center justify-center gap-2 w-full rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 px-4 py-3 text-center text-xs font-semibold text-slate-200 transition-all">
                                <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                Buka Profil Penyewa di CRM
                            </a>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Drawer Footer Note -->
            <div class="pt-6 border-t border-slate-800/80 text-[11px] text-slate-400 text-center">
                KostOS Core Property Management Engine
            </div>
        </div>
    </div>

</div>
@endsection
