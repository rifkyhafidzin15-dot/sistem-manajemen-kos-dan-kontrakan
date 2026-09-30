<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Temukan kamar kost dan hunian impian dengan fasilitas lengkap, proses sewa transparan, dan manajemen modern.">
    <title>vHomes | Let’s Guide You To Your Dream Home</title>
    
    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            purple: '#5B4AE6',
                            purpleHover: '#4A37D6',
                            purpleLight: '#F3F0FF',
                            dark: '#0F172A',
                            lavender: '#F8F7FD',
                        }
                    },
                    boxShadow: {
                        'soft-float': '0 20px 40px -15px rgba(91, 74, 230, 0.12), 0 0 25px 0 rgba(0, 0, 0, 0.04)',
                        'card-hover': '0 25px 50px -12px rgba(91, 74, 230, 0.15)',
                        'glow-purple': '0 10px 25px -5px rgba(91, 74, 230, 0.4)',
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-full bg-white text-slate-800 font-sans selection:bg-[#5B4AE6]/20 selection:text-[#5B4AE6]"
      x-data="{
          searchQuery: '',
          selectedType: 'all',
          selectedBudget: 'all',
          activeTab: 'rent',
          selectedRoomModal: null,
          mobileMenuOpen: false
      }">

    <!-- HERO BACKGROUND WRAPPER (Soft Lilac / Lavender Gradient exactly as in reference image) -->
    <div class="relative bg-gradient-to-b from-[#F3F0FF] via-[#F8F7FD] to-white pb-20 lg:pb-28">
        
        <!-- TOP NAVIGATION BAR -->
        <header class="relative z-30 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                
                <!-- Brand Logo (vHomes) -->
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                    <div class="flex items-end gap-1 h-7">
                        <span class="w-1.5 h-4 rounded-full bg-[#5B4AE6] group-hover:scale-110 transition-transform"></span>
                        <span class="w-1.5 h-7 rounded-full bg-[#5B4AE6]"></span>
                        <span class="w-1.5 h-5 rounded-full bg-[#7C3AED]"></span>
                    </div>
                    <span class="text-2xl font-extrabold tracking-tight text-[#0F172A] font-sans">
                        v<span class="text-[#5B4AE6]">Homes</span>
                    </span>
                </a>

                <!-- Center Navigation Links -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                    <a href="{{ route('home') }}" class="relative text-[#5B4AE6] font-bold py-1">
                        Home
                        <span class="absolute bottom-0 left-0 w-full h-0.5 bg-[#5B4AE6] rounded-full"></span>
                    </a>
                    <a href="#about" class="hover:text-[#5B4AE6] transition-colors py-1">About</a>
                    <a href="#properties" class="hover:text-[#5B4AE6] transition-colors py-1">Properties</a>
                    <a href="#contact" class="hover:text-[#5B4AE6] transition-colors py-1">ContactUs</a>
                </nav>

                <!-- Right Action Buttons -->
                <div class="flex items-center gap-4">
                    <a href="{{ route('login') }}" class="text-sm font-bold text-slate-700 hover:text-[#5B4AE6] transition-colors px-2 py-1">
                        Login
                    </a>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl bg-[#5B4AE6] hover:bg-[#4A37D6] text-white text-sm font-bold shadow-glow-purple hover:shadow-lg transition-all duration-200">
                        Register
                    </a>

                    <!-- Mobile Menu Hamburger Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Navigation Drawer -->
            <div x-show="mobileMenuOpen" x-cloak class="md:hidden mt-4 p-4 rounded-2xl bg-white shadow-xl border border-slate-100 flex flex-col gap-3 text-sm font-semibold text-slate-700">
                <a @click="mobileMenuOpen = false" href="{{ route('home') }}" class="text-[#5B4AE6] py-1">Home</a>
                <a @click="mobileMenuOpen = false" href="#about" class="hover:text-[#5B4AE6] py-1">About</a>
                <a @click="mobileMenuOpen = false" href="#properties" class="hover:text-[#5B4AE6] py-1">Properties</a>
                <a @click="mobileMenuOpen = false" href="#contact" class="hover:text-[#5B4AE6] py-1">ContactUs</a>
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ route('login') }}" class="text-slate-600 font-bold">Login Pengelola</a>
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl bg-[#5B4AE6] text-white text-xs font-bold">Dashboard</a>
                </div>
            </div>
        </header>

        <!-- HERO MAIN CONTENT (2-COLUMN LAYOUT EXACT MATCH) -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 lg:pt-14 pb-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-8 items-center">
                
                <!-- Left Column: Typography, Headline, CTA & Stat Metrics -->
                <div class="lg:col-span-6 space-y-6">
                    <h1 class="text-4xl sm:text-5xl lg:text-[62px] font-extrabold tracking-tight text-[#0F172A] leading-[1.12]">
                        Let’s guide you to<br>
                        your <span class="text-[#5B4AE6]">dream home</span>
                    </h1>

                    <p class="text-sm sm:text-base text-slate-500 leading-relaxed max-w-lg">
                        Want to buy or rent a new home? Look no further, we got you covered. We have the perfect home that suits your lifestyle and need.
                    </p>

                    <!-- CTA Button -->
                    <div class="pt-2">
                        <a href="#properties" class="inline-flex items-center justify-center px-8 py-4 rounded-xl bg-[#5B4AE6] hover:bg-[#4A37D6] text-white text-sm font-bold shadow-glow-purple hover:-translate-y-0.5 transition-all duration-200">
                            Get Started
                        </a>
                    </div>

                    <!-- 2 Stat Metrics Counter -->
                    <div class="pt-8 flex items-center gap-12 sm:gap-16 border-t border-slate-200/60">
                        <div>
                            <div class="text-3xl sm:text-4xl font-extrabold text-[#0F172A] tracking-tight">
                                {{ max(1500, ($totalRoomsCount ?? 15) * 100) }}+
                            </div>
                            <div class="text-xs sm:text-sm font-medium text-slate-500 mt-1">
                                Listed Properties
                            </div>
                        </div>

                        <div>
                            <div class="text-3xl sm:text-4xl font-extrabold text-[#0F172A] tracking-tight">
                                {{ max(3800, (($activeCustomersCount ?? 5) * 400) + 3800) }}+
                            </div>
                            <div class="text-xs sm:text-sm font-medium text-slate-500 mt-1">
                                Happy Customers
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Architectural Modern Luxury Building Photo -->
                <div class="lg:col-span-6 flex justify-center lg:justify-end">
                    <div class="relative w-full max-w-xl group">
                        <div class="overflow-hidden rounded-3xl shadow-soft-float border-4 border-white bg-slate-100 aspect-[4/3]">
                            <img src="{{ asset('images/hero-property.jpg') }}" 
                                 alt="Modern Architectural Dream Residence" 
                                 class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-out">
                        </div>
                        
                        <!-- Floating Verified Badge -->
                        <div class="absolute -bottom-4 -left-4 bg-white/95 backdrop-blur-md px-4 py-3 rounded-2xl shadow-xl border border-slate-100 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-extrabold text-slate-900">100% Terverifikasi</p>
                                <p class="text-[11px] text-slate-500">Siap huni & fasilitas lengkap</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- FLOATING SEARCH BOX (Exact Match to Reference Image with Tabs) -->
        <div id="search-box" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            
            <!-- Tabs on top: [ Buy ] [ Rent ] -->
            <div class="flex items-center gap-0">
                <button type="button"
                        @click="activeTab = 'buy'; selectedType = 'all'"
                        :class="activeTab === 'buy' ? 'bg-[#5B4AE6] text-white shadow-md' : 'bg-white/80 hover:bg-white text-slate-700 border-t border-x border-slate-200'"
                        class="px-8 py-3 rounded-t-2xl font-bold text-sm transition-all">
                    Buy
                </button>
                <button type="button"
                        @click="activeTab = 'rent'; selectedType = 'all'"
                        :class="activeTab === 'rent' ? 'bg-[#5B4AE6] text-white shadow-md' : 'bg-white/80 hover:bg-white text-slate-700 border-t border-x border-slate-200'"
                        class="px-8 py-3 rounded-t-2xl font-bold text-sm transition-all">
                    Rent
                </button>
            </div>

            <!-- Main White Card Container -->
            <div class="bg-white rounded-2xl rounded-tl-none shadow-soft-float border border-slate-100 p-6 sm:p-8">
                <div class="mb-4">
                    <h3 class="text-sm font-bold text-slate-900 tracking-tight">Search available properties</h3>
                </div>

                <!-- 4 Column Inputs Row -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 sm:gap-4 items-center">
                    
                    <!-- Input 1: Address / Location / Search keyword -->
                    <div class="md:col-span-4 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#5B4AE6]">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        </div>
                        <input type="text"
                               x-model="searchQuery"
                               placeholder="Enter an address, city, zip"
                               class="w-full pl-10 pr-3 py-3 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#5B4AE6] focus:ring-2 focus:ring-[#5B4AE6]/10 transition-all">
                    </div>

                    <!-- Input 2: Property Type Dropdown -->
                    <div class="md:col-span-3 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#5B4AE6]">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="16" height="20" x="4" y="2" rx="2" ry="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M8 10h.01"/><path d="M16 10h.01"/><path d="M8 14h.01"/><path d="M16 14h.01"/></svg>
                        </div>
                        <select x-model="selectedType"
                                class="w-full pl-10 pr-8 py-3 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-700 bg-white focus:outline-none focus:border-[#5B4AE6] focus:ring-2 focus:ring-[#5B4AE6]/10 transition-all appearance-none cursor-pointer">
                            <option value="all">Enter Property Type</option>
                            <option value="kamar mandi dalam">Kost Kamar Mandi Dalam</option>
                            <option value="km luar">Kost Kamar Mandi Luar</option>
                            <option value="deluxe">Kamar Tipe Deluxe</option>
                            <option value="kontrakan">Unit Kontrakan</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                        </div>
                    </div>

                    <!-- Input 3: Budget Range -->
                    <div class="md:col-span-3 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#5B4AE6]">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/></svg>
                        </div>
                        <select x-model="selectedBudget"
                                class="w-full pl-10 pr-8 py-3 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-700 bg-white focus:outline-none focus:border-[#5B4AE6] focus:ring-2 focus:ring-[#5B4AE6]/10 transition-all appearance-none cursor-pointer">
                            <option value="all">Enter Your Budget</option>
                            <option value="under_750k">&lt; Rp 750.000 / bln</option>
                            <option value="750k_1500k">Rp 750.000 - Rp 1.500.000</option>
                            <option value="above_1500k">&gt; Rp 1.500.000 / bln</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                        </div>
                    </div>

                    <!-- Action 4: Search Button -->
                    <div class="md:col-span-2">
                        <a href="#properties" 
                           class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-[#5B4AE6] hover:bg-[#4A37D6] text-white text-xs sm:text-sm font-bold shadow-glow-purple hover:shadow-lg transition-all">
                            <span>Search</span>
                        </a>
                    </div>

                </div>
            </div>

        </div>

    </div>

    <!-- MAIN BODY SECTIONS -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-24">
        
        <!-- SECTION 1: PROPERTIES CATALOG -->
        <section id="properties" class="space-y-10">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-slate-100">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#5B4AE6]/10 text-[#5B4AE6] text-xs font-bold uppercase tracking-wider mb-2">
                        Katalog Properti & Kamar
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-[#0F172A] tracking-tight">
                        Daftar Hunian & Kamar Siap Huni
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        Pilihan unit bersih dan nyaman dengan tarif sewa bulanan transparan.
                    </p>
                </div>

                <!-- Quick Filter Pills -->
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" @click="selectedType = 'all'" 
                            :class="selectedType === 'all' ? 'bg-[#5B4AE6] text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'"
                            class="px-4 py-2 rounded-xl text-xs font-bold transition-all">
                        Semua Unit
                    </button>
                    <button type="button" @click="selectedType = 'kamar mandi dalam'" 
                            :class="selectedType === 'kamar mandi dalam' ? 'bg-[#5B4AE6] text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'"
                            class="px-4 py-2 rounded-xl text-xs font-bold transition-all">
                        KM Dalam
                    </button>
                    <button type="button" @click="selectedType = 'km luar'" 
                            :class="selectedType === 'km luar' ? 'bg-[#5B4AE6] text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'"
                            class="px-4 py-2 rounded-xl text-xs font-bold transition-all">
                        KM Luar
                    </button>
                </div>
            </div>

            <!-- Properties Grouped -->
            @forelse ($properties as $property)
                <div class="space-y-6">
                    
                    <!-- Property Residence Header Banner -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 rounded-2xl bg-gradient-to-r from-slate-50 to-[#F8F7FD] border border-slate-200/80">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#5B4AE6]/10 text-[#5B4AE6] flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-extrabold text-[#0F172A]">
                                    {{ $property->name }}
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                    {{ $property->address }}
                                </p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white border border-slate-200 text-xs font-bold text-[#5B4AE6] shadow-sm w-fit">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            {{ $property->rooms->count() }} Pilihan Unit
                        </span>
                    </div>

                    <!-- Room Cards Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                        @foreach ($property->rooms as $room)
                            @php
                                $facilitiesList = $room->facilities->pluck('name')->implode(' ');
                                $price = (float) $room->base_price;
                            @endphp
                            <article class="group rounded-2xl bg-white border border-slate-200/90 overflow-hidden flex flex-col justify-between transition-all duration-300 hover:shadow-card-hover hover:border-[#5B4AE6]/40"
                                     x-show="(selectedType === 'all' || '{{ strtolower($room->room_type) }}'.includes(selectedType)) && 
                                             (selectedBudget === 'all' || 
                                              (selectedBudget === 'under_750k' && {{ $price }} < 750000) ||
                                              (selectedBudget === '750k_1500k' && {{ $price }} >= 750000 && {{ $price }} <= 1500000) ||
                                              (selectedBudget === 'above_1500k' && {{ $price }} > 1500000)) &&
                                             ('{{ strtolower($room->room_type . ' ' . $room->room_number . ' ' . $property->name . ' ' . $property->address . ' ' . $facilitiesList) }}'.includes(searchQuery.toLowerCase()))">
                                
                                <div class="p-6 space-y-4">
                                    <!-- Header Card Badge & Room Number -->
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <span class="text-xs font-extrabold font-mono text-[#5B4AE6] block mb-1">
                                                KAMAR {{ $room->room_number }}
                                            </span>
                                            <h4 class="text-base font-bold text-slate-900 group-hover:text-[#5B4AE6] transition-colors leading-snug">
                                                {{ $room->room_type }}
                                            </h4>
                                        </div>
                                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 flex-shrink-0">
                                            TERSEDIA
                                        </span>
                                    </div>

                                    <!-- Room Dimensions -->
                                    <div class="flex items-center gap-2 text-xs text-slate-500">
                                        <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                                        <span>Luas Kamar: <strong class="text-slate-700 font-semibold">{{ $room->size ?: '16m²' }}</strong></span>
                                    </div>

                                    <!-- Facilities Pills -->
                                    <div>
                                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Fasilitas Termasuk:</span>
                                        <div class="flex flex-wrap gap-1.5 min-h-[50px]">
                                            @forelse ($room->facilities as $facility)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200/80 text-[11px] text-slate-700 font-medium">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-[#5B4AE6]"></span>
                                                    {{ $facility->name }}
                                                </span>
                                            @empty
                                                <span class="text-xs text-slate-400 italic">Fasilitas standar hunian</span>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Footer: Pricing & Detail Action -->
                                <div class="p-6 pt-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
                                    <div>
                                        <span class="text-[10px] text-slate-400 block uppercase font-bold tracking-wider">Tarif Sewa</span>
                                        <div class="flex items-baseline gap-1">
                                            <span class="text-lg font-extrabold text-[#0F172A] font-mono">
                                                Rp {{ number_format($room->base_price, 0, ',', '.') }}
                                            </span>
                                            <span class="text-xs text-slate-400 font-medium">/ bln</span>
                                        </div>
                                    </div>

                                    <!-- Detail Modal Trigger -->
                                    <button type="button"
                                            @click="selectedRoomModal = {
                                                number: '{{ $room->room_number }}',
                                                type: '{{ $room->room_type }}',
                                                price: 'Rp {{ number_format($room->base_price, 0, ',', '.') }}',
                                                size: '{{ $room->size ?: '16m²' }}',
                                                property: '{{ $property->name }}',
                                                address: '{{ $property->address }}',
                                                facilities: {{ json_encode($room->facilities->pluck('name')) }}
                                            }"
                                            class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-white hover:bg-[#5B4AE6] text-[#5B4AE6] hover:text-white border border-slate-200 hover:border-[#5B4AE6] text-xs font-bold shadow-sm transition-all duration-200">
                                        <span>Detail & Sewa</span>
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                                    </button>
                                </div>

                            </article>
                        @endforeach
                    </div>

                </div>
            @empty
                <div class="text-center py-16 p-8 rounded-3xl bg-slate-50 border border-slate-200/80">
                    <p class="text-slate-500 font-medium">Saat ini seluruh kamar sedang terisi. Hubungi pengelola untuk reservasi berikutnya.</p>
                </div>
            @endforelse
        </section>

        <!-- SECTION 2: ABOUT & PLATFORM ADVANTAGES -->
        <section id="about" class="py-12 border-t border-slate-100">
            <div class="text-center max-w-2xl mx-auto space-y-3 mb-14">
                <span class="text-xs font-extrabold uppercase tracking-wider text-[#5B4AE6] bg-[#5B4AE6]/10 px-3 py-1 rounded-full">
                    Tentang Layanan Kami
                </span>
                <h2 class="text-3xl font-extrabold text-[#0F172A] tracking-tight">
                    Pengalaman Tinggal Praktis & Modern
                </h2>
                <p class="text-slate-500 text-sm leading-relaxed">
                    Kami menggabungkan kenyamanan hunian fisik berkualitas dengan kemudahan pencatatan digital untuk seluruh penyewa.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Feature 1 -->
                <div class="p-6 rounded-2xl bg-white border border-slate-100 shadow-sm hover:shadow-soft-float transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-[#5B4AE6]/10 text-[#5B4AE6] flex items-center justify-center mb-5 font-bold">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-2">Kontrak Digital Rapi</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Perjanjian sewa terdata sistem, periode transparan, dan perpanjangan sewa tanpa ribet.</p>
                </div>

                <!-- Feature 2 -->
                <div class="p-6 rounded-2xl bg-white border border-slate-100 shadow-sm hover:shadow-soft-float transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center mb-5 font-bold">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-2">Pembayaran Mudah</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Pencatatan tagihan otomatis, bukti kuitansi digital, dan konfirmasi pembayaran realtime.</p>
                </div>

                <!-- Feature 3 -->
                <div class="p-6 rounded-2xl bg-white border border-slate-100 shadow-sm hover:shadow-soft-float transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-600 flex items-center justify-center mb-5 font-bold">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-2">Keamanan & Perawatan</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Akses lingkungan terjaga, CCTV aktif, dan pemeliharaan fasilitas bersama secara berkala.</p>
                </div>

                <!-- Feature 4 -->
                <div class="p-6 rounded-2xl bg-white border border-slate-100 shadow-sm hover:shadow-soft-float transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-cyan-500/10 text-cyan-600 flex items-center justify-center mb-5 font-bold">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-2">Respon Cepat Pengelola</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Komunikasi langsung dengan admin pengelola untuk bantuan teknis dan pertanyaan sewa.</p>
                </div>
            </div>
        </section>

        <!-- SECTION 3: CONTACT US -->
        <section id="contact" class="rounded-3xl bg-gradient-to-br from-[#0F172A] to-[#1E1B4B] text-white p-8 sm:p-14 shadow-2xl relative overflow-hidden">
            <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-[#5B4AE6]/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-8 space-y-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-purple-200 text-xs font-bold uppercase tracking-wider">
                        Hubungi Pengelola Langsung
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight">
                        Tertarik Survey atau Booking Kamar?
                    </h2>
                    <p class="text-slate-300 text-sm sm:text-base max-w-xl leading-relaxed">
                        Konsultasikan kebutuhan hunian Anda atau jadwalkan kunjungan langsung ke lokasi properti kami. Tim pengelola siap membantu Anda setiap hari.
                    </p>
                </div>

                <div class="lg:col-span-4 flex flex-col sm:flex-row lg:flex-col gap-3">
                    <a href="https://wa.me/6281234567890?text=Halo%20vHomes,%20saya%20tertarik%20untuk%20survey%20kamar%20kost"
                       target="_blank"
                       class="inline-flex items-center justify-center gap-2 px-6 py-4 rounded-xl bg-[#5B4AE6] hover:bg-[#4A37D6] text-white text-sm font-bold shadow-lg shadow-indigo-500/30 transition-all">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <span>Chat WhatsApp Pengelola</span>
                    </a>

                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold border border-white/20 transition-all">
                        <span>Portal Masuk Pengelola (Admin)</span>
                    </a>
                </div>
            </div>
        </section>

    </main>

    <!-- FOOTER -->
    <footer class="border-t border-slate-100 bg-[#F8F7FD] py-12 text-slate-500 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-2.5">
                <div class="flex items-end gap-1 h-5">
                    <span class="w-1 h-3 rounded-full bg-[#5B4AE6]"></span>
                    <span class="w-1 h-5 rounded-full bg-[#5B4AE6]"></span>
                    <span class="w-1 h-3.5 rounded-full bg-[#7C3AED]"></span>
                </div>
                <span class="text-base font-extrabold text-[#0F172A]">
                    v<span class="text-[#5B4AE6]">Homes</span>
                </span>
                <span class="text-slate-400">| Kost & Property Management</span>
            </div>

            <div class="flex items-center gap-6 font-semibold">
                <a href="#about" class="hover:text-[#5B4AE6] transition-colors">Tentang Kami</a>
                <a href="#properties" class="hover:text-[#5B4AE6] transition-colors">Katalog Unit</a>
                <a href="#contact" class="hover:text-[#5B4AE6] transition-colors">Kontak</a>
                <a href="{{ route('login') }}" class="text-[#5B4AE6] hover:underline">Masuk Admin</a>
            </div>

            <p class="text-slate-400">
                &copy; {{ date('Y') }} vHomes Kost System. All rights reserved.
            </p>
        </div>
    </footer>

    <!-- INTERACTIVE DETAIL & BOOKING MODAL -->
    <div x-show="selectedRoomModal" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         @keydown.escape.window="selectedRoomModal = null">
        
        <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl p-6 sm:p-8 space-y-6"
             @click.away="selectedRoomModal = null">
            
            <div class="flex items-start justify-between pb-4 border-b border-slate-100">
                <div>
                    <span class="text-xs font-mono font-bold text-[#5B4AE6]" x-text="'UNIT ' + selectedRoomModal?.number"></span>
                    <h3 class="text-xl font-extrabold text-slate-900 mt-0.5" x-text="selectedRoomModal?.type"></h3>
                    <p class="text-xs text-slate-500 mt-1" x-text="selectedRoomModal?.property + ' • ' + selectedRoomModal?.address"></p>
                </div>
                <button @click="selectedRoomModal = null" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center">
                    ✕
                </button>
            </div>

            <!-- Price & Size Highlights -->
            <div class="grid grid-cols-2 gap-4 p-4 rounded-2xl bg-[#F8F7FD]">
                <div>
                    <span class="text-[10px] text-slate-400 uppercase font-bold">Tarif Sewa</span>
                    <div class="text-lg font-extrabold text-[#5B4AE6]" x-text="selectedRoomModal?.price + ' / bln'"></div>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 uppercase font-bold">Ukuran Kamar</span>
                    <div class="text-base font-extrabold text-slate-800" x-text="selectedRoomModal?.size"></div>
                </div>
            </div>

            <!-- Facilities Checklist -->
            <div>
                <h4 class="text-xs font-bold uppercase text-slate-400 mb-3 tracking-wider">Fasilitas Unit</h4>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <template x-for="f in selectedRoomModal?.facilities" :key="f">
                        <div class="flex items-center gap-2 p-2 rounded-lg bg-slate-50 text-slate-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span x-text="f"></span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                <button type="button" @click="selectedRoomModal = null" class="w-1/3 py-3 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all">
                    Tutup
                </button>
                <a :href="'https://wa.me/6281234567890?text=' + encodeURIComponent('Halo pengelola vHomes, saya ingin survey/sewa Unit ' + selectedRoomModal?.number + ' (' + selectedRoomModal?.type + ') tarif ' + selectedRoomModal?.price + '/bulan.')"
                   target="_blank"
                   class="w-2/3 py-3 rounded-xl bg-[#5B4AE6] hover:bg-[#4A37D6] text-white text-xs font-bold text-center shadow-glow-purple transition-all">
                    Booking via WhatsApp →
                </a>
            </div>

        </div>
    </div>

</body>
</html>